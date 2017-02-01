<?php
namespace LapayGroup\RussianPost;

class CalculateInfo
{
    private $version='1.6.4.206'; // Версия тарификатора
    private $categoryItemId = 0; // ID категории
    private $categoryItemName = 0; // Название категории
    private $weight = 0.00; // Вес отправления в граммах
    private $transportationID = 0; // ID способа пересылки
    private $transportationName = 'Наземно'; // Способ пересылки
    private $pay = 0.00; // Итого стоимоть без НДС
    private $payNds = 0.00; // Итого стоимость с НДС
    private $payMark = 0.00; // Итог остоимость при оплате марками
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
        $this->transportationName = mb_ucfirst($transportationName);
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
     * @return float
     */
    public function getPayMark()
    {
        return $this->payMark;
    }

    /**
     * @param int $payMark
     */
    public function setPayMark($payMark)
    {
        $this->payMark = number_format($payMark / 100, 2, '.', '');
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
}