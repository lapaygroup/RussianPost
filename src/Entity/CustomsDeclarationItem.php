<?php
namespace LapayGroup\RussianPost\Entity;

Class CustomsDeclarationItem
{
    /** @var string|null  */
    private $certificate_number = null; // Сертификаты, сопровождающие отправление
    /** @var string  */
    private $currency = 'RUB'; // Код валюты https://otpravka.pochta.ru/specification#/dictionary-currencies
    private $entries_type = 'GIFT'; // Категория вложения https://otpravka.pochta.ru/specification#/enums-base-entries-type
    /** @var string|null  */
    private $invoice_number = null; // Счет (номер счета-фактуры)
    private $license_number = null; // Лицензии, сопровождающие отправление
    /** @var boolean|null  */
    private $with_certificate = null; // Приложенные документы: сертификат
    /** @var boolean|null  */
    private $with_invoice = null; // Приложенные документы: счет-фактура
    /** @var boolean|null  */
    private $with_license = null; // Приложенные документы: лицензия
    /** @var array */
    private $customs_entries = []; // Список вложений
}