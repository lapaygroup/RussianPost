<?php
namespace LapayGroup\RussianPost;

class CalculateInfo
{
    private $version='1.21.2.479'; // Версия тарификатора
    private $categoryItemId = 0; // ID категории
    private $categoryItemName = 0; // Название категории
    private $weight = 0.00; // Вес отправления в граммах
    private $transportationID = 0; // ID способа пересылки
    private $transportationName = 'Наземно'; // Способ пересылки
    private $pay = 0.00; // Итого стоимоть без НДС
    private $payNds = 0.00; // Итого стоимость с НДС
    private $payMark = 0.00; // Итого стоимость при оплате марками
    private $ground = 0.00; // Почтовый сбор
    private $groundNds = 0.00; // Почтовый сбор c НДС
    private $cover = 0.00; // Страхование
    private $coverNds = 0.00; // Страхование с НДС
    private $service = 0.00; // Дополнительные услуги
    private $serviceNds = 0.00; // Дополнительные услуги с НДС
    private $deliveryPeriodMin = null; // Минимальный период доставки
    private $deliveryPeriodMax = null; // Максимальный период доставки
    private $deliveryDeadLine = null; // Срок внутренней доставки с учетом расписания работы и обмена
    private $tariffList = []; // Список тарифов

    /**
     * @return string
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param string $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return int
     */
    public function getCategoryItemId()
    {
        return $this->categoryItemId;
    }

    /**
     * @param int $categoryItemId
     */
    public function setCategoryItemId($categoryItemId)
    {
        $this->categoryItemId = $categoryItemId;
    }

    /**
     * @return int
     */
    public function getCategoryItemName()
    {
        return $this->categoryItemName;
    }

    /**
     * @param int $categoryItemName
     */
    public function setCategoryItemName($categoryItemName)
    {
        $this->categoryItemName = $categoryItemName;
    }

    /**
     * @return float
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int
     */
    public function getTransportationID()
    {
        return $this->transportationID;
    }

    /**
     * @param int $transportationID
     */
    public function setTransportationID($transportationID)
    {
        $this->transportationID = $transportationID;
    }

    /**
     * @return string
     */
    public function getTransportationName()
    {
        return $this->transportationName;
    }

    /**
     * @param string $transportationName
     */
    public function setTransportationName($transportationName)
    {
        $this->transportationName = self::mb_ucfirst($transportationName);
    }

    /**
     * @return float
     */
    public function getPay()
    {
        return $this->pay;
    }

    /**
     * @param int $pay
     */
    public function setPay($pay)
    {
        $this->pay =  number_format($pay / 100, 2, '.', '');
    }

    /**
     * @return float
     */
    public function getPayNds()
    {
        return $this->payNds;
    }

    /**
     * @param int $payNds
     */
    public function setPayNds($payNds)
    {
        $this->payNds = number_format($payNds / 100, 2, '.', '');
    }

    /**
     * @return array
     */
    public function getTariffList()
    {
        return $this->tariffList;
    }

    /**
     * @param Tariff $tariff
     */
    public function addTariff(Tariff $tariff)
    {
        $this->tariffList[] = $tariff;
    }

    /**
     * @return float
     */
    public function getGround()
    {
        return $this->ground;
    }

    /**
     * @param float $ground
     */
    public function setGround($ground)
    {
        $this->ground = number_format($ground / 100, 2, '.', '');
    }

    /**
     * @return float
     */
    public function getGroundNds()
    {
        return $this->groundNds;
    }

    /**
     * @param float $groundNds
     */
    public function setGroundNds($groundNds)
    {
        $this->groundNds = number_format($groundNds / 100, 2, '.', '');
    }

    /**
     * @return float
     */
    public function getCover()
    {
        return $this->cover;
    }

    /**
     * @param float $cover
     */
    public function setCover($cover)
    {
        $this->cover = number_format($cover / 100, 2, '.', '');
    }

    /**
     * @return float
     */
    public function getCoverNds()
    {
        return $this->coverNds;
    }

    /**
     * @param float $coverNds
     */
    public function setCoverNds($coverNds)
    {
        $this->coverNds = number_format($coverNds / 100, 2, '.', '');
    }

    /**
     * @return float
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param float $service
     */
    public function setService($service)
    {
        $this->service = number_format($service / 100, 2, '.', '');
    }

    /**
     * @return float
     */
    public function getServiceNds()
    {
        return $this->serviceNds;
    }

    /**
     * @param float $serviceNds
     */
    public function setServiceNds($serviceNds)
    {
        $this->serviceNds = number_format($serviceNds / 100, 2, '.', '');
    }

    /**
     * @return null
     */
    public function getDeliveryPeriodMin()
    {
        return $this->deliveryPeriodMin;
    }

    /**
     * @param null $deliveryPeriodMin
     */
    public function setDeliveryPeriodMin($deliveryPeriodMin)
    {
        $this->deliveryPeriodMin = $deliveryPeriodMin;
    }

    /**
     * @return null
     */
    public function getDeliveryPeriodMax()
    {
        return $this->deliveryPeriodMax;
    }

    /**
     * @param null $deliveryPeriodMax
     */
    public function setDeliveryPeriodMax($deliveryPeriodMax)
    {
        $this->deliveryPeriodMax = $deliveryPeriodMax;
    }

    /**
     * @return null
     */
    public function getDeliveryDeadLine()
    {
        return $this->deliveryDeadLine;
    }

    /**
     * @param null $deliveryDeadLine
     */
    public function setDeliveryDeadLine($deliveryDeadLine)
    {
        $this->deliveryDeadLine = $deliveryDeadLine;
    }

    public static function mb_ucfirst($string)
    {
        return mb_strtoupper(mb_substr($string, 0, 1)).mb_strtolower(mb_substr($string, 1));
    }
}
