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
    const SIMPLE  = 'SIMPLE';

    // Заказное
    const ORDERED = 'ORDERED';

    // Обыкновенное
    const ORDINARY = 'ORDINARY';

    // С объявленной ценностью
    const WITH_DECLARED_VALUE = 'WITH_DECLARED_VALUE';

    // С объявленной ценностью и наложенным платежом
    const WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY = 'WITH_DECLARED_VALUE_AND_CASH_ON_DELIVERY';

    // С объявленной ценностью и обязательным платежом
    const WITH_DECLARED_VALUE_AND_COMPULSORY_PAYMENT = 'WITH_DECLARED_VALUE_AND_COMPULSORY_PAYMENT';

    // С обязательным платежом
    const WITH_COMPULSORY_PAYMENT = 'WITH_COMPULSORY_PAYMENT';
}