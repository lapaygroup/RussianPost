<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost;

class Tariff
{
    private int $id;
    private string $name;
    private int $value;
    private int $valueNds;
    private int $valueMark;

    public function __construct(
        int $id,
        string $name,
        int|float|string $value,
        int|float|string $valueNds,
        int|float|string $valueMark
    )
    {
        $this->id = $id; // ID тарифа
        $this->name = $name; // Название тарифа
        $this->value = (int) round((float) $value); // Стоимость без НДС в копейках
        $this->valueNds = (int) round((float) $valueNds); // Стоимость с НДС в копейках
        $this->valueMark = (int) round((float) $valueMark); // Марки в копейках
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Стоимость без НДС в рублях.
     */
    public function getValue(): float
    {
        return $this->value / 100;
    }

    public function getValueKopecks(): int
    {
        return $this->value;
    }

    /**
     * Стоимость с НДС в рублях.
     */
    public function getValueNds(): float
    {
        return $this->valueNds / 100;
    }

    public function getValueNdsKopecks(): int
    {
        return $this->valueNds;
    }

	/**
	 * Стоимость при оплате марками в рублях.
	 */
	public function getValueMark(): float {
		return $this->valueMark / 100;
	}

    public function getValueMarkKopecks(): int
    {
        return $this->valueMark;
    }
}
