<?php
namespace LapayGroup\RussianPost\Providers;

use GuzzleHttp\Client;
use LapayGroup\RussianPost\AddressList;
use LapayGroup\RussianPost\Entity\Recipient;
use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\FioList;
use LapayGroup\RussianPost\PhoneList;
use LapayGroup\RussianPost\Singleton;
use LapayGroup\RussianPost\TariffInfo;
use LapayGroup\RussianPost\ParcelInfo;

class OtpravkaApi
{
    use Singleton;
    const VERSION = '1.0';
    const DELIVERY_VERSION = 'v1';

    /** @var \GuzzleHttp\Client  */
    private $otpravkaClient;

    /** @var \GuzzleHttp\Client  */
    private $deliveryClient;

    function __construct($config, $timeout = 60)
    {
        $this->otpravkaClient = new \GuzzleHttp\Client([
            'base_uri' => 'https://otpravka-api.pochta.ru/'.self::VERSION.'/',
            'headers' => ['Authorization' => 'AccessToken '. $config['auth']['otpravka']['token'],
                          'X-User-Authorization' => 'Basic '.$config['auth']['otpravka']['key'],
                          'Content-Type' => 'application/json',
                          'Accept' => 'application/json;charset=UTF-8'
            ],
            'timeout' => $timeout,
            'http_errors' => false
        ]);

        $this->deliveryClient = new \GuzzleHttp\Client([
            'base_uri'=>'https://delivery.pochta.ru/delivery/'.self::DELIVERY_VERSION.'/',
            'timeout' => $timeout,
            'http_errors' => false
        ]);
    }

    /**
     * Инициализирует вызов к API
     *
     * @param $method
     * @param $params
     * @return array | string
     * @throws RussianPostException
     */
    private function callApi($type, $method, $params = [], $endpoint = false)
    {

        switch ($endpoint) {
            case 'otpravka':
                $client = $this->otpravkaClient;
                break;

            case 'delivery':
                $client = $this->deliveryClient;
                break;

            default:
                $client = $this->otpravkaClient;
        }

        switch ($type) {
            case 'GET':
                $response = $client->get($method, ['query' => $params]);
                break;
            case 'POST':
                $response = $client->post($method, ['json' => $params]);
                break;
        }

        $response_contents = $response->getBody()->getContents();

        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 404 && $response->getStatusCode() != 400)
            throw new RussianPostException('Неверный код ответа от сервера Почты России при вызове метода '.$method.': ' . $response->getStatusCode(), $response->getStatusCode(), $response->getBody()->getContents());

        $resp = json_decode($response_contents, true);

        if (empty($resp) && $endpoint == 'delivery' && json_last_error() == JSON_ERROR_SYNTAX) {
            return $response_contents;
        }

        if (empty($resp))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' пришел пустой ответ', $response->getStatusCode(), $response->getBody()->getContents());

        if ($response->getStatusCode() == 404 && !empty($resp['code']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['sub-code'] . " (".$resp['code'].")", $response->getStatusCode(), $response->getBody()->getContents());

        if ($response->getStatusCode() == 400 && !empty($resp['error']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['error'] . " (".$resp['status'].")", $response->getStatusCode(), $response->getBody()->getContents());

        return $resp;
    }

    /**
     * Расчет стоимости пересылки
     *
     * @param ParcelInfo $parcelInfo
     * @return TariffInfo
     * @throws RussianPostException
     */
    public function getDeliveryTariff(ParcelInfo $parcelInfo)
    {
        $response = $this->callApi('POST', 'tariff', $parcelInfo->getArray());
        return new TariffInfo($response);
    }

    /**
     * Нормализация адресов
     *
     * @param AddressList $addressList - адреса для нормализации
     * @return array ответ API ПРФ
     * @throws RussianPostException
     */
    public function clearAddress(AddressList $addressList)
    {
        return $this->callApi('POST', 'clean/address',  $addressList->get());
    }

    /**
     * Нормализация ФИО
     *
     * @param FioList $fioList
     * @return array ответ API ПРФ
     * @throws RussianPostException
     */
    public function clearFio(FioList $fioList)
    {
        return $this->callApi('POST', 'clean/physical',  $fioList->get());
    }

    /**
     * Нормализация телефона
     *
     * @param PhoneList $phoneList
     * @return array ответ API ПРФ
     * @throws RussianPostException
     */
    public function clearPhone(PhoneList $phoneList)
    {
        return $this->callApi('POST', 'clean/phone',  $phoneList->get());
    }

    /**
     * Текущие точки сдачи отправлений
     *
     * @return mixed
     * @throws RussianPostException
     */
    public function shippingPoints()
    {
        return $this->callApi('GET', 'user-shipping-points');
    }

    /**
     * Получение баланса
     *
     * @return mixed
     * @throws RussianPostException
     */
    public function getBalance()
    {
        return $this->callApi('GET', 'counterpart/balance');
    }

    /**
     * Расчет периода доставки
     *
     * @param $post_type
     * @param $index_prom
     * @param $index_to
     * @return array
     * @throws RussianPostException
     */
    public function getDeliveryPeriod($post_type, $index_from, $index_to, $as_html = false)
    {
        $params = [];
        if (!$as_html)
            $params['jsontext'] = true;
        else
            $params['html'] = true;

        $params['posttype'] = $post_type;
        $params['from'] = $index_from;
        $params['to'] = $index_to;

        return $this->callApi('GET', 'calculate', $params, 'delivery');
    }

    /**
     * Взвращает информацию о благонадежности получателя
     *
     * @param Recipient $recipient
     * @return array
     * @throws RussianPostException
     */
    public function untrustworthyRecipient($recipient)
    {
        $params[] = $recipient->getParams();
        return $this->callApi('POST', 'unreliable-recipient', $params);
    }


    /**
     * Взвращает информацию о благонадежности списка получателей
     *
     * @param array $recipients - массив объектов Recipient
     * @return array
     * @throws RussianPostException
     */
    public function untrustworthyRecipients($recipients)
    {
        $params = [];
        foreach ($recipients as $recipient) {
            $params[] = $recipient->getParams();
        }

        return $this->callApi('POST', 'unreliable-recipient', $params);
    }
}