<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\Exceptions\RussianPostTarifficatorException;
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Providers\Calculation;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class TariffCalculation implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private readonly Calculation $calculation;

    public function __construct(Psr18Transport $httpTransport)
    {
        $this->calculation = new Calculation($httpTransport);
    }

    /**
     * Расчет тарифа V2
     *
     * @param int $object_id - ID типа почтового отправления
     * @param array $params - массив данных по отправлению
     * @param boolean $delivery_period - Считать ли сроки доставки
     * @param array $services - массив ID услуг
     * @param string $date - дата расчета тарифа (необязательный параметр)
     * @return CalculateInfo результат расчета тарифа
     * @throws RussianPostException
     * @throws RussianPostTarifficatorException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function calculate(
        int $object_id,
        array $params,
        bool $delivery_period = false,
        array $services = [],
        string|\DateTimeInterface|null $date = null
    ): CalculateInfo
    {
        $params['date'] = $date instanceof \DateTimeInterface
            ? $date->format('Ymd')
            : ($date ?? date('Ymd'));

        if ($this->logger) {
            $this->calculation->setLogger($this->logger);
        }

        if (!$delivery_period) {
            $resultRaw = $this->calculation->getTariff($object_id, $params, $services);
        } else {
            $resultRaw = $this->calculation->getTariffAndDeliveryPeriod($object_id, $params, $services);
        }

        $calculateInfo = new CalculateInfo();

        if (!empty($resultRaw['errors'])) {
            throw new RussianPostTarifficatorException(
                'При расчёте тарифа вернулась ошибка (см. getErrors())',
                0,
                $resultRaw['errors']
            );
        }

        foreach (['version', 'id', 'paynds', 'items'] as $requiredField) {
            if (!array_key_exists($requiredField, $resultRaw)) {
                throw new RussianPostTarifficatorException(
                    'В ответе тарификатора отсутствует обязательное поле: ' . $requiredField
                );
            }
        }

        if (!is_array($resultRaw['items'])) {
            throw new RussianPostTarifficatorException('Поле items в ответе тарификатора должно быть массивом');
        }

        $calculateInfo->setVersion((string) $resultRaw['version']);
        $calculateInfo->setCategoryItemId((int) $resultRaw['id']);
        $calculateInfo->setCategoryItemName(isset($resultRaw['name']) ? (string) $resultRaw['name'] : null);
        $calculateInfo->setWeight(isset($resultRaw['weight']) ? (int) $resultRaw['weight'] : null);
        $calculateInfo->setTransportationID(isset($resultRaw['transid']) ? (int) $resultRaw['transid'] : null);
        $calculateInfo->setTransportationName(isset($resultRaw['transname']) ? (string) $resultRaw['transname'] : null);
        $calculateInfo->setPay($resultRaw['pay'] ?? null);
        $calculateInfo->setPayNds($resultRaw['paynds']);

        if (isset($resultRaw['ground']) && is_array($resultRaw['ground'])) {
            $calculateInfo->setGround($resultRaw['ground']['val'] ?? null);
            $calculateInfo->setGroundNds($resultRaw['ground']['valnds'] ?? null);
        }
        if (isset($resultRaw['cover']) && is_array($resultRaw['cover'])) {
            $calculateInfo->setCover($resultRaw['cover']['val'] ?? null);
            $calculateInfo->setCoverNds($resultRaw['cover']['valnds'] ?? null);
        }

        if (isset($resultRaw['service']) && is_array($resultRaw['service'])) {
            $calculateInfo->setService($resultRaw['service']['val'] ?? null);
            $calculateInfo->setServiceNds($resultRaw['service']['valnds'] ?? null);
        }

        if (isset($resultRaw['delivery']) && is_array($resultRaw['delivery'])) {
            $calculateInfo->setDeliveryPeriodMin(isset($resultRaw['delivery']['min']) ? (int) $resultRaw['delivery']['min'] : null);
            $calculateInfo->setDeliveryPeriodMax(isset($resultRaw['delivery']['max']) ? (int) $resultRaw['delivery']['max'] : null);
            $calculateInfo->setDeliveryDeadLine(isset($resultRaw['delivery']['deadline']) ? (string) $resultRaw['delivery']['deadline'] : null);
        }

        foreach ($resultRaw['items'] as $itemInfo) {
            if (isset($itemInfo['tariff']) && is_array($itemInfo['tariff'])) {
                $calculateInfo->addTariff(new Tariff(
                    (int) $itemInfo['id'],
                    (string) $itemInfo['name'],
                    $itemInfo['tariff']['val'] ?? 0,
                    $itemInfo['tariff']['valnds'] ?? 0,
                    $itemInfo['tariff']['valmark'] ?? 0
                ));
            }
        }

        return $calculateInfo;
    }
}
