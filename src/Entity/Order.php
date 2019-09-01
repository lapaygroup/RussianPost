<?php
namespace LapayGroup\RussianPost\Entity;

use LapayGroup\RussianPost\Entity\Item;
use LapayGroup\RussianPost\Entity\EcomData;
use LapayGroup\RussianPost\Entity\CustomsDeclaration;

Class Order
{
    /** @var string  */
    private $order_num = ''; // * Номер заказа. Внешний идентификатор заказа, который формируется отправителем
    /** @var int|null  */
    private $post_office_code = null; // Индекс места приема
    /** @var string */
    private $address_type_to = 'DEFAULT'; // * Тип адреса https://otpravka.pochta.ru/specification#/enums-base-address-type
    /** @var string|null  */
    private $raw_address = null; // Необработанный адрес получателя
    /** @var int|null  */
    private $index_to = null; // Почтовый индекс получателя
    /** @var string|null  */
    private $str_index_to = null; // Индекс получателя (иностранные отправления)
    /** @var string  */
    private $region_to = ''; // * Область, регион получателя
    /** @var string|null  */
    private $area_to = null; // Район получателя
    /** @var string  */
    private $place_to = ''; // * Населенный пункт
    private $street_to = null; // * Улица получателя
    /** @var string|null  */
    private $branch_name = null; // Идентификатор подразделения
    /** @var string|null */
    private $location_to = null; // Микрорайон
    /** @var string|null  */
    private $house_to = null; // * Номер дома получателя
    /** @var string|null  */
    private $letter_to = null; // Литера дома получателя
    /** @var string|null  */
    private $slash_to = null; // Дробь
    /** @var string|null  */
    private $building_to = null; // Строение получателя
    /** @var string|null  */
    private $corpus_to = null; // Корпус получателя
    /** @var string|null  */
    private $vladenie_to = null; // Владение получателя
    /** @var string|null  */
    private $hotel_to = null; // Название гостиницы
    /** @var string|null  */
    private $office_to = null; // Офис получателя
    /** @var string|null  */
    private $room_to = null; // Номер помещения
    /** @var boolean|null  */
    private $completeness_checking = null; // Проверка комплектации
    /** @var integer|null */
    private $compulsory_payment = null; // К оплате с получателя в копейках
    /** @var boolean|null  */
    private $courier = null; // Отметка "Курьер"
    /** @var customsDeclaration|null  */
    private $customsDeclaration = null; // Таможенная декларация
    /** @var boolean|null  */
    private $delivery_with_cod = null; // Признак оплаты при получении
    /** @var int|null  */
    private $height = null; // Высота в см.
    /** @var int|null  */
    private $length = null; // Длина в см.
    /** @var int|null  */
    private $width = null; // Ширина в см.
    /** @var string|null  */
    private $dimension_type = null; // Типоразмер https://otpravka.pochta.ru/specification#/enums-dimension-type
    /** @var boolean|null  */
    private $easy_return = null; // Лёгкий возврат
    /** @var ecomData|null  */
    private $ecomData = null; // Данные отправления ЕКОМ
    /** @var string|null  */
    private $envelope_type = null; // Тип конверта - ГОСТ Р 51506-99 https://otpravka.pochta.ru/specification#/enums-base-envelope-type
    /** @var boolean  */
    private $fragile = false; // * Отметка Осторожно/Хрупкое
    /** @var string  */
    private $given_name = ''; // * Имя получателя
    /** @var array|null  */
    private $items = null; // Товары в заказе (масссив объектов Item)
    /** @var integer|null */
    private $insr_value = null; // Объявленная ценность в копейках
    /** @var boolean|null  */
    private $inventory = null; // Наличие описи вложения
    /** @var string  */
    private $mail_category = 'SIMPLE'; // * Категория РПО https://otpravka.pochta.ru/specification#/enums-base-mail-category
    /** @var int  */
    private $mail_direct = 643; // * Код страны https://otpravka.pochta.ru/specification#/dictionary-countries
    /** @var int  */
    private $mass = 0; // Вес РПО в граммах
    /** @var string  */
    private $surname = ''; // * Фамилия получателя
    /** @var string|null  */
    private $middle_name = null; // Отчество получателя
    /** @var boolean|null  */
    private $no_return = null; // Отметка "Возврату не подлежит"
    /** @var string|null  */
    private $notice_payment_method = null; // Способ оплаты уведомления https://otpravka.pochta.ru/specification#/enums-payment-methods
    /** @var string|null  */
    private $num_address_type_to = null; // Номер для а/я, войсковая часть, войсковая часть ЮЯ, полевая почта
    /** @var int|null  */
    private $payment = null; // Сумма наложенного платежа в копейках
    /** @var string|null  */
    private $payment_method = null; // Способ оплаты https://otpravka.pochta.ru/specification#/enums-payment-methods
    /** @var string  */
    private $recipient_name = ''; // * Наименование получателя одной строкой (ФИО, наименование организации)
    /** @var int|null  */
    private $sms_notice_recipient = null; // Признак услуги SMS уведомления
    /** @var int|null  */
    private $tel_address = null; // Телефон получателя (только цифры)
    /** @var string|null  */
    private $transport_type = null; // Возможный вид транспортировки (для международных отправлений) https://otpravka.pochta.ru/specification#/enums-base-transport-type
    /** @var boolean|null  */
    private $vsd = null; // Возврат сопроводительных документов
    /** @var boolean|null  */
    private $with_electronic_notice = null; // Отметка 'С электронным уведомлением'
    /** @var boolean|null  */
    private $with_order_of_notice = null; // Отметка 'С заказным уведомлением'
    /** @var boolean|null  */
    private $with_simple_notice = null; // Отметка 'С простым уведомлением'
    /** @var boolean|null  */
    private $wo_mail_rank = null; // Отметка "Без разряда"

    public function asArr()
    {
        // TODO подготовка запроса
    }

    /**
     * @return string
     */
    public function getOrderNum()
    {
        return $this->order_num;
    }

    /**
     * @param string $order_num
     */
    public function setOrderNum($order_num)
    {
        $this->order_num = $order_num;
    }

    /**
     * @return int|null
     */
    public function getPostOfficeCode()
    {
        return $this->post_office_code;
    }

    /**
     * @param int|null $post_office_code
     */
    public function setPostOfficeCode($post_office_code)
    {
        $this->post_office_code = $post_office_code;
    }

    /**
     * @return string
     */
    public function getAddressTypeTo()
    {
        return $this->address_type_to;
    }

    /**
     * @param string $address_type_to
     */
    public function setAddressTypeTo($address_type_to)
    {
        $this->address_type_to = $address_type_to;
    }

    /**
     * @return string|null
     */
    public function getRawAddress()
    {
        return $this->raw_address;
    }

    /**
     * @param string|null $raw_address
     */
    public function setRawAddress($raw_address)
    {
        $this->raw_address = $raw_address;
    }

    /**
     * @return int|null
     */
    public function getIndexTo()
    {
        return $this->index_to;
    }

    /**
     * @param int|null $index_to
     */
    public function setIndexTo($index_to)
    {
        $this->index_to = $index_to;
    }

    /**
     * @return string|null
     */
    public function getStrIndexTo()
    {
        return $this->str_index_to;
    }

    /**
     * @param string|null $str_index_to
     */
    public function setStrIndexTo($str_index_to)
    {
        $this->str_index_to = $str_index_to;
    }

    /**
     * @return string
     */
    public function getRegionTo()
    {
        return $this->region_to;
    }

    /**
     * @param string $region_to
     */
    public function setRegionTo($region_to)
    {
        $this->region_to = $region_to;
    }

    /**
     * @return string|null
     */
    public function getAreaTo()
    {
        return $this->area_to;
    }

    /**
     * @param string|null $area_to
     */
    public function setAreaTo($area_to)
    {
        $this->area_to = $area_to;
    }

    /**
     * @return string
     */
    public function getPlaceTo()
    {
        return $this->place_to;
    }

    /**
     * @param string $place_to
     */
    public function setPlaceTo($place_to)
    {
        $this->place_to = $place_to;
    }

    /**
     * @return null
     */
    public function getStreetTo()
    {
        return $this->street_to;
    }

    /**
     * @param null $street_to
     */
    public function setStreetTo($street_to)
    {
        $this->street_to = $street_to;
    }

    /**
     * @return string|null
     */
    public function getBranchName()
    {
        return $this->branch_name;
    }

    /**
     * @param string|null $branch_name
     */
    public function setBranchName($branch_name)
    {
        $this->branch_name = $branch_name;
    }

    /**
     * @return string|null
     */
    public function getLocationTo()
    {
        return $this->location_to;
    }

    /**
     * @param string|null $location_to
     */
    public function setLocationTo($location_to)
    {
        $this->location_to = $location_to;
    }

    /**
     * @return string|null
     */
    public function getHouseTo()
    {
        return $this->house_to;
    }

    /**
     * @param string|null $house_to
     */
    public function setHouseTo($house_to)
    {
        $this->house_to = $house_to;
    }

    /**
     * @return string|null
     */
    public function getLetterTo()
    {
        return $this->letter_to;
    }

    /**
     * @param string|null $letter_to
     */
    public function setLetterTo($letter_to)
    {
        $this->letter_to = $letter_to;
    }

    /**
     * @return string|null
     */
    public function getSlashTo()
    {
        return $this->slash_to;
    }

    /**
     * @param string|null $slash_to
     */
    public function setSlashTo($slash_to)
    {
        $this->slash_to = $slash_to;
    }

    /**
     * @return string|null
     */
    public function getBuildingTo()
    {
        return $this->building_to;
    }

    /**
     * @param string|null $building_to
     */
    public function setBuildingTo($building_to)
    {
        $this->building_to = $building_to;
    }

    /**
     * @return string|null
     */
    public function getCorpusTo()
    {
        return $this->corpus_to;
    }

    /**
     * @param string|null $corpus_to
     */
    public function setCorpusTo($corpus_to)
    {
        $this->corpus_to = $corpus_to;
    }

    /**
     * @return string|null
     */
    public function getVladenieTo()
    {
        return $this->vladenie_to;
    }

    /**
     * @param string|null $vladenie_to
     */
    public function setVladenieTo($vladenie_to)
    {
        $this->vladenie_to = $vladenie_to;
    }

    /**
     * @return string|null
     */
    public function getHotelTo()
    {
        return $this->hotel_to;
    }

    /**
     * @param string|null $hotel_to
     */
    public function setHotelTo($hotel_to)
    {
        $this->hotel_to = $hotel_to;
    }

    /**
     * @return string|null
     */
    public function getOfficeTo()
    {
        return $this->office_to;
    }

    /**
     * @param string|null $office_to
     */
    public function setOfficeTo($office_to)
    {
        $this->office_to = $office_to;
    }

    /**
     * @return string|null
     */
    public function getRoomTo()
    {
        return $this->room_to;
    }

    /**
     * @param string|null $room_to
     */
    public function setRoomTo($room_to)
    {
        $this->room_to = $room_to;
    }

    /**
     * @return bool|null
     */
    public function getCompletenessChecking()
    {
        return $this->completeness_checking;
    }

    /**
     * @param bool|null $completeness_checking
     */
    public function setCompletenessChecking($completeness_checking)
    {
        $this->completeness_checking = $completeness_checking;
    }

    /**
     * @return int|null
     */
    public function getCompulsoryPayment()
    {
        return $this->compulsory_payment;
    }

    /**
     * @param int|null $compulsory_payment
     */
    public function setCompulsoryPayment($compulsory_payment)
    {
        $this->compulsory_payment = $compulsory_payment;
    }

    /**
     * @return bool|null
     */
    public function getCourier()
    {
        return $this->courier;
    }

    /**
     * @param bool|null $courier
     */
    public function setCourier($courier)
    {
        $this->courier = $courier;
    }

    /**
     * @return customsDeclaration|null
     */
    public function getCustomsDeclaration()
    {
        return $this->customsDeclaration;
    }

    /**
     * @param customsDeclaration|null $customsDeclaration
     */
    public function setCustomsDeclaration($customsDeclaration)
    {
        $this->customsDeclaration = $customsDeclaration;
    }

    /**
     * @return bool|null
     */
    public function getDeliveryWithCod()
    {
        return $this->delivery_with_cod;
    }

    /**
     * @param bool|null $delivery_with_cod
     */
    public function setDeliveryWithCod($delivery_with_cod)
    {
        $this->delivery_with_cod = $delivery_with_cod;
    }

    /**
     * @return int|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int|null $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int|null
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int|null $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return int|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int|null $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return string|null
     */
    public function getDimensionType()
    {
        return $this->dimension_type;
    }

    /**
     * @param string|null $dimension_type
     */
    public function setDimensionType($dimension_type)
    {
        $this->dimension_type = $dimension_type;
    }

    /**
     * @return bool|null
     */
    public function getEasyReturn()
    {
        return $this->easy_return;
    }

    /**
     * @param bool|null $easy_return
     */
    public function setEasyReturn($easy_return)
    {
        $this->easy_return = $easy_return;
    }

    /**
     * @return ecomData|null
     */
    public function getEcomData()
    {
        return $this->ecomData;
    }

    /**
     * @param ecomData|null $ecomData
     */
    public function setEcomData($ecomData)
    {
        $this->ecomData = $ecomData;
    }

    /**
     * @return string|null
     */
    public function getEnvelopeType()
    {
        return $this->envelope_type;
    }

    /**
     * @param string|null $envelope_type
     */
    public function setEnvelopeType($envelope_type)
    {
        $this->envelope_type = $envelope_type;
    }

    /**
     * @return bool
     */
    public function isFragile()
    {
        return $this->fragile;
    }

    /**
     * @param bool $fragile
     */
    public function setFragile($fragile)
    {
        $this->fragile = $fragile;
    }

    /**
     * @return string
     */
    public function getGivenName()
    {
        return $this->given_name;
    }

    /**
     * @param string $given_name
     */
    public function setGivenName($given_name)
    {
        $this->given_name = $given_name;
    }

    /**
     * @return array|null
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param array|null $items
     */
    public function setItems($items)
    {
        $this->items = $items;
    }

    /**
     * @return int|null
     */
    public function getInsrValue()
    {
        return $this->insr_value;
    }

    /**
     * @param int|null $insr_value
     */
    public function setInsrValue($insr_value)
    {
        $this->insr_value = $insr_value;
    }

    /**
     * @return bool|null
     */
    public function getInventory()
    {
        return $this->inventory;
    }

    /**
     * @param bool|null $inventory
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * @return string
     */
    public function getMailCategory()
    {
        return $this->mail_category;
    }

    /**
     * @param string $mail_category
     */
    public function setMailCategory($mail_category)
    {
        $this->mail_category = $mail_category;
    }

    /**
     * @return int
     */
    public function getMailDirect()
    {
        return $this->mail_direct;
    }

    /**
     * @param int $mail_direct
     */
    public function setMailDirect($mail_direct)
    {
        $this->mail_direct = $mail_direct;
    }

    /**
     * @return int
     */
    public function getMass()
    {
        return $this->mass;
    }

    /**
     * @param int $mass
     */
    public function setMass($mass)
    {
        $this->mass = $mass;
    }

    /**
     * @return string
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * @param string $surname
     */
    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    /**
     * @return string|null
     */
    public function getMiddleName()
    {
        return $this->middle_name;
    }

    /**
     * @param string|null $middle_name
     */
    public function setMiddleName($middle_name)
    {
        $this->middle_name = $middle_name;
    }

    /**
     * @return bool|null
     */
    public function getNoReturn()
    {
        return $this->no_return;
    }

    /**
     * @param bool|null $no_return
     */
    public function setNoReturn($no_return)
    {
        $this->no_return = $no_return;
    }

    /**
     * @return string|null
     */
    public function getNoticePaymentMethod()
    {
        return $this->notice_payment_method;
    }

    /**
     * @param string|null $notice_payment_method
     */
    public function setNoticePaymentMethod($notice_payment_method)
    {
        $this->notice_payment_method = $notice_payment_method;
    }

    /**
     * @return string|null
     */
    public function getNumAddressTypeTo()
    {
        return $this->num_address_type_to;
    }

    /**
     * @param string|null $num_address_type_to
     */
    public function setNumAddressTypeTo($num_address_type_to)
    {
        $this->num_address_type_to = $num_address_type_to;
    }

    /**
     * @return int|null
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * @param int|null $payment
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
    }

    /**
     * @return string|null
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * @param string|null $payment_method
     */
    public function setPaymentMethod($payment_method)
    {
        $this->payment_method = $payment_method;
    }

    /**
     * @return string
     */
    public function getRecipientName()
    {
        return $this->recipient_name;
    }

    /**
     * @param string $recipient_name
     */
    public function setRecipientName($recipient_name)
    {
        $this->recipient_name = $recipient_name;
    }

    /**
     * @return int|null
     */
    public function getSmsNoticeRecipient()
    {
        return $this->sms_notice_recipient;
    }

    /**
     * @param int|null $sms_notice_recipient
     */
    public function setSmsNoticeRecipient($sms_notice_recipient)
    {
        $this->sms_notice_recipient = $sms_notice_recipient;
    }

    /**
     * @return int|null
     */
    public function getTelAddress()
    {
        return $this->tel_address;
    }

    /**
     * @param int|null $tel_address
     */
    public function setTelAddress($tel_address)
    {
        $this->tel_address = $tel_address;
    }

    /**
     * @return string|null
     */
    public function getTransportType()
    {
        return $this->transport_type;
    }

    /**
     * @param string|null $transport_type
     */
    public function setTransportType($transport_type)
    {
        $this->transport_type = $transport_type;
    }

    /**
     * @return bool|null
     */
    public function getVsd()
    {
        return $this->vsd;
    }

    /**
     * @param bool|null $vsd
     */
    public function setVsd($vsd)
    {
        $this->vsd = $vsd;
    }

    /**
     * @return bool|null
     */
    public function getWithElectronicNotice()
    {
        return $this->with_electronic_notice;
    }

    /**
     * @param bool|null $with_electronic_notice
     */
    public function setWithElectronicNotice($with_electronic_notice)
    {
        $this->with_electronic_notice = $with_electronic_notice;
    }

    /**
     * @return bool|null
     */
    public function getWithOrderOfNotice()
    {
        return $this->with_order_of_notice;
    }

    /**
     * @param bool|null $with_order_of_notice
     */
    public function setWithOrderOfNotice($with_order_of_notice)
    {
        $this->with_order_of_notice = $with_order_of_notice;
    }

    /**
     * @return bool|null
     */
    public function getWithSimpleNotice()
    {
        return $this->with_simple_notice;
    }

    /**
     * @param bool|null $with_simple_notice
     */
    public function setWithSimpleNotice($with_simple_notice)
    {
        $this->with_simple_notice = $with_simple_notice;
    }

    /**
     * @return bool|null
     */
    public function getWoMailRank()
    {
        return $this->wo_mail_rank;
    }

    /**
     * @param bool|null $wo_mail_rank
     */
    public function setWoMailRank($wo_mail_rank)
    {
        $this->wo_mail_rank = $wo_mail_rank;
    }
}