<?php
namespace LapayGroup\RussianPost;

class ParcelInfo
{
    private $courier = false; // Отметка 'Курьер'
    private $declaredValue = 0; // Объявленная ценность
    private $height = 0; // Линейная высота (сантиметры)
    private $length = 0; // Линейная длина (сантиметры)
    private $width = 0; // Линейная ширина (сантиметры)
    private $fragile = false; // Отметка 'Осторожно/Хрупко'
    private $indexTo = 101000; // Индекс места назначения (по умолчанию Москва)
    private $indexFrom = 101000; // Индекс места отправления (по умолчанию Москва)
    private $mailCategory = 'SIMPLE'; // Категория РПО https://otpravka.pochta.ru/specification#/enums-base-mail-category
    private $mailType = 'POSTAL_PARCEL'; // Вид РПО https://otpravka.pochta.ru/specification#/enums-base-mail-type
    private $weight = 0; // Вес отправления в граммах
    private $paymentMethod = 'CASHLESS'; // Способ оплаты https://otpravka.pochta.ru/specification#/enums-payment-methods
    private $smsNoticeRecipient = null; // Отметка 'SMS уведомления'
    private $transportType = null; // Вид транспортировки https://otpravka.pochta.ru/specification#/enums-base-transport-type
    private $notify = false; // Отметка 'С заказным уведомлением'
    private $simpleNotify = false; // Отметка 'С простым уведомлением'
  
    /**
     * Возвращает данные по отправлению в виде массива для API ПРФ
     * @return array
     */
    public function getArray()
    {
        $array = [];
        $array['courier'] = $this->isCourier();
        $array['declared-value'] = $this->getDeclaredValue();
        if ($this->getHeight() > 0) {
            $array['dimension']['height'] = $this->getHeight();
            $array['dimension']['length'] = $this->getLength();
            $array['dimension']['width']  = $this->getWidth();
        }
        $array['fragile'] = $this->isFragile();
        $array['index-from'] = $this->getIndexFrom();
        $array['index-to'] = $this->getIndexTo();
        $array['mail-category'] = $this->getMailCategory();
        $array['mail-type'] = $this->getMailType();
        $array['mass'] = $this->getWeight();
        $array['payment-method'] = $this->getPaymentMethod();
        $array['with-order-of-notice'] = $this->isNotify();
        $array['with-simple-notice'] = $this->isSimpleNotify();

        if (null !== $smsNotice = $this->getSmsNoticeRecipient()) {
            $array['sms-notice-recipient'] = (int)$smsNotice;
        }
        
        if (null !== $transportType = $this->getTransportType()) {
            $array['transport-type'] = $transportType;
        }

        return $array;
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
        return $this->declaredValue;
    }

    /**
     * @param int $declaredValue
     */
    public function setDeclaredValue($declaredValue)
    {
        $this->declaredValue = $declaredValue;
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
        return $this->indexTo;
    }

    /**
     * @param int $indexTo
     */
    public function setIndexTo($indexTo)
    {
        $this->indexTo = $indexTo;
    }
    
    /**
     * @return int
     */
    public function getIndexFrom()
    {
        return $this->indexFrom;
    }

    /**
     * @param int $indexFrom
     */
    public function setIndexFrom($indexFrom)
    {
        $this->indexFrom = $indexFrom;
    }

    /**
     * @return string
     */
    public function getMailCategory()
    {
        return $this->mailCategory;
    }

    /**
     * @param string $mailCategory
     */
    public function setMailCategory($mailCategory)
    {
        $this->mailCategory = $mailCategory;
    }

    /**
     * @return string
     */
    public function getMailType()
    {
        return $this->mailType;
    }

    /**
     * @param string $mailType
     */
    public function setMailType($mailType)
    {
        $this->mailType = $mailType;
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
        return $this->paymentMethod;
    }

    /**
     * @param string $paymentMethod
     */
    public function setPaymentMethod($paymentMethod)
    {
        $this->paymentMethod = $paymentMethod;
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
        return $this->simpleNotify;
    }

    /**
     * @param bool $simpleNotify
     */
    public function setSimpleNotify($simpleNotify)
    {
        $this->simpleNotify = $simpleNotify;
    }

    /**
     * @return null|bool
     */
    public function getSmsNoticeRecipient()
    {
        return $this->smsNoticeRecipient;
    }

    /**
     * @param null|bool $smsNoticeRecipient
     */
    public function setSmsNoticeRecipient($smsNoticeRecipient)
    {
        $this->smsNoticeRecipient = $smsNoticeRecipient;
    }

    /**
     * @return null|string
     */
    public function getTransportType()
    {
        return $this->transportType;
    }

    /**
     * @param null|string $transportType
     */
    public function setTransportType($transportType)
    {
        $this->transportType = $transportType;
    }
}
