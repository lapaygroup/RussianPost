<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class PaymentMethods
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-payment-methods (Данные из спецификации)
 */
class PaymentMethods
{
    public const CASHLESS             = 'CASHLESS';            // Безналичный расчет
    public const STAMP                = 'STAMP';               // Оплата марками
    public const FRANKING             = 'FRANKING';            // Франкирование
    public const TO_FRANKING          = 'TO_FRANKING';         // На франкировку
    public const ONLINE_PAYMENT_MARK  = 'ONLINE_PAYMENT_MARK'; // Знак онлайн оплаты
}