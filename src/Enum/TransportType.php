<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class TransportType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-transport-type (Данные из спецификации)
 */
class TransportType
{
    public const SURFACE  = 'SURFACE';  // Подарок
    public const AVIA     = 'AVIA';     // Документы
    public const COMBINED = 'COMBINED'; // Продажа товара
    public const EXPRESS  = 'EXPRESS';  // Коммерческий образец
    public const STANDARD = 'STANDARD'; // Прочее
}