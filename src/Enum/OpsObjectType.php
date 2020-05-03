<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class OpsObjectType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/postoffice_passport-unload_passport (Данные из спецификации)
 */
class OpsObjectType
{
    public const ALL  = 'ALL'; // Все объекты
    public const OPS  = 'OPS'; // ОПС
    public const PVZ  = 'PVZ'; // ПВЗ
    public const APS  = 'APS'; // Почтаматы
}