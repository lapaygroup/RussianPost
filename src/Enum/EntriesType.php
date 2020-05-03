<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class EntriesType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-entries-type (Данные из спецификации)
 */
class EntriesType
{
    public const GIFT              = 'GIFT';              // Подарок
    public const DOCUMENT          = 'DOCUMENT';          // Документы
    public const SALE_OF_GOODS     = 'SALE_OF_GOODS';     // Продажа товара
    public const COMMERCIAL_SAMPLE = 'COMMERCIAL_SAMPLE'; // Коммерческий образец
    public const OTHER             = 'OTHER';             // Прочее
}