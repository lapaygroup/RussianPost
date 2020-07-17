<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class PaymentMethods
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-payment-methods (Данные из спецификации)
 */
class PaymentMethods
{
    const CASHLESS             = 'CASHLESS';            // Безналичный расчет
    const STAMP                = 'STAMP';               // Оплата марками
    const FRANKING             = 'FRANKING';            // Франкирование
    const TO_FRANKING          = 'TO_FRANKING';         // На франкировку
    const ONLINE_PAYMENT_MARK  = 'ONLINE_PAYMENT_MARK'; // Знак онлайн оплаты
}