<?php
namespace LapayGroup\RussianPost\Entity;

/**
 * Возвратное отправление
 *
 * Class ReturnShipment
 * @package LapayGroup\RussianPost\Entity
 */
Class ReturnShipment
{
    /** @var AddressReturn|null */
    private $address_from = null; // Адрес отправителя

    /** @var AddressReturn|null */
    private $address_to = null; // Адрес получателя
    /** @var int|null  */
    private $insr_value = null; // Сумма объявленной ценности (копейки)
    /** @var string  */
    private $mail_type = null; // https://otpravka.pochta.ru/specification#/enums-base-mail-type
    /** @var string|null  */
    private $order_num = null; // Номер заказа. Внешний идентификатор заказа, который формируется отправителем
    /** @var string|null  */
    private $postoffice_code = null; // Индекс ОПС
    /** @var string  */
    private $recipient_name = null; // Наименование получателя одной строкой (ФИО, наименование организации)
    /** @var string  */
    private $sender_name = null; // Наименование отправителя одной строкой (ФИО, наименование организации)

    /**
     * Возвращает массив параметров отправления для запроса
     * @return array
     */
    public function asArr()
    {
        if (empty($this->mail_type))
            throw new \InvalidArgumentException('Не задан вид РПО!');

        if (empty($this->recipient_name))
            throw new \InvalidArgumentException('Не задано наименование получателя!');

        if (empty($this->sender_name))
            throw new \InvalidArgumentException('Не задано наименование отправителя!');

        if (!is_a($this->address_from,AddressReturn::class))
            throw new \InvalidArgumentException('Адрес отправителя долежен быть объектом '.AddressReturn::class);

        if (!is_a($this->address_to,AddressReturn::class))
            throw new \InvalidArgumentException('Адрес получателя долежен быть объектом '.AddressReturn::class);

        $params = [];
        $params['address-from']   = $this->address_from->asArr();
        $params['address-to']     = $this->address_to->asArr();
        $params['mail-type']      = $this->mail_type;
        $params['recipient-name'] = $this->recipient_name;
        $params['sender-name']    = $this->sender_name;

        if (!is_null($this->insr_value))
            $params['insr-value'] = $this->insr_value;

        if (!is_null($this->order_num))
            $params['order-num'] = $this->order_num;

        if (!is_null($this->postoffice_code))
            $params['postoffice-code'] = $this->postoffice_code;

        return $params;
    }

    /**
     * @return AddressReturn|null
     */
    public function getAddressFrom()
    {
        return $this->address_from;
    }

    /**
     * @param AddressReturn|null $address_from
     */
    public function setAddressFrom($address_from)
    {
        $this->address_from = $address_from;
    }

    /**
     * @return AddressReturn|null
     */
    public function getAddressTo()
    {
        return $this->address_to;
    }

    /**
     * @param AddressReturn|null $address_to
     */
    public function setAddressTo($address_to)
    {
        $this->address_to = $address_to;
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
     * @return string
     */
    public function getMailType()
    {
        return $this->mail_type;
    }

    /**
     * @param string $mail_type
     */
    public function setMailType($mail_type)
    {
        $this->mail_type = $mail_type;
    }

    /**
     * @return string|null
     */
    public function getOrderNum()
    {
        return $this->order_num;
    }

    /**
     * @param string|null $order_num
     */
    public function setOrderNum($order_num)
    {
        $this->order_num = $order_num;
    }

    /**
     * @return string|null
     */
    public function getPostofficeCode()
    {
        return $this->postoffice_code;
    }

    /**
     * @param string|null $postoffice_code
     */
    public function setPostofficeCode($postoffice_code)
    {
        $this->postoffice_code = $postoffice_code;
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
     * @return string
     */
    public function getSenderName()
    {
        return $this->sender_name;
    }

    /**
     * @param string $sender_name
     */
    public function setSenderName($sender_name)
    {
        $this->sender_name = $sender_name;
    }
}