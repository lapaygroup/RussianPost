<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class AddressType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-address-type (Данные из спецификации)
 */
class AddressType
{
    const DEFAULT = 'DEFAULT'; // Стандартный (улица, дом, квартира)
    const PO_BOX  = 'PO_BOX';  // Абонентский ящик
    const DEMAND  = 'DEMAND';  // До востребования
    const UNIT    = 'UNIT';    // Для военных частей
}