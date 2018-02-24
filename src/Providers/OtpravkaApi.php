<?php
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\AddressList;
use LapayGroup\RussianPost\FioList;
use LapayGroup\RussianPost\PhoneList;
use LapayGroup\RussianPost\Singleton;
use LapayGroup\RussianPost\TariffInfo;
use LapayGroup\RussianPost\ParcelInfo;

class OtpravkaApi
{
    use Singleton;
    const VERSION = '1.0';

    function __construct($config)
    {
        $this->httpClient = new \GuzzleHttp\Client([
            'base_uri' => 'https://otpravka-api.pochta.ru',
            'headers' => ['Authorization' => 'AccessToken '. $config['auth']['otpravka']['token'],
                          'X-User-Authorization' => 'Basic '.$config['auth']['otpravka']['key'],
                          'Content-Type' => 'application/json',
                          'Accept' => 'application/json;charset=UTF-8'
            ]
        ]);
    }

    /**
     * Расчет стоимости пересылки
     * @param ParcelInfo $parcelInfo
     * @return TariffInfo
     */
    public function getDeliveryTariff(ParcelInfo $parcelInfo)
    {
        $response = $this->httpClient->request('POST', '/'.self::VERSION.'/tariff', [
            'json' => $parcelInfo->getArray(),
        ]);

        $rawResult = $this->parseResponse($response);
        return new TariffInfo($rawResult);
    }


    /**
     * Нормализация адресов
     * @param AddressList $addressList - адреса для нормализации
     * @return array ответ API ПРФ
     */
    public function clearAddress(AddressList $addressList)
    {
        $response = $this->httpClient->request('POST', '/'.self::VERSION.'/clean/address', [
            'json' => $addressList->get(),
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Нормализация ФИО
     * @param FioList $fioList
     * @return array ответ API ПРФ
     */
    public function clearFio(FioList $fioList)
    {
        $response = $this->httpClient->request('POST', '/'.self::VERSION.'/clean/physical', [
            'json' => $fioList->get(),
        ]);

        return $this->parseResponse($response);
    }

    /**
     * Нормализация телефона
     * @param PhoneList $phoneList
     * @return array ответ API ПРФ
     */
    public function clearPhone(PhoneList $phoneList)
    {
        $response = $this->httpClient->request('POST', '/'.self::VERSION.'/clean/phone', [
            'json' => $phoneList->get(),
        ]);

        return $this->parseResponse($response);
    }

    private function parseResponse($response)
    {
        $result = json_decode($response->getBody()->getContents(), true);
        return $result;
    }
}