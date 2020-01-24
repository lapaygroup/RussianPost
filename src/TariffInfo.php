<?php
namespace LapayGroup\RussianPost;

class TariffInfo
{
    private $totalRate = 0; // Плата всего (коп)
    private $totalNds = 0; // Всего НДС (коп)

    // Плата за Авиа-пересылку
    private $aviaRate = 0; // Тариф без НДС (коп)
    private $aviaNds = 0; // НДС (коп)

    // Плата за 'Проверку комплектности'
    private $completenessCheckingRate = 0; // Тариф без НДС (коп);
    private $completenessCheckingNds = 0; // НДС (коп)

    // Плата за 'Проверку вложений'
    private $contentsCheckingRate = 0; // Тариф без НДС (коп);
    private $contentsCheckingNds = 0; // НДС (коп)

    // Примерные сроки доставки
    private $deliveryMinDays = 0; // Минимальное время доставки (дни)
    private $deliveryMaxDays = 0; // Максимальное время доставки (дни)

    // Надбавка за отметку 'Осторожно/Хрупкое'
    private $fragileRate = 0; // Тариф без НДС (коп)
    private $fragileNds = 0; // НДС (коп)

    // Плата за услугу проверки работоспособности
    private $functionalityCheckingRate = 0; // Тариф без НДС (коп)
    private $functionalityCheckingNds = 0; // НДС (коп)

    // Плата за пересылку
    private $groundRate = 0; // Тариф без НДС (коп)
    private $groundNds = 0; // НДС (коп)

    // Плата за объявленную ценность (коп)
    private $insuranceRate = 0; // Тариф без НДС (коп)
    private $insuranceNds = 0; // НДС (коп)

    // Надбавка за уведомление о вручении
    private $noticeRate = 0; // Тариф без НДС (коп)
    private $noticeNds = 0; // НДС (коп)

    // Надбавка за негабарит при весе более 10кг
    private $oversizeRate = 0; // Тариф без НДС (коп)
    private $oversizeNds = 0; // НДС (коп)
    
    // Плата за услугу примерки
    private $withFittingRate = 0; // Тариф без НДС (коп);
    private $withFittingNds = 0; // НДС (коп)

    /**
     * TariffInfo constructor
     * @param array $rawData - сырые данные от api ПРФ
     */
    public function __construct($rawData)
    {
        if (!empty($rawData['total-rate']))
            $this->setTotalRate($rawData['total-rate']);

        if (!empty($rawData['total-vat']))
            $this->setTotalNds($rawData['total-vat']);

        if (!empty($rawData['avia-rate']) && !empty($rawData['avia-rate']['rate'])) {
            $this->setAviaRate($rawData['avia-rate']['rate']);
        }

        if (!empty($rawData['avia-rate']) && !empty($rawData['avia-rate']['vat'])) {
            $this->setAviaNds($rawData['avia-rate']['vat']);
        }

        if (!empty($rawData['completeness-checking-rate']) && !empty($rawData['completeness-checking-rate']['rate'])) {
            $this->setCompletenessCheckingRate($rawData['completeness-checking-rate']['rate']);
        }

        if (!empty($rawData['completeness-checking-rate']) && !empty($rawData['completeness-checking-rate']['vat'])) {
            $this->setCompletenessCheckingNds($rawData['completeness-checking-rate']['vat']);
        }

        if (!empty($rawData['contents-checking-rate']) && !empty($rawData['contents-checking-rate']['rate'])) {
            $this->setContentsCheckingRate($rawData['contents-checking-rate']['rate']);
        }

        if (!empty($rawData['contents-checking-rate']) && !empty($rawData['contents-checking-rate']['vat'])) {
            $this->setContentsCheckingNds($rawData['contents-checking-rate']['vat']);
        }

        if (!empty($rawData['delivery-time']) && !empty($rawData['delivery-time']['min-days'])) {
            $this->setDeliveryMinDays($rawData['delivery-time']['min-days']);
        }

        if (!empty($rawData['delivery-time']) && !empty($rawData['delivery-time']['max-days'])) {
            $this->setDeliveryMaxDays($rawData['delivery-time']['max-days']);
        }

        if (!empty($rawData['fragile-rate']) && !empty($rawData['fragile-rate']['rate'])) {
            $this->setFragileRate($rawData['fragile-rate']['rate']);
        }

        if (!empty($rawData['fragile-rate']) && !empty($rawData['fragile-rate']['vat'])) {
            $this->setFragileNds($rawData['fragile-rate']['vat']);
        }
        
        if (!empty($rawData['functionality-checking-rate']) && !empty($rawData['functionality-checking-rate']['rate'])) {
            $this->setFunctionalityCheckingRate($rawData['functionality-checking-rate']['rate']);
        }
        
        if (!empty($rawData['functionality-checking-rate']) && !empty($rawData['functionality-checking-rate']['vat'])) {
            $this->setFunctionalityCheckingNds($rawData['functionality-checking-rate']['vat']);
        }

        if (!empty($rawData['ground-rate']) && !empty($rawData['ground-rate']['rate'])) {
            $this->setGroundRate($rawData['ground-rate']['rate']);
        }

        if (!empty($rawData['ground-rate']) && !empty($rawData['ground-rate']['vat'])) {
            $this->setGroundNds($rawData['ground-rate']['vat']);
        }

        if (!empty($rawData['insurance-rate']) && !empty($rawData['insurance-rate']['rate'])) {
            $this->setInsuranceRate($rawData['insurance-rate']['rate']);
        }

        if (!empty($rawData['insurance-rate']) && !empty($rawData['insurance-rate']['vat'])) {
            $this->setInsuranceNds($rawData['insurance-rate']['vat']);
        }

        if (!empty($rawData['notice-rate']) && !empty($rawData['notice-rate']['rate'])) {
            $this->setNoticeRate($rawData['notice-rate']['rate']);
        }

        if (!empty($rawData['notice-rate']) && !empty($rawData['notice-rate']['vat'])) {
            $this->setNoticeNds($rawData['notice-rate']['vat']);
        }

        if (!empty($rawData['oversize-rate']) && !empty($rawData['oversize-rate']['rate'])) {
            $this->setOversizeRate($rawData['oversize-rate']['rate']);
        }

        if (!empty($rawData['oversize-rate']) && !empty($rawData['oversize-rate']['vat'])) {
            $this->setOversizeNds($rawData['oversize-rate']['vat']);
        }

        if (!empty($rawData['with-fitting-rate']) && !empty($rawData['with-fitting-rate']['rate'])) {
            $this->setWithFittingRate($rawData['with-fitting-rate']['rate']);
        }
        
        if (!empty($rawData['with-fitting-rate']) && !empty($rawData['with-fitting-rate']['vat'])) {
            $this->setWithFittingNds($rawData['with-fitting-rate']['vat']);
        }
    }

    /**
     * @return int
     */
    public function getTotalRate()
    {
        return $this->totalRate;
    }

    /**
     * @param int $totalRate
     */
    public function setTotalRate($totalRate)
    {
        $this->totalRate = $totalRate;
    }

    /**
     * @return int
     */
    public function getTotalNds()
    {
        return $this->totalNds;
    }

    /**
     * @param int $totalNds
     */
    public function setTotalNds($totalNds)
    {
        $this->totalNds = $totalNds;
    }

    /**
     * @return int
     */
    public function getCompletenessCheckingRate()
    {
        return $this->completenessCheckingRate;
    }

    /**
     * @param int $completenessCheckingRate
     */
    public function setCompletenessCheckingRate($completenessCheckingRate)
    {
        $this->completenessCheckingRate = $completenessCheckingRate;
    }

    /**
     * @return int
     */
    public function getCompletenessCheckingNds()
    {
        return $this->completenessCheckingNds;
    }

    /**
     * @param int $completenessCheckingNds
     */
    public function setCompletenessCheckingNds($completenessCheckingNds)
    {
        $this->completenessCheckingNds = $completenessCheckingNds;
    }

    /**
     * @return int
     */
    public function getContentsCheckingRate()
    {
        return $this->contentsCheckingRate;
    }

    /**
     * @param int $contentsCheckingRate
     */
    public function setContentsCheckingRate($contentsCheckingRate)
    {
        $this->contentsCheckingRate = $contentsCheckingRate;
    }

    /**
     * @param int $contentsCheckingNds
     */
    public function setContentsCheckingNds($contentsCheckingNds)
    {
        $this->contentsCheckingNds = $contentsCheckingNds;
    }

    /**
     * @return int
     */
    public function getContentsCheckingNds()
    {
        return $this->contentsCheckingNds;
    }

    /**
     * @return int
     */
    public function getAviaRate()
    {
        return $this->aviaRate;
    }

    /**
     * @param int $aviaRate
     */
    public function setAviaRate($aviaRate)
    {
        $this->aviaRate = $aviaRate;
    }

    /**
     * @return int
     */
    public function getAviaNds()
    {
        return $this->aviaNds;
    }

    /**
     * @param int $aviaNds
     */
    public function setAviaNds($aviaNds)
    {
        $this->aviaNds = $aviaNds;
    }

    /**
     * @return int
     */
    public function getDeliveryMinDays()
    {
        return $this->deliveryMinDays;
    }

    /**
     * @param int $deliveryMinDays
     */
    public function setDeliveryMinDays($deliveryMinDays)
    {
        $this->deliveryMinDays = $deliveryMinDays;
    }

    /**
     * @return int
     */
    public function getDeliveryMaxDays()
    {
        return $this->deliveryMaxDays;
    }

    /**
     * @param int $deliveryMaxDays
     */
    public function setDeliveryMaxDays($deliveryMaxDays)
    {
        $this->deliveryMaxDays = $deliveryMaxDays;
    }

    /**
     * @return int
     */
    public function getFragileRate()
    {
        return $this->fragileRate;
    }

    /**
     * @param int $fragileRate
     */
    public function setFragileRate($fragileRate)
    {
        $this->fragileRate = $fragileRate;
    }

    /**
     * @return int
     */
    public function getFragileNds()
    {
        return $this->fragileNds;
    }

    /**
     * @param int $fragileNds
     */
    public function setFragileNds($fragileNds)
    {
        $this->fragileNds = $fragileNds;
    }

    /**
     * @return int
     */
    public function getFunctionalityCheckingRate()
    {
        return $this->functionalityCheckingRate;
    }

    /**
     * @param int $functionalityCheckingRate
     */
    public function setFunctionalityCheckingRate($functionalityCheckingRate)
    {
        $this->functionalityCheckingRate = $functionalityCheckingRate;
    }

    /**
     * @return int
     */
    public function getFunctionalityCheckingNds()
    {
        return $this->functionalityCheckingNds;
    }

    /**
     * @param int $functionalityCheckingNds
     */
    public function setFunctionalityCheckingNds($functionalityCheckingNds)
    {
        $this->functionalityCheckingNds = $functionalityCheckingNds;
    }

    /**
     * @return int
     */
    public function getGroundRate()
    {
        return $this->groundRate;
    }

    /**
     * @param int $groundRate
     */
    public function setGroundRate($groundRate)
    {
        $this->groundRate = $groundRate;
    }

    /**
     * @return int
     */
    public function getGroundNds()
    {
        return $this->groundNds;
    }

    /**
     * @param int $groundNds
     */
    public function setGroundNds($groundNds)
    {
        $this->groundNds = $groundNds;
    }

    /**
     * @return int
     */
    public function getInsuranceRate()
    {
        return $this->insuranceRate;
    }

    /**
     * @param int $insuranceRate
     */
    public function setInsuranceRate($insuranceRate)
    {
        $this->insuranceRate = $insuranceRate;
    }

    /**
     * @return int
     */
    public function getInsuranceNds()
    {
        return $this->insuranceNds;
    }

    /**
     * @param int $insuranceNds
     */
    public function setInsuranceNds($insuranceNds)
    {
        $this->insuranceNds = $insuranceNds;
    }

    /**
     * @return int
     */
    public function getNoticeRate()
    {
        return $this->noticeRate;
    }

    /**
     * @param int $noticeRate
     */
    public function setNoticeRate($noticeRate)
    {
        $this->noticeRate = $noticeRate;
    }

    /**
     * @return int
     */
    public function getNoticeNds()
    {
        return $this->noticeNds;
    }

    /**
     * @param int $noticeNds
     */
    public function setNoticeNds($noticeNds)
    {
        $this->noticeNds = $noticeNds;
    }

    /**
     * @return int
     */
    public function getOversizeRate()
    {
        return $this->oversizeRate;
    }

    /**
     * @param int $oversizeRate
     */
    public function setOversizeRate($oversizeRate)
    {
        $this->oversizeRate = $oversizeRate;
    }

    /**
     * @return int
     */
    public function getOversizeNds()
    {
        return $this->oversizeNds;
    }

    /**
     * @param int $oversizeNds
     */
    public function setOversizeNds($oversizeNds)
    {
        $this->oversizeNds = $oversizeNds;
    }

    /**
     * @return int
     */
    public function getWithFittingRate()
    {
        return $this->withFittingRate;
    }

    /**
     * @param int $withFittingRate
     */
    public function setWithFittingRate($withFittingRate)
    {
        $this->withFittingRate = $withFittingRate;
    }

    /**
     * @return int
     */
    public function getWithFittingNds()
    {
        return $this->withFittingNds;
    }

    /**
     * @param int $withFittingNds
     */
    public function setWithFittingNds($withFittingNds)
    {
        $this->withFittingNds = $withFittingNds;
    }
}
