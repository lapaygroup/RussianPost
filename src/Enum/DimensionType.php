<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class DimensionType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-dimension-type (Данные из спецификации)
 */
class DimensionType
{
    public const S         = 'S';         // до 260х170х80 мм
    public const M         = 'M';         // до 300х200х150 мм
    public const L         = 'L';         // до 400х270х180 мм
    public const XL        = 'XL';        // 530х260х220 мм
    public const OVERSIZED = 'OVERSIZED'; // Негабарит (сумма сторон не более 1400 мм, сторона не более 600 мм)
}