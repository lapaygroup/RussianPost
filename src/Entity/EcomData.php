<?php
namespace LapayGroup\RussianPost\Entity;

/**
 * Данные отправления ЕКОМ
 *
 * Class EcomData
 * @package LapayGroup\RussianPost\Entity
 */
Class EcomData
{
    /** @var string|null  */
    private $delivery_point_index = null; // Идентификатор пункта выдачи заказов
    /** @var int  */
    private $delivery_rate = 0; // Стоимость доставки для получателя (вкл. НДС) в копейках
    /** @var int  */
    private $delivery_vat_rate = -1; // Ставка НДС: Без НДС(-1), 0, 10, 20
    /** @var array|null  */
    private $services = null; // Сервисы ЕКОМ https://otpravka.pochta.ru/specification#/enums-ecom-services

    /**
     * @return string|null
     */
    public function getDeliveryPointIndex()
    {
        return $this->delivery_point_index;
    }

    /**
     * @param string|null $delivery_point_index
     */
    public function setDeliveryPointIndex($delivery_point_index)
    {
        $this->delivery_point_index = $delivery_point_index;
    }

    /**
     * @return int
     */
    public function getDeliveryRate()
    {
        return $this->delivery_rate;
    }

    /**
     * @param int $delivery_rate
     */
    public function setDeliveryRate($delivery_rate)
    {
        $this->delivery_rate = $delivery_rate;
    }

    /**
     * @return int
     */
    public function getDeliveryVatRate()
    {
        return $this->delivery_vat_rate;
    }

    /**
     * @param int $delivery_vat_rate
     */
    public function setDeliveryVatRate($delivery_vat_rate)
    {
        $this->delivery_vat_rate = $delivery_vat_rate;
    }

    /**
     * @return array|null
     */
    public function getServices()
    {
        return $this->services;
    }

    /**
     * @param array|null $services
     */
    public function setServices($services)
    {
        $this->services = $services;
    }
}