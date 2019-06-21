<?php
namespace LapayGroup\RussianPost;

/**
 * Class PostType
 * @package LapayGroup\RussianPost
 *
 * Данные из справочника https://delivery.pochta.ru/calc_rpo_delivery_api.pdf
 */
class PostType
{
    const MAIL = 2; // Письмо
    const BANDEROL = 3; // Бандероль
    const POSILKA = 4; // Посылка
    const SMALL_PACKAGE = 5; // Маленький пакет
    const POST_CARD = 6; // Почтовая карточка
    const EMS = 7; // Отправление EMS
    const SEKOGRAMMA = 8; // Секограмма
    const BAG_M = 9; // Мешок "М"
    const VSD = 10; // Отправление VSD
    const MAIL2_0 = 11; // Письмо 2.0
    const NOTIFICATION_FORM = 12; // Бланк уведомлений
    const NEWSPAPER_PACK = 13; // Газетная пачка
    const GROUPED_SHIPMENTS = 14; // Консигнация
    const MAIL_ONE_CLASS = 15; // Писмо первого класса
    const BANDEROL_ONE_CLASS = 16; // Бандероль первого класса
    const NOTIFICATION_FORM_ONE_CLASS = 17; // Бланк уведомления 1 класса
    const INSURANCE_BAG = 18; // Сумка страховая
    const OVPO_MAIL = 19; // ОВПО - письмо
    const MULTI_ENVELOPE = 20; // Мультиконверт
    const HEAVY_MAILING = 21; // Тяжеловесное почтовое отправление
    const OVPO_CARD = 22; // ОВПО - карточка
    const POSILKA_ONLINE = 23; // Посылка онлайн
    const COURIER_ONLIME = 24; // Курьер онлайн
    const DEPARTURE_DM = 25; // Отправление ДМ
    const PACKAGE_DM = 26; // Пакет ДМ
    const POSILKA_STANDART = 27; // Посылка стандарт
    const POSILKA_COURIER = 28; // Посылка курьер
    const POSILKA_COURIER_EMS = 28; // Посылка курьер EMS
    const POSILKA_EXPRESS = 29; // Посылка экспресс
    const BUSINESS_COURIER = 30; // Бизнес курьер
    const BUSINESS_COURIER_EXPRESS = 31; //
    const MAIL_EXPRESS = 32; // Письмо Экспресс
    const MAIL_COURIER = 33; // Письмо Курьерское
    const EMS_OPTIMAL = 34; // EMS оптимальное
    const BANDEROL_SET = 35; // Бандероль-комплект
    const TRACK_CARD = 36; // Трек-открытка
    const TRACK_MAIL = 37; // Трек-письмо
    const POSILKA_EKOMPRO = 38; // Посылка-экомпро
    const KPO_STANDART = 39; // КПО-стандарт
    const KPO_ECONOMY = 40; // КПО-эконом
    const EMS_PT = 41; // EMS PT
    const POSILKA_ONLINE_PLUS = 42; // Посылка онлайн плюс
    const COURIER_ONLINE_PLUS = 43; // Курьер онлайн плюс
    const VGPO_ONE_CLASS = 46; // ВГПО 1 кл
    const POSILKA_ONE_CLASS = 47; // Посылка 1-го класса
    const MAIL_ONE_CLASS2_0 = 48; // Письмо 1-го класса 2.0
    const MAIL_ONE_CLASS_COURIER = 49; // Письмо 1-го класса Курьерское
}