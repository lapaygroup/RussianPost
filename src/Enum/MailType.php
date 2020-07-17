<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class MailType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-mail-type (Данные из спецификации)
 */
class MailType
{
    const UNDEFINED = 'UNDEFINED'; // Не определено

    const PARCEL_POSTAL      = 'POSTAL_PARCEL';  // Посылка "нестандартная"
    const PARCEL_ONLINE      = 'ONLINE_PARCEL';  // Посылка "онлайн"
    const PARCEL_FIRST_CLASS = 'PARCEL_CLASS_1'; // Посылка 1-го класса

    const ONLINE_COURIER      = 'ONLINE_COURIER';      // Курьер "онлайн"
    const BUSINESS_COURIER    = 'BUSINESS_COURIER';    // Бизнес курьер
    const BUSINESS_COURIER_ES = 'BUSINESS_COURIER_ES'; // Бизнес курьер экпресс

    const EMS         = 'EMS';         // Отправление EMS
    const EMS_RT      = 'EMS_RT';      // EMS РТ
    const EMS_OPTIMAL = 'EMS_OPTIMAL'; // EMS оптимальн  ое
    const EMS_TENDER  = 'EMS_TENDER';  // EMS тендер

    const BANDEROL             = 'BANDEROL';         // Бандероль
    const BANDEROL_FIRST_CLASS = 'BANDEROL_CLASS_1'; // Бандероль 1-го класса

    const LETTER             = 'LETTER';         // Письмо
    const LETTER_FIRST_CLASS = 'LETTER_CLASS_1'; // Письмо 1-го класса

    const VSD            = 'VSD';         // Отправление ВСД
    const ECOM           = 'ECOM';        // ЕКОМ
    const COMBINED       = 'COMBINED';    // Комбинированное
    const EASY_RETURN    = 'EASY_RETURN'; // Легкий возврат
    const VGPO_CLASS_1   = 'VGPO_CLASS_1'; // ВГПО 1-го класса
    const SMALL_PACKET   = 'SMALL_PACKET'; // Мелкий пакет

}