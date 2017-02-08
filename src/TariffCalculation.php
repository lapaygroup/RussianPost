<?php
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Providers\Calculation;

class TariffCalculation
{
    use Singleton;

    /**
     * @param int $object_id - ID типа почтового отправления
     * @param array $params - массив данных по отправлению
     * @param array $services - массив ID услуг
     * @param string $date - дата расчета тарифа (необязательный параметр)
     * @return CalculateInfo результат расчета тарифа
     */
    public function calculate($object_id, $params, $services = [], $date=false)
    {
        if(empty($date)) $date = date('Ymd');
        $params['date'] = $date;

        $resultRaw = Calculation::getInstance()->getTariff($object_id, $params, $services);

        $calculateInfo = new CalculateInfo();
        $calculateInfo->setVersion($resultRaw['version']);
        $calculateInfo->setCategoryItemId($resultRaw['id']);
        $calculateInfo->setCategoryItemName($resultRaw['name']);
        $calculateInfo->setWeight($resultRaw['weight']);
        $calculateInfo->setTransportationID($resultRaw['transid']);
        $calculateInfo->setTransportationName($resultRaw['transname']);
        $calculateInfo->setPay($resultRaw['pay']);
        $calculateInfo->setPayNds($resultRaw['paynds']);
        $paymark = !empty($resultRaw['paymark']) ? $resultRaw['paymark'] : 0;
        $calculateInfo->setPayMark($paymark);
        $calculateInfo->setGround($resultRaw['ground']['val']);
        $calculateInfo->setGroundNds($resultRaw['ground']['valnds']);

        if(!empty($resultRaw['cover'])) {
            $calculateInfo->setCover($resultRaw['cover']['val']);
            $calculateInfo->setCoverNds($resultRaw['cover']['valnds']);
        } else {
            $calculateInfo->setCover(0);
            $calculateInfo->setCoverNds(0);
        }

        if(!empty($resultRaw['service'])) {
            $calculateInfo->setService($resultRaw['service']['val']);
            $calculateInfo->setServiceNds($resultRaw['service']['valnds']);
        } else {
            $calculateInfo->setService(0);
            $calculateInfo->setServiceNds(0);
        }

        foreach($resultRaw['tariff'] as $tariffInfo) {
            foreach($tariffInfo as $key => $paramInfo) {
                if(is_array($paramInfo)) {
                    $valMark = !empty($tariffInfo[$key]['valmark']) ? $tariffInfo[$key]['valmark'] : 0;
                    $val = !empty($tariffInfo[$key]['val']) ? $tariffInfo[$key]['val'] : 0;
                    $valNds = !empty($tariffInfo[$key]['valnds']) ? $tariffInfo[$key]['valnds'] : 0;
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

        return $calculateInfo;
    }
}