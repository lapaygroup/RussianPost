<?php
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\Exceptions\RussianPostTarrificatorException;
use LapayGroup\RussianPost\Providers\Calculation;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class TariffCalculation implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /** @var int  */
    private $timeout = 60;

    /**
     * Расчет тарифа V2
     *
     * @param int $object_id - ID типа почтового отправления
     * @param array $params - массив данных по отправлению
     * @param boolean $delivery_period - Считать ли сроки доставки
     * @param array $services - массив ID услуг
     * @param string $date - дата расчета тарифа (необязательный параметр)
     * @param int $timeout - время ожидания ответа от api (необязательный параметр)
     * @return CalculateInfo результат расчета тарифа
     * @throws RussianPostException
     * @throws RussianPostTarrificatorException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function calculate($object_id, $params, $delivery_period = false, $services = [], $date = false)
    {
        if (empty($date)) $date = date('Ymd');
        $params['date'] = $date;

        $calculation = new Calculation($this->timeout);
        if ($this->logger) {
            $calculation->setLogger($this->logger);
        }

        if (!$delivery_period) {
            $resultRaw = $calculation->getTariff($object_id, $params, $services);
        } else {
            $resultRaw = $calculation->getTariffAndDeliveryPeriod($object_id, $params, $services);
        }

        $resultRaw = $calculation->getTariff($object_id, $params, $services);

        $calculateInfo = new CalculateInfo();

        if (!empty($resultRaw['errors']))
            throw new RussianPostTarrificatorException('При расчета тарифа вернулась ошибка (см. $this->getErrors())', 0, $resultRaw['errors']);


        if (empty($resultRaw['transid'])) $resultRaw['transid'] = Null;
        if (empty($resultRaw['transname'])) $resultRaw['transname'] = Null;
        if (!isset($resultRaw['weight'])) $resultRaw['weight'] = Null;
        if (!isset($resultRaw['name'])) $resultRaw['name'] = Null;
        if (!isset($resultRaw['pay'])) $resultRaw['pay'] = Null;

        $calculateInfo->setVersion($resultRaw['version']);
        $calculateInfo->setCategoryItemId($resultRaw['id']);
        $calculateInfo->setCategoryItemName($resultRaw['name']);
        $calculateInfo->setWeight($resultRaw['weight']);
        $calculateInfo->setTransportationID($resultRaw['transid']);
        $calculateInfo->setTransportationName($resultRaw['transname']);
        $calculateInfo->setPay($resultRaw['pay']);
        $calculateInfo->setPayNds($resultRaw['paynds']);

        if (!empty($resultRaw['ground'])) {
            $calculateInfo->setGround($resultRaw['ground']['val']);
            $calculateInfo->setGroundNds($resultRaw['ground']['valnds']);
        }
        if (!empty($resultRaw['cover'])) {
            $calculateInfo->setCover($resultRaw['cover']['val']);
            $calculateInfo->setCoverNds($resultRaw['cover']['valnds']);
        }

        if (!empty($resultRaw['service'])) {
            $calculateInfo->setService($resultRaw['service']['val']);
            $calculateInfo->setServiceNds($resultRaw['service']['valnds']);
        }

        foreach ($resultRaw['items'] as $itemInfo) {
            if (!empty($itemInfo['tariff'])) {
                $val     = !empty($itemInfo['tariff']['val']) ? $itemInfo['tariff']['val'] : 0;
                $valNds  = !empty($itemInfo['tariff']['valnds']) ? $itemInfo['tariff']['valnds'] : 0;
                $calculateInfo->addTariff(new Tariff($itemInfo['tariff']['id'], $itemInfo['tariff']['name'], $val, $valNds));
            }

            if ($itemInfo['id'] == 5204 && !empty($itemInfo['delivery']['min']) && $itemInfo['delivery']['max']) {
                $calculateInfo->setDeliveryPeriodMin($itemInfo['delivery']['min']);
                $calculateInfo->setDeliveryPeriodMin($itemInfo['delivery']['max']);
            }

            if ($itemInfo['id'] == 5304 && !empty($itemInfo['delivery']['deadline'])) {
                $calculateInfo->setDeliveryDeadLine($itemInfo['delivery']['deadline']);
            }
        }

        return $calculateInfo;
    }

    /**
     * @return int
     */
    public function getTimeout()
    {
        return $this->timeout;
    }

    /**
     * @param int $timeout - таймаут ожидания ответа
     */
    public function setTimeout($timeout)
    {
        $this->timeout = $timeout;
    }
}
