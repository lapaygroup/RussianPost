<?php
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Providers\Calculation;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class TariffCalculation implements LoggerAwareInterface
{
    use Singleton;
    use LoggerAwareTrait;

    /**
     * @param int $object_id - ID типа почтового отправления
     * @param array $params - массив данных по отправлению
     * @param array $services - массив ID услуг
     * @param string $date - дата расчета тарифа (необязательный параметр)
     * @return CalculateInfo результат расчета тарифа
     * @throws Exceptions\RussianPostException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function calculate($object_id, $params, $services = [], $date=false)
    {
        if (empty($date)) $date = date('Ymd');
        $params['date'] = $date;

        $calculation = new Calculation();
        if ($this->logger) {
            $calculation->setLogger($this->logger);
        }

        $resultRaw = $calculation->getTariff($object_id, $params, $services);

        $calculateInfo = new CalculateInfo();

        if (empty($resultRaw['error'])) {
            if (empty($resultRaw['transid'])) $resultRaw['transid'] = Null;
            if (empty($resultRaw['transname'])) $resultRaw['transname'] = Null;

            $calculateInfo->setVersion($resultRaw['version']);
            $calculateInfo->setCategoryItemId($resultRaw['id']);
            $calculateInfo->setCategoryItemName($resultRaw['name']);
            $calculateInfo->setWeight($resultRaw['weight']);
            $calculateInfo->setTransportationID($resultRaw['transid']);
            $calculateInfo->setTransportationName($resultRaw['transname']);
            $calculateInfo->setPay($resultRaw['pay']);
            $calculateInfo->setPayNds($resultRaw['paynds']);
            $paymark = ! empty($resultRaw['paymark']) ? $resultRaw['paymark'] : 0.00;
            $calculateInfo->setPayMark($paymark);
            if (! empty($resultRaw['ground'])) {
                $calculateInfo->setGround($resultRaw['ground']['val']);
                $calculateInfo->setGroundNds($resultRaw['ground']['valnds']);
            }
            if (! empty($resultRaw['cover'])) {
                $calculateInfo->setCover($resultRaw['cover']['val']);
                $calculateInfo->setCoverNds($resultRaw['cover']['valnds']);
            }

            if (! empty($resultRaw['service'])) {
                $calculateInfo->setService($resultRaw['service']['val']);
                $calculateInfo->setServiceNds($resultRaw['service']['valnds']);
            }

            foreach ($resultRaw['tariff'] as $tariffInfo) {
                foreach ($tariffInfo as $key => $paramInfo) {
                    if (is_array($paramInfo)) {
                        $valMark = ! empty($tariffInfo[$key]['valmark']) ? $tariffInfo[$key]['valmark'] : 0;
                        $val = ! empty($tariffInfo[$key]['val']) ? $tariffInfo[$key]['val'] : 0;
                        $valNds = ! empty($tariffInfo[$key]['valnds']) ? $tariffInfo[$key]['valnds'] : 0;
                    }
                }

                $calculateInfo->addTariff(new Tariff($tariffInfo['id'],
                        $tariffInfo['name'],
                        $val,
                        $valNds,
                        $valMark
                    )
                );

            }
        } else {
            $calculateInfo->setError($resultRaw['error']);
        }

        return $calculateInfo;
    }
}
