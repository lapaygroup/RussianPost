<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class OpsObjectType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/postoffice_passport-unload_passport (Данные из спецификации)
 */
class OpsObjectType
{
    const ALL  = 'ALL'; // Все объекты
    const OPS  = 'OPS'; // ОПС
    const PVZ  = 'PVZ'; // ПВЗ
    const APS  = 'APS'; // Почтаматы
}