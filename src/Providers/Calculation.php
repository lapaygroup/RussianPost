<?php
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\Exceptions\RussianPostException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Calculation implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $httpClient;
    const VERSION = 'v2';

    function __construct($timeout = 60)
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => 'https://delivery.pochta.ru/' . self::VERSION . '/',
            'timeout' => $timeout,
            'http_errors' => false
        ]);
    }

    /**
     * Инициализирует вызов к API
     *
     * @param $method
     * @param $params
     * @return array
     * @throws RussianPostException
     */
    private function callApi($type, $method, $params = [])
    {
        $params['json'] = true; // указываем, что ждем ответ в JSON

        switch ($type) {
            case 'GET':
                $request = http_build_query($params);
                if ($this->logger) {
                     $this->logger->info("Russian Post Tariff API {$type} request /" . self::VERSION . "/{$method}: " . $request);
                }
                $response = $this->httpClient->get($method, ['query' => $params]);
                break;
            case 'POST':
                $request = json_encode($params);
                if ($this->logger) {
                    $this->logger->info("Russian Post Tariff API {$type} request /" . self::VERSION . "/{$method}: " . $request);
                }
                $response = $this->httpClient->post($method, ['json' => $params]);
                break;
        }

        if (!in_array($response->getStatusCode(), [200, 400, 404]))
            throw new RussianPostException('Неверный код ответа от сервера Почты России при вызове метода ' . $method . ': ' . $response->getStatusCode(), $response->getStatusCode(), $response->getBody()->getContents(), $request);

        $json = $response->getBody()->getContents();

        if ($this->logger) {
            $headers = $response->getHeaders();
            $headers['http_status'] = $response->getStatusCode();
            $this->logger->info("Russian Post Tariff API {$type} response /" . self::VERSION . "/{$method}: " . $json, $headers);
        }

        $resp = json_decode($json, true);

        if (empty($resp))
            throw new RussianPostException('От сервера Почты России при вызове метода ' . $method . ' пришел пустой ответ', $response->getStatusCode(), $response->getBody()->getContents(), $request);

        if ($response->getStatusCode() == 404 && !empty($resp['code']))
            throw new RussianPostException('От сервера Почты России при вызове метода ' . $method . ' получена ошибка: ' . $resp['sub-code'] . " (" . $resp['code'] . ")", $response->getStatusCode(), $response->getBody()->getContents(), $request);

        if ($response->getStatusCode() == 400 && !empty($resp['error']))
            throw new RussianPostException('От сервера Почты России при вызове метода ' . $method . ' получена ошибка: ' . $resp['error'] . " (" . $resp['status'] . ")", $response->getStatusCode(), $response->getBody()->getContents(), $request);

        return $resp;
    }

    /**
     * Отправка запрос на расчет тарифа к API тарификатора V2
     *
     * @param $method
     * @param $object_id
     * @param $params
     * @param $services
     * @return array
     * @throws RussianPostException
     */
    private function tariffRequest($method, $object_id, $params, $services)
    {
        $params['object'] = $object_id;
        if (!empty($services))
            $params['service'] = implode(',', $services);

        return $this->callApi('GET', $method, $params);
    }

    /**
     * Получение списка категорий
     *
     * @return mixed
     * @throws RussianPostException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCategoryList()
    {
        return $this->callApi('GET', 'dictionary/tariff/delivery', ['category' => 'all']);
    }

    /**
     * Описание категории
     *
     * @param $category_id
     * @return mixed
     * @throws RussianPostException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCategoryDescription($category_id)
    {
        return $this->callApi('GET', 'dictionary/tariff/delivery', ['category' => $category_id]);
    }

    /**
     * Расчет тарифа
     *
     * @param $object_id
     * @param $params
     * @param $services
     * @return mixed
     * @throws RussianPostException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTariff($object_id, $params, $services)
    {
        return $this->tariffRequest('calculate/tariff', $object_id, $params, $services);
    }

    /**
     * Расчет тарифа и сроков доставки
     *
     * @param $object_id
     * @param $params
     * @param $services
     * @return mixed
     * @throws RussianPostException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTariffAndDeliveryPeriod($object_id, $params, $services)
    {
        return $this->tariffRequest('calculate/tariff/delivery', $object_id, $params, $services);
    }

    /**
     * Описание объекта
     *
     * @param $object_id
     * @return mixed
     * @throws RussianPostException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getObjectInfo($object_id)
    {
        return $this->callApi('GET', 'dictionary/tariff/delivery', ['object' => $object_id]);
    }

    /**
     * Список стран
     *
     * @return array
     * @throws RussianPostException
     */
    public function getCountryList()
    {
        $result = $this->callApi('GET', 'dictionary/tariff/delivery', ['country' => false]);
        return !empty($result['country']) ? $result['country'] : [];
    }
}
