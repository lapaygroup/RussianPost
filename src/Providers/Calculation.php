<?php
namespace LapayGroup\RussianPost\Providers;

use GuzzleHttp\Psr7\Response;
use LapayGroup\RussianPost\Singleton;

class Calculation
{
    use Singleton;

    private $httpClient;

    function __construct()
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri'=>'https://tariff.pochta.ru/tariff/v1/'
        ]);
    }

    /**
     * Получение списка категорий
     *
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCategoryList()
    {
        $response = $this->httpClient->request('GET', 'dictionary', [
                         'query' => [
                                     'jsontext' => true,
                                     'category' => 'all'
                                    ]
                         ]);
        return $this->parseResponse($response);
    }

    /**
     * Описание категории
     *
     * @param $category_id
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCategoryDescription($category_id)
    {
        $response = $this->httpClient->request('GET', 'dictionary', [
            'query' => [
                'jsontext' => true,
                'category' => $category_id
            ]
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Расчет тарифа
     *
     * @param $object_id
     * @param $params
     * @param $services
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getTariff($object_id, $params, $services)
    {
        $params['object'] = $object_id;
        $params['jsontext'] = true;
        if(!empty($services))
            $params['service'] = implode(',', $services);

        $response = $this->httpClient->request('GET', 'calculate', [
            'query' => $params
        ]);
        return $this->parseResponse($response);
    }

    /**
     * Описание объекта
     *
     * @param $object_id
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getObjectInfo($object_id)
    {
        $response = $this->httpClient->request('GET', 'dictionary', [
            'query' => [
                'jsontext' => true,
                'object' => $object_id
            ]
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Обработка ответа
     *
     * @param Response $response
     * @return mixed
     */
    private function parseResponse($response)
    {
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;
    }
}