<?php
namespace LapayGroup\RussianPost;

class Tariff
{
    private $id = 0;
    private $name = '';
    private $value = 0;
    private $valueNds = 0;
    private $valueMark = 0;

    function __construct($id, $name, $value, $valueNds, $valueMark)
    {
        $this->id = $id; // ID тарифа
        $this->name = $name; // Название тарифа
        $this->value = $value / 100; // Стоимость без НДС
        $this->valueNds = $valueNds / 100; // Стоимость с НДС
        $this->valueMark = $valueMark / 100; // Стоимость при оплате марками
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return int
     */
    public function getValueNds()
    {
        return $this->valueNds;
    }

    /**
     * @return int
     */
    public function getValueMark()
    {
        return $this->valueMark;
    }
}