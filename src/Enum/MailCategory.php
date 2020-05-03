<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class MailCategory
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-mail-category (Данные из спецификации)
 */
class MailCategory
{
    // Простое
    public const SIMPLE  = 'SIMPLE';

    // Заказное
    public const ORDERED = 'ORDERED';

    // Обыкновенное
    public const ORDINARY = 'ORDINARY';

    // С объявленной ценностью
    public const WITH_DECLARED_VALUE = 'WITH_DECLARED_VALUE';

    // С объявленной ценностью и наложенным платежом
    public const WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY = 'WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY';

    // С объявленной ценностью и обязательным платежом
    public const WITH_DECLARED_VALUE_AND_COMPULSORY_PAYMENT = 'WITH_DECLARED_VALUE_AND_COMPULSORY_PAYMENT';

    // С обязательным платежом
    public const WITH_COMPULSORY_PAYMENT = 'WITH_COMPULSORY_PAYMENT';
}