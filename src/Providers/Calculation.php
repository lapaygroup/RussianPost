<?php
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\Singleton;

class Calculation
{
    use Singleton;

    function __construct()
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri'=>'http://tariff.russianpost.ru/tariff/v1/'
        ]);
    }

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

    private function parseResponse($response)
    {
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;
    }
}