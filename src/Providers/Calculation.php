<?php
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\Exceptions\RussianPostException;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Calculation implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private $httpClient;
    const VERSION = 'v1';

    function __construct($timeout = 60)
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri'=>'https://tariff.pochta.ru/tariff/'.self::VERSION.'/',
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
        switch ($type) {
            case 'GET':
                if ($this->logger) {
                    $this->logger->info('Russian Post Tariff API request: '.http_build_query($params));
                }
                $response = $this->httpClient->get($method, ['query' => $params]);
                break;
            case 'POST':
                if ($this->logger) {
                    $this->logger->info('Russian Post Tariff API request: '.json_encode($params));
                }
                $response = $this->httpClient->post($method, ['json' => $params]);
                break;
        }

        if ($response->getStatusCode() != 200 && $response->getStatusCode() != 404 && $response->getStatusCode() != 400)
            throw new RussianPostException('Неверный код ответа от сервера Почты России при вызове метода '.$method.': ' . $response->getStatusCode(), $response->getStatusCode(), $response->getBody()->getContents());

        $json = $response->getBody()->getContents();

        if ($this->logger) {
            $this->logger->info('Russian Post Tariff API response: '.$json);
        }

        $resp = json_decode($json, true);

        if (empty($resp))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' пришел пустой ответ', $response->getStatusCode(), $response->getBody()->getContents());

        if ($response->getStatusCode() == 404 && !empty($resp['code']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['sub-code'] . " (".$resp['code'].")", $response->getStatusCode(), $response->getBody()->getContents());

        if ($response->getStatusCode() == 400 && !empty($resp['error']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['error'] . " (".$resp['status'].")", $response->getStatusCode(), $response->getBody()->getContents());

        return $resp;
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
        $params = [
            'jsontext' => true,
            'category' => 'all'
        ];

        return $this->callApi('GET', 'dictionary', $params);
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
        $params = [
            'jsontext' => true,
            'category' => $category_id
        ];

        return $this->callApi('GET', 'dictionary', $params);
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
        $params['object'] = $object_id;
        $params['jsontext'] = true;
        if(!empty($services))
            $params['service'] = implode(',', $services);

        return $this->callApi('GET', 'calculate', $params);
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
        $params = [
            'jsontext' => true,
            'object' => $object_id
        ];

        return $this->callApi('GET', 'dictionary', $params);
    }

    /**
     * Список стран
     *
     * @return array
     * @throws RussianPostException
     */
    public function getCountryList()
    {
        $params = [
            'json' => true,
            'country' => false
        ];

        $result = $this->callApi('GET', 'dictionary', $params);

        return !empty($result['country']) ? $result['country'] : [];
    }
}