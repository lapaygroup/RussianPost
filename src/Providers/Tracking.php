<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\Exceptions\StatusValidationException;
use LapayGroup\RussianPost\Exceptions\TrackingException;
use LapayGroup\RussianPost\StatusList;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Tracking implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const WSDL_BASE_URI = 'https://tracking.pochta.ru';
    private const SERVICE_SINGLE = 'single';
    private const SERVICE_PACK = 'pack';
    private const NAMESPACE_DATA = 'http://russianpost.org/operationhistory/data';
    private const NAMESPACE_DATA1 = 'http://www.russianpost.org/RTM/DataExchangeESPP/Data';

    private string $login;
    private string $password;
    private string $service;
    private int $timeout;
    private \Closure $soapClientFactory;

    public \SoapClient $client;

    /**
     * Tracking constructor.
     *
     * @param $service
     * @param $config
     * @param int $timeout
     * @throws \SoapFault
     */
    public function __construct(
        string $service,
        array $config,
        int $timeout = 60,
        ?callable $soapClientFactory = null
    )
    {
        if ($timeout <= 0) {
            throw new \InvalidArgumentException('Таймаут должен быть больше нуля');
        }

        if (!isset($config['auth']['tracking']['login'], $config['auth']['tracking']['password'])) {
            throw new \InvalidArgumentException('Не заданы данные авторизации Tracking API');
        }

        $this->login = (string) $config['auth']['tracking']['login'];
        $this->password = (string) $config['auth']['tracking']['password'];
        $this->timeout = $timeout;
        $this->soapClientFactory = $soapClientFactory === null
            ? static fn (string $wsdl, array $options): \SoapClient => new \SoapClient($wsdl, $options)
            : \Closure::fromCallable($soapClientFactory);

        $this->createClient($service);
    }

    /**
     * @param $service
     * @throws \SoapFault
     */
    private function createClient(string $service): void
    {
        $this->service = $service;

        [$wsdlPath, $soapVersion] = match ($service) {
            self::SERVICE_SINGLE => ['/tracking-web-static/rtm34_wsdl.xml', SOAP_1_2],
            self::SERVICE_PACK => ['/tracking-web-static/fc_wsdl.xml', SOAP_1_1],
            default => throw new \InvalidArgumentException('Неизвестный режим Tracking API: ' . $service),
        };

        $client = ($this->soapClientFactory)(self::WSDL_BASE_URI . $wsdlPath, [
            'trace' => false,
            'soap_version' => $soapVersion,
            'use' => SOAP_LITERAL,
            'style' => SOAP_DOCUMENT,
            'connection_timeout' => $this->timeout,
        ]);

        if (!$client instanceof \SoapClient) {
            throw new \UnexpectedValueException('Фабрика SOAP-клиента должна вернуть экземпляр SoapClient');
        }

        $this->client = $client;
    }

    private function logExchange(string $operation): void
    {
        $this->logger?->info('Russian Post Tracking API request completed', [
            'operation' => $operation,
            'service' => $this->service,
        ]);
    }

    /**
     * Получение подробной информации обо всех операциях, совершенных над отправлением
     * @param $rpo - ШК отправления
     * @param string $lang - Язык названия операций (RUS, ENG)
     * @return \stdClass[]
     * @throws \SoapFault
     */
    public function getOperationsByRpo(string $rpo, string $lang = 'RUS'): array
    {
        // Если пакетный клиент, меняем на штучный
        if ($this->service === self::SERVICE_PACK) {
            $this->createClient(self::SERVICE_SINGLE);
        }

        $requestParams = new \SoapVar([
            new \SoapVar([
                new \SoapVar($rpo, XSD_STRING, null, null, 'Barcode', self::NAMESPACE_DATA),
                new \SoapVar(0, XSD_INT, null, null, 'MessageType', self::NAMESPACE_DATA),
                new \SoapVar($lang, XSD_STRING, null, null, 'Language', self::NAMESPACE_DATA),
            ], SOAP_ENC_OBJECT, null, null, 'OperationHistoryRequest', self::NAMESPACE_DATA),
            new \SoapVar([
                new \SoapVar($this->login, XSD_STRING, null, null, 'login', self::NAMESPACE_DATA),
                new \SoapVar($this->password, XSD_STRING, null, null, 'password', self::NAMESPACE_DATA),
            ], SOAP_ENC_OBJECT, null, null, 'AuthorizationHeader', self::NAMESPACE_DATA),
        ], SOAP_ENC_OBJECT);

        $response = $this->client->getOperationHistory($requestParams);

        $this->logExchange('getOperationHistory');

        $result = $response->OperationHistoryData;

        if (!isset($result->historyRecord)) return [];

        if (!is_array($result->historyRecord))
            $result->historyRecord = [$result->historyRecord];

        return !empty($result->historyRecord) ? $result->historyRecord : [];
    }

    /**
     * Получение информации об операциях с наложенным платежом, который связан с почтовым отправлением.
     * @param $rpo - ШК отправления
     * @param string $lang - Язык названия операций (RUS, ENG)
     * @return array
     * @throws \SoapFault
     */
    public function getNpayInfo(string $rpo, string $lang = 'RUS'): array
    {
        // Если пакетный клиент, меняем на штучный
        if ($this->service === self::SERVICE_PACK) {
            $this->createClient(self::SERVICE_SINGLE);
        }

        $requestParams = new \SoapVar([
                new \SoapVar([
                    new \SoapVar($this->login, XSD_STRING, null, null, 'login', self::NAMESPACE_DATA),
                    new \SoapVar($this->password, XSD_STRING, null, null, 'password', self::NAMESPACE_DATA),
                ], SOAP_ENC_OBJECT, null, null, 'AuthorizationHeader', self::NAMESPACE_DATA),
                new \SoapVar(
                    '<ns2:PostalOrderEventsForMailInput Barcode="'.$rpo.'" Language="'.$lang.'" />'
                , XSD_ANYXML, null, null, 'PostalOrderEventsForMailInput', self::NAMESPACE_DATA1),
        ], SOAP_ENC_OBJECT);


        $response = $this->client->PostalOrderEventsForMail($requestParams);

        $this->logExchange('PostalOrderEventsForMail');

        $result = $response->PostalOrderEventsForMaiOutput;

        if (!empty($result->PostalOrderEvent) && !is_array($result->PostalOrderEvent))
            $result->PostalOrderEvent = [$result->PostalOrderEvent];

        return !empty($result->PostalOrderEvent) ? $result->PostalOrderEvent : [];
    }

    /**
     * Создание запроса на получение информации о операциях с переданными отправлениями
     * @param $rpoList - массиш ШК отправлений
     * @param string $lang - Язык названия операций (RUS, ENG)
     * @return array
     * @throws \SoapFault
     */
    public function getTickets(array $rpoList, string $lang = 'RUS'): array
    {
        // Если штучный клиент, меняем на пакетный
        if ($this->service === self::SERVICE_SINGLE) {
            $this->createClient(self::SERVICE_PACK);
        }

        // Бьем по 500, если больше, то ловис HTTP Exception так как слишком большой размер ответа от ПРФ
        $rpoPack = array_chunk($rpoList, 500);
        $requestParams = new \stdClass();
        $requestParams->login = $this->login;
        $requestParams->password = $this->password;
        $requestParams->language = $lang;
        $requestParams->request = new \stdClass();

        $result['tickets'] = $result['not_create'] = [];

        foreach ($rpoPack as $rpoList) {
            $requestParams->Item = [];

            foreach ($rpoList as $rpo) {
                $item = new \stdClass();
                $item->Barcode = $rpo;
                $requestParams->request->Item[] = $item;
            }

            $response = $this->client->getTicket($requestParams);

            $this->logExchange('getTicket');

            if (!empty($response) && !empty($response->value)) {
                $result['tickets'][] = $response->value;
            } else {
                $result['not_create'] = array_merge($result['not_create'], $rpoList);
            }
        }

        return $result;
    }

    /**
     * Получение подробной информации обо всех операциях, совершенных над переданными отправлениями в тикете
     * @param $ticket
     * @return array
     * @throws TrackingException
     * @throws \SoapFault
     */
    public function getOperationsByTicket(string $ticket): array
    {
        // Если штучный клиент, меняем на пакетный
        if ($this->service === self::SERVICE_SINGLE) {
            $this->createClient(self::SERVICE_PACK);
        }

        $statusList = new StatusList();

        $requestParams = new \stdClass();
        $requestParams->login = $this->login;
        $requestParams->password = $this->password;
        $requestParams->ticket = $ticket;

        $response = $this->client->getResponseByTicket($requestParams);

        $this->logExchange('getResponseByTicket');

        if (!empty($response->error) || empty($response->value))
            throw new TrackingException('Ответ по тикету '.$ticket.' еще не готов.');

        $result = !is_array($response->value->Item) ? [$response->value->Item] : $response->value->Item;

        // Проставляем название подстатуса из справочника
        foreach ($result as $key => &$item) {
            if (empty($item->Operation)) continue;

            $rpo = (string)$item->Barcode;
            if (!is_array($item->Operation)) {
                $item = [$item->Operation];
            } else {
                $item = $item->Operation;
            }

            foreach ($item as &$operation) {
                try {
                    $statusInfo = $statusList->getInfo($operation->OperTypeID, $operation->OperCtgID);
                    $operation->OperCtgName = $statusInfo['substatusName'];
                    $operation->isFinal = $statusInfo['isFinal'];
                }

                catch (StatusValidationException $e) {
                    $operation->OperCtgName = $e->getMessage();
                    $operation->isFinal = false;
                }
            }

            $result[$rpo] = $item;
            unset($result[$key]);
        }

        return $result;
    }
}
