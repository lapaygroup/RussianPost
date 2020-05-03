<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class MailType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-mail-type (Данные из спецификации)
 */
class MailType
{
    public const UNDEFINED = 'UNDEFINED'; // Не определено

    public const PARCEL_POSTAL      = 'POSTAL_PARCEL';  // Посылка "нестандартная"
    public const PARCEL_ONLINE      = 'ONLINE_PARCEL';  // Посылка "онлайн"
    public const PARCEL_FIRST_CLASS = 'PARCEL_CLASS_1'; // Посылка 1-го класса

    public const ONLINE_COURIER      = 'ONLINE_COURIER';      // Курьер "онлайн"
    public const BUSINESS_COURIER    = 'BUSINESS_COURIER';    // Бизнес курьер
    public const BUSINESS_COURIER_ES = 'BUSINESS_COURIER_ES'; // Бизнес курьер экпресс

    public const EMS         = 'EMS';         // Отправление EMS
    public const EMS_RT      = 'EMS_RT';      // EMS РТ
    public const EMS_OPTIMAL = 'EMS_OPTIMAL'; // EMS оптимальн  ое
    public const EMS_TENDER  = 'EMS_TENDER';  // EMS тендер

    public const BANDEROL             = 'BANDEROL';         // Бандероль
    public const BANDEROL_FIRST_CLASS = 'BANDEROL_CLASS_1'; // Бандероль 1-го класса

    public const LETTER             = 'LETTER';         // Письмо
    public const LETTER_FIRST_CLASS = 'LETTER_CLASS_1'; // Письмо 1-го класса

    public const VSD            = 'VSD';         // Отправление ВСД
    public const ECOM           = 'ECOM';        // ЕКОМ
    public const COMBINED       = 'COMBINED';    // Комбинированное
    public const EASY_RETURN    = 'EASY_RETURN'; // Легкий возврат
    public const VGPO_CLASS_1   = 'VGPO_CLASS_1'; // ВГПО 1-го класса
    public const SMALL_PACKET   = 'SMALL_PACKET'; // Мелкий пакет

}