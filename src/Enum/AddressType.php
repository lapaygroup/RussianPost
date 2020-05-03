<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class AddressType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-address-type (Данные из спецификации)
 */
class AddressType
{
    public const DEFAULT = 'DEFAULT'; // Стандартный (улица, дом, квартира)
    public const PO_BOX  = 'PO_BOX';  // Абонентский ящик
    public const DEMAND  = 'DEMAND';  // До востребования
    public const UNIT    = 'UNIT';    // Для военных частей
}