<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class EntriesType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-entries-type (Данные из спецификации)
 */
class EntriesType
{
    const GIFT              = 'GIFT';              // Подарок
    const DOCUMENT          = 'DOCUMENT';          // Документы
    const SALE_OF_GOODS     = 'SALE_OF_GOODS';     // Продажа товара
    const COMMERCIAL_SAMPLE = 'COMMERCIAL_SAMPLE'; // Коммерческий образец
    const OTHER             = 'OTHER';             // Прочее
}