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
        $calculateInfo->setPayMark($resultRaw['paymark']);

        foreach($resultRaw['tariff'] as $tariffInfo) {
            $calculateInfo->addTariff(new Tariff($tariffInfo['id'],
                                                        $tariffInfo['name'],
                                                        $tariffInfo['ground']['val'],
                                                        $tariffInfo['ground']['valnds'],
                                                        $tariffInfo['ground']['valmark']
                                                        )
                                      );

        }

        return $calculateInfo;
    }
}