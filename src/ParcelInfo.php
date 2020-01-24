<?php
namespace LapayGroup\RussianPost;

class ParcelInfo
{
    private $completenessChecking = false; // Признак услуги проверки комплектности
    private $contentsChecking = false; // Признак услуги проверки комплектности
    private $courier = false; // Отметка 'Курьер'
    private $declaredValue = 0; // Объявленная ценность
    private $deliveryPointIndex = null; // Идентификатор пункта выдачи заказов
    private $goodsValue = 0; // Стоимость (для ЕКОМ)
    private $height = 0; // Линейная высота (сантиметры)
    private $length = 0; // Линейная длина (сантиметры)
    private $width = 0; // Линейная ширина (сантиметры)
    private $dimensionType = null; // Типоразмер
    private $entriesType = null; // Категория вложения https://otpravka.pochta.ru/specification#/enums-base-entries-type
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
    private $functionalityChecking = false; // Признак услуги проверки работоспособности (для ЕКОМ)
    private $withFitting = false; // Признак услуги 'Возможность примерки' (для ЕКОМ)

    /**
     * Возвращает данные по отправлению в виде массива для API ПРФ
     * @return array
     */
    public function getArray()
    {
        $array = [];
        if ($this->completenessChecking)
            $array['completeness-checking'] = $this->isCompletenessChecking();

        if ($this->contentsChecking)
            $array['contents-checking'] = $this->isContentsChecking();

        if ($this->courier)
            $array['courier'] = $this->isCourier();

        if ($this->deliveryPointIndex)
            $array['delivery-point-index'] = $this->getDeliveryPointIndex();

        if ($this->goodsValue)
            $array['goods-value'] = $this->getGoodsValue();

        if ($this->declaredValue)
            $array['declared-value'] = $this->getDeclaredValue();

        if ($this->getHeight() > 0) {
            $array['dimension']['height'] = $this->getHeight();
            $array['dimension']['length'] = $this->getLength();
            $array['dimension']['width']  = $this->getWidth();
        }
        
        if ($this->dimensionType)
            $array['dimension-type'] = $this->getDimensionType();
        
        if ($this->entriesType)
            $array['entries-type'] = $this->getEntriesType();

        if ($this->fragile)
            $array['fragile'] = $this->isFragile();

        $array['index-from'] = $this->getIndexFrom();
        $array['index-to'] = $this->getIndexTo();
        $array['mail-category'] = $this->getMailCategory();
        $array['mail-type'] = $this->getMailType();
        $array['mass'] = $this->getWeight();

        if ($this->paymentMethod)
            $array['payment-method'] = $this->getPaymentMethod();

        $array['with-order-of-notice'] = $this->isNotify();
        $array['with-simple-notice'] = $this->isSimpleNotify();
        $array['functionality-checking'] = $this->isFunctionalityChecking();
        $array['with-fitting'] = $this->isWithFitting();

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
    public function isCompletenessChecking()
    {
        return $this->completenessChecking;
    }

    /**
     * @param bool $completenessChecking
     */
    public function setCompletenessChecking($completenessChecking)
    {
        $this->completenessChecking = $completenessChecking;
    }

    /**
     * @return bool
     */
    public function isContentsChecking()
    {
        return $this->contentsChecking;
    }

    /**
     * @param bool $contentsChecking
     */
    public function setContentsChecking($contentsChecking)
    {
        $this->contentsChecking = $contentsChecking;
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
     * @return string|null
     */
    public function getDeliveryPointIndex()
    {
        return $this->deliveryPointIndex;
    }

    /**
     * @param string|null $deliveryPointIndex
     */
    public function setDeliveryPointIndex($deliveryPointIndex)
    {
        $this->deliveryPointIndex = $deliveryPointIndex;
    }

    /**
     * @return int
     */
    public function getGoodsValue()
    {
        return $this->goodsValue;
    }

    /**
     * @param int $goodsValue
     */
    public function setGoodsValue($goodsValue)
    {
        $this->goodsValue = $goodsValue;
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
        return $this->dimensionType;
    }

    /**
     * @param string|null $dimensionType
     */
    public function setDimensionType($dimensionType)
    {
        $this->dimensionType = $dimensionType;
    }

    /**
     * @return string|null
     */
    public function getEntriesType()
    {
        return $this->entriesType;
    }

    /**
     * @param string|null $entriesType
     */
    public function setEntriesType($entriesType)
    {
        $this->entriesType = $entriesType;
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
     * @return bool
     */
    public function isFunctionalityChecking()
    {
        return $this->functionalityChecking;
    }

    /**
     * @param bool $functionalityChecking
     */
    public function setFunctionalityChecking($functionalityChecking)
    {
        $this->functionalityChecking = $functionalityChecking;
    }

    /**
     * @return bool
     */
    public function isWithFitting()
    {
        return $this->withFitting;
    }

    /**
     * @param bool $withFitting
     */
    public function setWithFitting($withFitting)
    {
        $this->withFitting = $withFitting;
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
