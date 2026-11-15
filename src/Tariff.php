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
        $this->value = number_format($value / 100, 2, '.', ''); // Стоимость без НДС
        $this->valueNds = number_format($valueNds / 100, 2, '.', ''); // Стоимость с НДС
        $this->valueMark = number_format($valueMark / 100, 2, '.', ''); // Марки
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
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return float
     */
    public function getValueNds()
    {
        return $this->valueNds;
    }

	/**
	 * @return float
	 */
	public function getValueMark() {
		return $this->valueMark;
	}
}
