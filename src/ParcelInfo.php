<?php
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Enum\MailCategory;
use LapayGroup\RussianPost\Enum\MailType;
use LapayGroup\RussianPost\Enum\PaymentMethods;

class ParcelInfo
{
    private $completeness_checking = false; // Признак услуги проверки комплектности
    private $contents_checking = false; // Признак услуги проверки комплектности
    private $courier = false; // Отметка 'Курьер'
    private $declared_value = 0; // Объявленная ценность
    private $delivery_point_index = null; // Идентификатор пункта выдачи заказов
    private $goods_value = 0; // Стоимость (для ЕКОМ)
    private $height = 0; // Линейная высота (сантиметры)
    private $length = 0; // Линейная длина (сантиметры)
    private $width = 0; // Линейная ширина (сантиметры)
    private $dimension_type = null; // Типоразмер https://otpravka.pochta.ru/specification#/enums-dimension-type
    private $entries_type = null; // Категория вложения https://otpravka.pochta.ru/specification#/enums-base-entries-type
    private $fragile = false; // Отметка 'Осторожно/Хрупко'
    private $index_to = 101000; // Индекс места назначения (по умолчанию Москва)
    private $index_from = 101000; // Индекс места отправления (по умолчанию Москва)
    private $mail_category = MailCategory::SIMPLE; // Категория РПО https://otpravka.pochta.ru/specification#/enums-base-mail-category
    private $mail_type = MailType::PARCEL_POSTAL; // Вид РПО https://otpravka.pochta.ru/specification#/enums-base-mail-type
    private $mail_direct = null; // Код страны назначения https://otpravka.pochta.ru/specification#/dictionary-countries
    private $weight = 0; // Вес отправления в граммах
    private $payment_method = PaymentMethods::CASHLESS; // Способ оплаты https://otpravka.pochta.ru/specification#/enums-payment-methods
    private $sms_notice_recipient = null; // Отметка 'SMS уведомления'
    private $transport_type = null; // Вид транспортировки https://otpravka.pochta.ru/specification#/enums-base-transport-type
    private $notify = false; // Отметка 'С заказным уведомлением'
    private $simple_notify = false; // Отметка 'С простым уведомлением'
    private $functionality_checking = false; // Признак услуги проверки работоспособности (для ЕКОМ)
    private $with_fitting = false; // Признак услуги 'Возможность примерки' (для ЕКОМ)
    private $inventory = false; // Опись вложения
    private $vsd = null; // Возврат сопроводительныйх документов
    private $with_electronic_notice = null; // Отметка 'С электронным уведомлением'

    /**
     * Возвращает данные по отправлению в виде массива для API ПРФ
     * @return array
     */
    public function getArray()
    {
        $array = [];
        if ($this->completeness_checking)
            $array['completeness-checking'] = $this->isCompletenessChecking();

        if ($this->contents_checking)
            $array['contents-checking'] = $this->isContentsChecking();

        if ($this->courier)
            $array['courier'] = $this->isCourier();

        if ($this->delivery_point_index)
            $array['delivery-point-index'] = $this->getDeliveryPointindex();

        if ($this->goods_value)
            $array['goods-value'] = $this->getGoodsValue();

        if ($this->declared_value)
            $array['declared-value'] = $this->getDeclaredValue();

        if ($this->getHeight() > 0) {
            $array['dimension']['height'] = $this->getHeight();
            $array['dimension']['length'] = $this->getLength();
            $array['dimension']['width']  = $this->getWidth();
        }
        
        if ($this->dimension_type)
            $array['dimension-type'] = $this->getDimensionType();
        
        if ($this->entries_type)
            $array['entries-type'] = $this->getEntriesType();

        if ($this->fragile)
            $array['fragile'] = $this->isFragile();

        $array['index-from'] = $this->getIndexFrom();
        $array['index-to'] = $this->getIndexTo();
        $array['mail-category'] = $this->getMailCategory();
        $array['mail-type'] = $this->getMailType();
        if ($this->mail_direct)
            $array['mail-direct'] = $this->getMailDirect();

        $array['mass'] = $this->getWeight();

        if ($this->payment_method)
            $array['payment-method'] = $this->getPaymentMethod();

        $array['with-order-of-notice'] = $this->isNotify();
        $array['with-simple-notice'] = $this->isSimpleNotify();
        $array['functionality-checking'] = $this->isFunctionalityChecking();
        $array['with-fitting'] = $this->isWithFitting();

        if (null !== $with_electronic_notice = $this->isWithElectronicNotice()) {
            $array['with-electronic-notice'] = $with_electronic_notice;
        }

        if (null !== $sms_notice = $this->getSmsNoticerecipient()) {
            $array['sms-notice-recipient'] = (int)$sms_notice;
        }
        
        if (null !== $transport_type = $this->getTransportType()) {
            $array['transport-type'] = $transport_type;
        }

        if (null !== $vsd = $this->isVsd()) {
            $array['vsd'] = $vsd;
        }

        if ($this->inventory)
            $array['inventory'] = $this->isInventory();

        return $array;
    }

    /**
     * @return bool
     */
    public function isCompletenessChecking()
    {
        return $this->completeness_checking;
    }

    /**
     * @param bool $completeness_checking
     */
    public function setCompletenessChecking($completeness_checking)
    {
        $this->completeness_checking = $completeness_checking;
    }

    /**
     * @return bool
     */
    public function isContentsChecking()
    {
        return $this->contents_checking;
    }

    /**
     * @param bool $contents_checking
     */
    public function setContentsChecking($contents_checking)
    {
        $this->contents_checking = $contents_checking;
    }

    /**
     * @return bool
     */
    public function isCourier()
    {
        return $this->courier;
    }

    /**
     * @param bool $courier
     */
    public function setCourier($courier)
    {
        $this->courier = $courier;
    }

    /**
     * @return int
     */
    public function getDeclaredValue()
    {
        return $this->declared_value;
    }

    /**
     * @param int $declared_value
     */
    public function setDeclaredValue($declared_value)
    {
        $this->declared_value = $declared_value;
    }

    /**
     * @return string|null
     */
    public function getDeliveryPointindex()
    {
        return $this->delivery_point_index;
    }

    /**
     * @param string|null $delivery_point_index
     */
    public function setDeliveryPointindex($delivery_point_index)
    {
        $this->delivery_point_index = $delivery_point_index;
    }

    /**
     * @return int
     */
    public function getGoodsValue()
    {
        return $this->goods_value;
    }

    /**
     * @param int $goods_value
     */
    public function setGoodsValue($goods_value)
    {
        $this->goods_value = $goods_value;
    }

    /**
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * @param int $height
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return int
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * @param int $length
     */
    public function setLength($length)
    {
        $this->length = $length;
    }

    /**
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param int $width
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
     * @return string|null
     */
    public function getEntriesType()
    {
        return $this->entries_type;
    }

    /**
     * @param string|null $entries_type
     */
    public function setEntriesType($entries_type)
    {
        $this->entries_type = $entries_type;
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
     * @return int
     */
    public function getIndexTo()
    {
        return $this->index_to;
    }

    /**
     * @param int $index_to
     */
    public function setIndexTo($index_to)
    {
        $this->index_to = $index_to;
    }
    
    /**
     * @return int
     */
    public function getIndexFrom()
    {
        return $this->index_from;
    }

    /**
     * @param int $index_from
     */
    public function setIndexFrom($index_from)
    {
        $this->index_from = $index_from;
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
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return string
     */
    public function getPaymentMethod()
    {
        return $this->payment_method;
    }

    /**
     * @param string $payment_method
     */
    public function setPaymentMethod($payment_method)
    {
        $this->payment_method = $payment_method;
    }

    /**
     * @return bool
     */
    public function isNotify()
    {
        return $this->notify;
    }

    /**
     * @param bool $notify
     */
    public function setNotify($notify)
    {
        $this->notify = $notify;
    }

    /**
     * @return bool
     */
    public function isSimpleNotify()
    {
        return $this->simple_notify;
    }

    /**
     * @param bool $simple_notify
     */
    public function setSimpleNotify($simple_notify)
    {
        $this->simple_notify = $simple_notify;
    }

    /**
     * @return bool
     */
    public function isFunctionalityChecking()
    {
        return $this->functionality_checking;
    }

    /**
     * @param bool $functionality_checking
     */
    public function setFunctionalityChecking($functionality_checking)
    {
        $this->functionality_checking = $functionality_checking;
    }

    /**
     * @return bool
     */
    public function isWithFitting()
    {
        return $this->with_fitting;
    }

    /**
     * @param bool $with_fitting
     */
    public function setWithFitting($with_fitting)
    {
        $this->with_fitting = $with_fitting;
    }

    /**
     * @return null|bool
     */
    public function getSmsNoticerecipient()
    {
        return $this->sms_notice_recipient;
    }

    /**
     * @param null|bool $sms_notice_recipient
     */
    public function setSmsNoticerecipient($sms_notice_recipient)
    {
        $this->sms_notice_recipient = $sms_notice_recipient;
    }

    /**
     * @return null|string
     */
    public function getTransportType()
    {
        return $this->transport_type;
    }

    /**
     * @param null|string $transport_type
     */
    public function setTransportType($transport_type)
    {
        $this->transport_type = $transport_type;
    }

    /**
     * @return bool
     */
    public function isInventory()
    {
        return $this->inventory;
    }

    /**
     * @param bool $inventory
     */
    public function setInventory($inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * @return bool
     */
    public function isVsd()
    {
        return $this->vsd;
    }

    /**
     * @param bool $vsd
     */
    public function setVsd($vsd)
    {
        $this->vsd = $vsd;
    }

    /**
     * @return bool
     */
    public function isWithElectronicNotice()
    {
        return $this->with_electronic_notice;
    }

    /**
     * @param bool $with_electronic_notice
     */
    public function setWithElectronicNotice($with_electronic_notice)
    {
        $this->with_electronic_notice = $with_electronic_notice;
    }
}
