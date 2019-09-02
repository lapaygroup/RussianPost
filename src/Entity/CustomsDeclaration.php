<?php
namespace LapayGroup\RussianPost\Entity;

/**
 * Таможенная декларация (для международных отправлений)
 *
 * Class CustomsDeclaration
 * @package LapayGroup\RussianPost\Entity
 */
Class CustomsDeclaration
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

    /**
     * @return string|null
     */
    public function getCertificateNumber()
    {
        return $this->certificate_number;
    }

    /**
     * @param string|null $certificate_number
     */
    public function setCertificateNumber($certificate_number)
    {
        $this->certificate_number = $certificate_number;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getEntriesType()
    {
        return $this->entries_type;
    }

    /**
     * @param string $entries_type
     */
    public function setEntriesType($entries_type)
    {
        $this->entries_type = $entries_type;
    }

    /**
     * @return string|null
     */
    public function getInvoiceNumber()
    {
        return $this->invoice_number;
    }

    /**
     * @param string|null $invoice_number
     */
    public function setInvoiceNumber($invoice_number)
    {
        $this->invoice_number = $invoice_number;
    }

    /**
     * @return null
     */
    public function getLicenseNumber()
    {
        return $this->license_number;
    }

    /**
     * @param null $license_number
     */
    public function setLicenseNumber($license_number)
    {
        $this->license_number = $license_number;
    }

    /**
     * @return bool|null
     */
    public function getWithCertificate()
    {
        return $this->with_certificate;
    }

    /**
     * @param bool|null $with_certificate
     */
    public function setWithCertificate($with_certificate)
    {
        $this->with_certificate = $with_certificate;
    }

    /**
     * @return bool|null
     */
    public function getWithInvoice()
    {
        return $this->with_invoice;
    }

    /**
     * @param bool|null $with_invoice
     */
    public function setWithInvoice($with_invoice)
    {
        $this->with_invoice = $with_invoice;
    }

    /**
     * @return bool|null
     */
    public function getWithLicense()
    {
        return $this->with_license;
    }

    /**
     * @param bool|null $with_license
     */
    public function setWithLicense($with_license)
    {
        $this->with_license = $with_license;
    }

    /**
     * @return array
     */
    public function getCustomsEntries()
    {
        return $this->customs_entries;
    }

    /**
     * @param array $customs_entries
     */
    public function setCustomsEntries($customs_entries)
    {
        $this->customs_entries = $customs_entries;
    }
}