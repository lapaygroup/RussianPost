<?php
namespace LapayGroup\RussianPost\Entity;

Class CustomsDeclarationItem
{
    /** @var int  */
    private $amount = 0; // Количество
    /** @var int  */
    private $country_code = 643; // Код страны https://otpravka.pochta.ru/specification#/dictionary-countries
    /** @var string  */
    private $description = ''; // Наименование товара
    private $tnved_code = ''; // Код ТНВЭД
    private $trademark = ''; //Торговая марка
    private $weight = 0; // Вес вложения в граммах
    /** @var int|null  */
    private $value = null; // Цена товара (вкл. НДС) в копейках

    /**
     * @return int
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param int $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

    /**
     * @return int
     */
    public function getCountryCode()
    {
        return $this->country_code;
    }

    /**
     * @param int $country_code
     */
    public function setCountryCode($country_code)
    {
        $this->country_code = $country_code;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getTnvedCode()
    {
        return $this->tnved_code;
    }

    /**
     * @param string $tnved_code
     */
    public function setTnvedCode($tnved_code)
    {
        $this->tnved_code = $tnved_code;
    }

    /**
     * @return int
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return int|null
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int|null $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getTrademark()
    {
        return $this->trademark;
    }

    /**
     * @param string $trademark
     */
    public function setTrademark($trademark)
    {
        $this->trademark = $trademark;
    }


}