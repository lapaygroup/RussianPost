<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class TransportType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-transport-type (Данные из спецификации)
 */
class TransportType
{
    const SURFACE  = 'SURFACE';  // Подарок
    const AVIA     = 'AVIA';     // Документы
    const COMBINED = 'COMBINED'; // Продажа товара
    const EXPRESS  = 'EXPRESS';  // Коммерческий образец
    const STANDARD = 'STANDARD'; // Прочее
}