<?php

namespace LapayGroup\RussianPost\Entity;

/**
 * Вложение отправления
 *
 * Class Item
 * @package LapayGroup\RussianPost\Entity
 */
class Item
{
    /** @var string|null  */
    private $code = null; // Код (маркировка) товара
    /** @var int|null  */
    private $lineattr = null; // Признак предмета расчета. См. Справочнк признаков предмета расчета
    /** @var int|null  */
    private $payattr  = null; // Признак способа расчета. См. Признаки способов расчета
    /** @var string  */
    private $goods_type = null; // Признак товар или услуга. См. Тип вложения
    /** @var int|null  */
    private $country_code = null; // Код страны происхождения. См. Список стран
    /** @var string|null  */
    private $customs_declaration_number = null; // Номер таможенной декларации
    /** @var int|null  */
    private $excise = null; // Акциз
    /** @var string  */
    private $description = ''; // Наименование товара
    /** @var int|null  */
    private $insr_value = null; // Объявленная ценность в копейках
    /** @var string|null  */
    private $item_number = null; // Номенклатура(артикул) товара
    /** @var int  */
    private $quantity = 1; // Количество товара
    /** @var string|null  */
    private $supplier_inn = null; // ИНН поставщика товара
    /** @var string|null  */
    private $supplier_name = null; // Наименование поставщика товара
    private $supplier_phone = null; // Телефон поставщика товара
    /** @var int  */
    private $value = 0; // Цена товара (вкл. НДС)
    /** @var int  */
    private $vat_rate = -1; // Ставка НДС: Без НДС(-1), 0, 10, 20
    /** @var int|null  */
    private $weight = null; // Вес товара (в граммах)


    /**
     * @return string|null
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string|null $code
     */
    public function setCode($code)
    {
        $this->code = $code;
    }

    /**
     * @return int|null
     */
    public function getLineAttr()
    {
        return $this->lineattr;
    }

    /**
     * @param int|null $lineattr
     */
    public function setLineAttr($lineattr)
    {
        $this->lineattr = $lineattr;
    }

    /**
     * @return int|null
     */
    public function getPayAttr()
    {
        return $this->payattr;
    }

    /**
     * @param int|null $payattr
     */
    public function setPayAttr($payattr)
    {
        $this->lineattr = $payattr;
    }

    /**
     * @return string|null
     */
    public function getGoodsType()
    {
        return $this->goods_type;
    }

    /**
     * @param string|null $goods_type
     */
    public function setGoodsType($goods_type)
    {
        $this->lineattr = $goods_type;
    }

    /**
     * @return int|null
     */
    public function getСountryCode()
    {
        return $this->country_code;
    }

    /**
     * @param int|null $country_code
     */
    public function setСountryCode($country_code)
    {
        $this->code = $country_code;
    }

    /**
     * @return string|null
     */
    public function getСustomsDeclarationNumber()
    {
        return $this->customs_declaration_number;
    }
    /**
     * @param string|null $customs_declaration_number
     */
    public function setСustomsDeclarationNumber($customs_declaration_number)
    {
        $this->code = $customs_declaration_number;
    }
    /**
     * @return int|null
     */
    public function getExcise()
    {
        return $this->excise;
    }
    /**
     * @param int|null $excise
     */
    public function setExcise($excise)
    {
        $this->code = $excise;
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
     * @return int|null
     */
    public function getInsrValue()
    {
        return $this->insr_value;
    }

    /**
     * @param int|null $insr_value
     */
    public function setInsrValue($insr_value)
    {
        $this->insr_value = $insr_value;
    }

    /**
     * @return string|null
     */
    public function getItemNumber()
    {
        return $this->item_number;
    }

    /**
     * @param string|null $item_number
     */
    public function setItemNumber($item_number)
    {
        $this->item_number = $item_number;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * @return string|null
     */
    public function getSupplierInn()
    {
        return $this->supplier_inn;
    }

    /**
     * @param string|null $supplier_inn
     */
    public function setSupplierInn($supplier_inn)
    {
        $this->supplier_inn = $supplier_inn;
    }

    /**
     * @return string|null
     */
    public function getSupplierName()
    {
        return $this->supplier_name;
    }

    /**
     * @param string|null $supplier_name
     */
    public function setSupplierName($supplier_name)
    {
        $this->supplier_name = $supplier_name;
    }

    /**
     * @return null
     */
    public function getSupplierPhone()
    {
        return $this->supplier_phone;
    }

    /**
     * @param null $supplier_phone
     */
    public function setSupplierPhone($supplier_phone)
    {
        $this->supplier_phone = $supplier_phone;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getVatRate()
    {
        return $this->vat_rate;
    }

    /**
     * @param int $vat_rate
     */
    public function setVatRate($vat_rate)
    {
        $this->vat_rate = $vat_rate;
    }

    /**
     * @return int|null
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int|null $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }
}
