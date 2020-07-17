<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class DimensionType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-dimension-type (Данные из спецификации)
 */
class DimensionType
{
    const S         = 'S';         // до 260х170х80 мм
    const M         = 'M';         // до 300х200х150 мм
    const L         = 'L';         // до 400х270х180 мм
    const XL        = 'XL';        // 530х260х220 мм
    const OVERSIZED = 'OVERSIZED'; // Негабарит (сумма сторон не более 1400 мм, сторона не более 600 мм)
}