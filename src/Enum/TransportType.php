<?php
namespace LapayGroup\RussianPost\Enum;

/**
 * Class TransportType
 * @package LapayGroup\RussianPost
 * @see https://otpravka.pochta.ru/specification#/enums-base-transport-type (Данные из спецификации)
 */
class TransportType
{
    const SURFACE  = 'SURFACE';  // Наземный
    const AVIA     = 'AVIA';     // Авиа
    const COMBINED = 'COMBINED'; // Комбинированный
    const EXPRESS  = 'EXPRESS';  // Системой ускоренной почты
    const STANDARD = 'STANDARD'; // Используется для отправлений "EMS Оптимальное"
}
