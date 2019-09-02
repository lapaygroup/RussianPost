<?php
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\AddressList;
use LapayGroup\RussianPost\Entity\Order;
use LapayGroup\RussianPost\Entity\Recipient;
use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\FioList;
use LapayGroup\RussianPost\PhoneList;
use LapayGroup\RussianPost\TariffInfo;
use LapayGroup\RussianPost\ParcelInfo;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class OtpravkaApi implements LoggerAwareInterface
{
    use LoggerAwareTrait;

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
                if ($this->logger) {
                    $this->logger->info('Russian Post Otpravka API request: '.http_build_query($params));
                }
                $response = $client->get($method, ['query' => $params]);
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                if ($this->logger) {
                    $this->logger->info('Russian Post Otpravka API request: '.json_encode($params));
                }

                $response = $client->{strtolower($type)}($method, ['json' => $params]);

                break;
        }

        $response_contents = $response->getBody()->getContents();

        if ($this->logger) {
            $this->logger->info('Russian Post Otpravka API response: '.$response_contents);
        }

        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 404 && $response->getStatusCode() != 400)
            throw new RussianPostException('Неверный код ответа от сервера Почты России при вызове метода '.$method.': ' . $response->getStatusCode(), $response->getStatusCode(), $response_contents);

        $resp = json_decode($response_contents, true);

        if (empty($resp) && $endpoint == 'delivery' && json_last_error() == JSON_ERROR_SYNTAX) {
            return $response_contents;
        }

        if (empty($resp))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' пришел пустой ответ', $response->getStatusCode(), $response_contents);

        if ($response->getStatusCode() == 404 && !empty($resp['code']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['sub-code'] . " (".$resp['code'].")", $response->getStatusCode(), $response_contents);

        if ($response->getStatusCode() == 400 && !empty($resp['error']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['error'] . " (".$resp['status'].")", $response->getStatusCode(), $response_contents);

        return $resp;
    }

    /**
     * Расчет стоимости пересылки
     *
     * @param ParcelInfo $parcelInfo
     * @return TariffInfo
     * @throws RussianPostException
     */
    public function getDeliveryTariff($parcelInfo)
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
    public function clearAddress($addressList)
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
    public function clearFio($fioList)
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
        $result = $this->callApi('POST', 'unreliable-recipient', $params);
        return array_shift($result);
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

    /**
     * Создание заказов
     *
     * @param array $order - массив объектов Order
     * @return array
     * @throws RussianPostException
     */
    public function createOrder($orders)
    {
        return $this->callApi('PUT', 'user/backlog', $orders);
    }

    /**
     * Поиск заказа по идентификатору
     *
     * @param int $id - id заказа Почты России
     * @return array
     * @throws RussianPostException
     */
    public function findOrderById($id)
    {
        return $this->callApi('GET', 'backlog/'.$id);
    }

    /**
     * Поиска заказа по назначенному магазином идентификатору
     *
     * @param string $order_num - номер заказа магазина
     * @return array
     * @throws RussianPostException
     */
    public function findOrderByShopId($order_num)
    {
        return $this->callApi('GET', 'backlog/search', ['query' => $order_num]);
    }

    /**
     * Удаление заказов
     *
     * @param $id_list - массив id заказов
     * @return array
     * @throws RussianPostException
     */
    public function deleteOrders($id_list)
    {
        return $this->callApi('DELETE', 'backlog', $id_list);
    }
}