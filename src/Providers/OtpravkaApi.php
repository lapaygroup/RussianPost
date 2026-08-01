<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\AddressList;
use LapayGroup\RussianPost\Entity\Order;
use LapayGroup\RussianPost\Entity\Recipient;
use LapayGroup\RussianPost\Entity\ReturnShipment;
use LapayGroup\RussianPost\Enum\OpsObjectType;
use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\FioList;
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\PhoneList;
use LapayGroup\RussianPost\TariffInfo;
use LapayGroup\RussianPost\ParcelInfo;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class OtpravkaApi implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const V1           = '1.0';
    const V2           = '2.0';
    const DELIVERY_VERSION  = 'v1';
    const PRINT_TYPE_PAPER  = 'PAPER';
    const PRINT_TYPE_THERMO = 'THERMO';
    const PRINT_ONE_SIDED   = 'ONE_SIDED';
    const PRINT_TWO_SIDED   = 'TWO_SIDED';
    /** @deprecated Начиная с 2.0.0 SDK всегда возвращает UploadedFileInterface. */
    const DOWNLOAD_FILE     = 1;
    const PRINT_FILE        = 2;
    const OTPRAVKA          = 1; // Endpoint отправки
    const DELIVERY          = 2; // Endpoint сроков доставки
    const POSTOFFICE        = 3; // Endpoint работы с ОПС

    private readonly string $token;
    private readonly string $key;

    public function __construct(array $config, private readonly Psr18Transport $httpTransport)
    {
        if (!isset($config['auth']['otpravka']['token'], $config['auth']['otpravka']['key'])) {
            throw new \InvalidArgumentException('Не заданы данные авторизации Otpravka API');
        }

        $this->token = (string) $config['auth']['otpravka']['token'];
        $this->key = (string) $config['auth']['otpravka']['key'];
    }

    /**
     * Инициализирует вызов к API
     *
     * @param string $method - метод API
     * @param array $params - параметры запроса
     * @param int|null $endpoint - идентификатор адреса API
     * @return array|string|UploadedFileInterface
     * @throws RussianPostException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function callApi(
        string $type,
        string $method,
        array $params = [],
        string $version = self::V1,
        ?int $endpoint = null
    ): array|string|UploadedFileInterface
    {
        if (empty($endpoint)) $endpoint = self::OTPRAVKA;

        switch ($endpoint) {
            case self::OTPRAVKA:
                $baseUri = 'https://otpravka-api.pochta.ru/';
                $headers = [
                    'Authorization' => 'AccessToken ' . $this->token,
                    'X-User-Authorization' => 'Basic ' . $this->key,
                    'Content-Type' => 'application/json'
                ];
                break;

            case self::DELIVERY:
                $version = self::DELIVERY_VERSION;
                $baseUri = 'https://delivery.pochta.ru/delivery/';
                $headers = ['Content-Type' => 'application/json'];
                break;

            case self::POSTOFFICE:
                $version = self::V1;
                $baseUri = 'https://otpravka-api.pochta.ru/postoffice/';
                $headers = [
                    'Authorization' => 'AccessToken ' . $this->token,
                    'X-User-Authorization' => 'Basic ' . $this->key,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json;charset=UTF-8'
                ];
                break;

            default:
                throw new \InvalidArgumentException('Неизвестный endpoint API Почты России');
        }

        $uri = $baseUri . $version . '/' . $method;

        switch ($type) {
            case 'GET':
                $request = http_build_query($params);
                $this->logRequest($type, $uri);
                $response = $this->httpTransport->send($type, $uri, $headers, $params);
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                try {
                    $request = json_encode($params, JSON_THROW_ON_ERROR);
                } catch (\JsonException $exception) {
                    throw new RussianPostException(
                        'Не удалось сериализовать параметры запроса: ' . $exception->getMessage(),
                        0,
                        null,
                        var_export($params, true),
                        $exception
                    );
                }
                $this->logRequest($type, $uri);

                /** @var ResponseInterface $response */
                $response = $this->httpTransport->send($type, $uri, $headers, [], $request);
                break;
            default:
                throw new \InvalidArgumentException('Неподдерживаемый HTTP-метод: ' . $type);
        }

        $content_type = $response->getHeaderLine('Content-Type');

        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300
            && preg_match('~^application/(pdf|zip|octet-stream)~', $content_type, $matches_type)) {
            $this->logResponse($type, $uri, $response->getStatusCode(), $content_type);

            $body = $response->getBody();
            if ($body->isSeekable()) $body->rewind();

            preg_match('~=(.+)~', $response->getHeaderLine('Content-Disposition'), $matches_name);
            $fileName = !empty($matches_name[1]) ? trim($matches_name[1], "\"' ") : 'document';

            return $this->httpTransport->createUploadedFile(
                $body,
                $body->getSize(),
                "{$fileName}.{$matches_type[1]}",
                $response->getHeaderLine('Content-Type')
            );
        }

        $response_contents = $response->getBody()->getContents();

        $this->logResponse($type, $uri, $response->getStatusCode(), $content_type);

        if ($response_contents === '') {
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' пришел пустой ответ', $response->getStatusCode(), $response_contents, $request);
        }

        try {
            $resp = json_decode($response_contents, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            if ($endpoint === self::DELIVERY && $response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                return $response_contents;
            }

            throw new RussianPostException(
                'От сервера Почты России при вызове метода ' . $method . ' получен некорректный JSON',
                $response->getStatusCode(),
                $response_contents,
                $request,
                $exception
            );
        }

        if (!is_array($resp)) {
            throw new RussianPostException(
                'От сервера Почты России при вызове метода ' . $method . ' получен ответ неожиданного формата',
                $response->getStatusCode(),
                $response_contents,
                $request
            );
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            $description = $resp['sub-code'] ?? $resp['desc'] ?? $resp['message'] ?? $resp['error'] ?? 'HTTP ' . $response->getStatusCode();
            $errorCode = $resp['code'] ?? $resp['status'] ?? $response->getStatusCode();
            throw new RussianPostException(
                'От сервера Почты России при вызове метода ' . $method . ' получена ошибка: ' . $description . ' (' . $errorCode . ')',
                $response->getStatusCode(),
                $response_contents,
                $request
            );
        }

        return $resp;
    }

    private function logRequest(string $method, string $uri): void
    {
        $this->logger?->info('Russian Post Otpravka API request', [
            'method' => $method,
            'uri' => $uri,
        ]);
    }

    private function logResponse(string $method, string $uri, int $statusCode, string $contentType): void
    {
        $this->logger?->info('Russian Post Otpravka API response', [
            'method' => $method,
            'uri' => $uri,
            'http_status' => $statusCode,
            'content_type' => $contentType,
        ]);
    }

    /**
     * Действие с файлом
     *
     * @param UploadedFileInterface $file
     * @param int $action
     * @return UploadedFileInterface
     */
    private function fileAction(UploadedFileInterface $file, int $action): UploadedFileInterface
    {
        if (!in_array($action, [self::DOWNLOAD_FILE, self::PRINT_FILE], true)) {
            throw new \InvalidArgumentException('Неизвестное действие с файлом');
        }

        return $file;
    }

    /**
     * Расчет стоимости пересылки
     *
     * @param ParcelInfo $parcelInfo
     * @return TariffInfo
     * @throws RussianPostException
     */
    public function getDeliveryTariff($parcelInfo)
    {
        $response = $this->callApi('POST', 'tariff', $parcelInfo->getArray());
        return new TariffInfo($response);
    }

    /**
     * Нормализация адресов
     *
     * @param AddressList $addressList - адреса для нормализации
     * @return array ответ API ПРФ
     * @throws RussianPostException
     */
    public function clearAddress($addressList)
    {
        return $this->callApi('POST', 'clean/address',  iterator_to_array($addressList->getIterator()));
    }

    /**
     * Нормализация ФИО
     *
     * @param FioList $fioList
     * @return array ответ API ПРФ
     * @throws RussianPostException
     */
    public function clearFio($fioList)
    {
        return $this->callApi('POST', 'clean/physical',  iterator_to_array($fioList->getIterator()));
    }

    /**
     * Нормализация телефона
     *
     * @param PhoneList $phoneList
     * @return array ответ API ПРФ
     * @throws RussianPostException
     */
    public function clearPhone(PhoneList $phoneList)
    {
        return $this->callApi('POST', 'clean/phone',  iterator_to_array($phoneList->getIterator()));
    }

    /**
     * Текущие точки сдачи отправлений
     *
     * @return mixed
     * @throws RussianPostException
     */
    public function shippingPoints()
    {
        return $this->callApi('GET', 'user-shipping-points');
    }

    /**
     * Текущие настройки пользователя
     *
     * @return mixed
     * @throws RussianPostException
     */
    public function settings()
    {
        return $this->callApi('GET', 'settings');
    }

    /**
     * Получение баланса
     *
     * @return mixed
     * @throws RussianPostException
     */
    public function getBalance()
    {
        return $this->callApi('GET', 'counterpart/balance');
    }

    /**
     * Взвращает информацию о благонадежности получателя
     *
     * @param Recipient $recipient
     * @return array
     * @throws RussianPostException
     */
    public function untrustworthyRecipient($recipient)
    {
        $params[] = $recipient->getParams();
        $result = $this->callApi('POST', 'unreliable-recipient', $params);
        return array_shift($result);
    }


    /**
     * Взвращает информацию о благонадежности списка получателей
     *
     * @param array $recipients - массив объектов Recipient
     * @return array
     * @throws RussianPostException
     */
    public function untrustworthyRecipients($recipients)
    {
        $params = [];
        foreach ($recipients as $recipient) {
            $params[] = $recipient->getParams();
        }

        return $this->callApi('POST', 'unreliable-recipient', $params);
    }

    /**
     * Создание заказов
     *
     * @param Order[] $orders - массив объектов Order
     * @return array
     * @throws RussianPostException
     */
    public function createOrders($orders)
    {
        return $this->callApi('PUT', 'user/backlog', $orders);
    }

    /**
     * Создание заказов V2
     *
     * @param Order[] $orders - массив объектов Order
     * @return array
     * @throws RussianPostException
     */
    public function createOrdersV2($orders)
    {
        return $this->callApi('PUT', 'user/backlog', $orders, self::V2);
    }


    /**
     * Редактирование заказа
     *
     * @param Order $order - данные заказа
     * @param $id - id заказа в системе Почты России
     * @return array|string|UploadedFileInterface
     * @throws RussianPostException
     */
    public function editOrder($order, $id)
    {
        return $this->callApi('PUT', 'backlog/'.$id, $order->asArr());
    }

    /**
     * Поиск заказа по идентификатору
     *
     * @param int $id - id заказа Почты России
     * @return array
     * @throws RussianPostException
     */
    public function findOrderById($id)
    {
        return $this->callApi('GET', 'backlog/'.$id);
    }

    /**
     * Поиска заказа по назначенному магазином идентификатору
     *
     * @param string $order_num - номер заказа магазина
     * @return array
     * @throws RussianPostException
     */
    public function findOrderByShopId($order_num)
    {
        return $this->callApi('GET', 'backlog/search', ['query' => $order_num]);
    }

    /**
     * Поиск заказов с ШПИ (В партии)
     *
     * @param $rpo - ШПИ (РПО) заказа
     * @return array
     * @throws RussianPostException
     */
    public function findOrderByRpo($rpo)
    {
        return $this->callApi('GET', 'shipment/search', ['query' => $rpo]);
    }

    /**
     * Поиск заказа в партии по внутреннему id
     *
     * @param int $id - id заказа Почты России
     * @return array
     * @throws RussianPostException
     */
    public function findOrderInBatch($id)
    {
        return $this->callApi('GET', 'shipment/'.$id);
    }

    /**
     * Удаление заказов
     *
     * @param array $id_list - массив id заказов
     * @return array
     * @throws RussianPostException
     */
    public function deleteOrders($id_list)
    {
        return $this->callApi('DELETE', 'backlog', $id_list);
    }

    /**
     * Возврат заказов в "Новые"
     *
     * @param array $id_list - массив id заказов
     * @return array
     * @throws RussianPostException
     */
    public function returnToNew($id_list)
    {
        return $this->callApi('POST', 'user/backlog', $id_list);
    }

    /**
     * Создание партии из N заказов
     *
     * @param array $id_list - массив id заказов
     * @param \DateTimeImmutable $sending_date - Дата отправки
     * @param boolean $use_online_balance - Признак использования онлайн баланса
     * @return array
     * @throws RussianPostException
     */
    public function createBatch($id_list, $sending_date = null, $use_online_balance = false)
    {
        $method = 'user/shipment';
        if ($sending_date)
            $method .= '?sending-date='.$sending_date->format('Y-m-d').'&use-online-balance='.$use_online_balance;

        return $this->callApi('POST', $method, $id_list);
    }

    /**
     * Поиск всех партий
     *
     * @param string|null $mail_type
     * @param string|null $mail_category
     * @param int|null $size
     * @param string $sort
     * @param int|null $page
     * @return array
     * @throws RussianPostException
     */
    public function getAllBatches($mail_type = null, $mail_category = null, $size = null, $sort = 'ask', $page = null)
    {
        $params = [];
        $params['sort'] = $sort;
        if (!empty($mail_type)) $params['mailType'] = $mail_type;
        if (!empty($mail_category)) $params['mailCategory'] = $mail_category;
        if (!empty($size)) $params['size'] = $size;
        if (!empty($page)) $params['page'] = $page;

        return $this->callApi('GET', 'batch', $params);
    }

    /**
     * Перенос заказов в ранее созданную партию
     *
     * @param string $batch_name - название партии
     * @param array $id_list - массив id заказов
     * @return array
     * @throws RussianPostException
     */
    public function moveOrdersToBatch($batch_name, $id_list)
    {
        $method = 'batch/'.$batch_name.'/shipment';
        return $this->callApi('POST', $method, $id_list);
    }


    /**
     * Поиск партии по наименованию
     *
     * @param string $batch_name - название партии
     * @return array
     * @throws RussianPostException
     */
    public function findBatchByName($batch_name)
    {
        return $this->callApi('GET', 'batch/'.$batch_name);
    }

    /**
     * Создание заказов и добавление их в партию
     *
     * @param string $batch_name - название партии
     * @param array $orders - заказы (массив объектов Order)
     * @return array
     * @throws RussianPostException
     */
    public function addOrdersToBatch($batch_name, $orders)
    {
        return $this->callApi('PUT', 'batch/'.$batch_name.'/shipment', $orders);
    }

    /**
     * Удаление заказов из партии
     *
     * @param array $id_list - массив id заказов
     * @return array
     * @throws RussianPostException
     */
    public function deleteOrdersInBatch($id_list)
    {
        return $this->callApi('DELETE', 'shipment', $id_list);
    }

    /**
     * Запрос данных о заказах в партии
     *
     * @param string $batch_name
     * @param int|null $size
     * @param string $sort
     * @param int|null $page
     * @return array
     * @throws RussianPostException
     */
    public function getOrdersInBatch($batch_name, $size = null, $sort = 'ask', $page = null)
    {
        $params = [];
        $params['sort'] = $sort;
        if (!empty($size)) $params['size'] = $size;
        if (!empty($page)) $params['page'] = $page;

        return $this->callApi('GET', 'batch/'.$batch_name.'/shipment', $params);
    }


    /**
     * Изменение дня отправки в почтовое отделение
     *
     * @param string $batch_name - наименование партии
     * @param \DateTimeImmutable $date - дата отправки
     * @return bool
     * @throws RussianPostException
     * @throws \InvalidArgumentException
     */
    public function changeBatchSendingDay(string $batch_name, \DateTimeImmutable $date): bool
    {
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');
        $result = $this->callApi('POST', 'batch/'.$batch_name.'/sending/'.$year.'/'.$month.'/'.$day);
        if (empty($result)) return true;

        $errorCode = is_array($result) ? ($result['error-code'] ?? 'UNKNOWN_ERROR') : 'UNEXPECTED_RESPONSE';
        throw new \InvalidArgumentException((string) $errorCode);
    }

    /**
     * Запрос данных о партиях в архиве
     *
     * @return array
     * @throws RussianPostException
     */
    public function getArchivedBatches()
    {
        return $this->callApi('GET', 'archive');
    }

    /**
     * Перевод списка партий в архив
     *
     * @param array $batch_name_list - массив названий партий
     * @return array
     * @throws RussianPostException
     */
    public function archivingBatch($batch_name_list)
    {
        return $this->callApi('PUT', 'archive', $batch_name_list);
    }

    /**
     * Возврат списка партий из архива
     *
     * @param array $batch_name_list - массив названий партий
     * @return array
     * @throws RussianPostException
     */
    public function unarchivingBatch($batch_name_list)
    {
        return $this->callApi('POST', 'archive/revert', $batch_name_list);
    }

    /**
     * Генерация пакета документации по партии
     *
     * @param string $batch_name - наименование партии
     * @param int $action - действие с файлом (Печать или сохранение)
     * @param string $print_type - тип печати (термо или на бумаге)
     * @param string $print_type_form - тип печати уведомления (двусторонняя или односторонняя)
     * @return string|UploadedFileInterface
     * @throws RussianPostException
     */
    public function generateDocPackage($batch_name, $action = self::PRINT_FILE, $print_type = self::PRINT_TYPE_PAPER, $print_type_form = self::PRINT_ONE_SIDED)
    {
        $file = $this->callApi('GET', 'forms/'.$batch_name.'/zip-all', [
            'print-type' => $print_type,
            'print-type-from' => $print_type_form
        ]);
        return $this->fileAction($file, $action);
    }

    /**
     * Генерация печатной формы Ф7п
     *
     * @param int $order_id - уникальный идентификатор заказа
     * @param int $action - действие с файлом
     * @param \DateTimeImmutable $sending_date
     * @param string $print_type
     * @return string|UploadedFileInterface
     * @throws RussianPostException
     */
    public function generateDocF7p($order_id, $action, $sending_date = null, $print_type = self::PRINT_TYPE_PAPER)
    {
        $file = $this->callApi('GET', 'forms/'.$order_id.'/f7pdf', [
            'print-type' => $print_type,
            'sending-date' => $sending_date->format('Y-m-d')
        ]);
        return $this->fileAction($file, $action);
    }

    /**
     * Генерация печатной формы Ф112ЭК
     *
     * @param int $order_id - уникальный идентификатор заказа
     * @param int $action - действие с файлом
     * @return string|UploadedFileInterface
     * @throws RussianPostException
     */
    public function generateDocF112ek($order_id, $action = self::PRINT_FILE)
    {
        $file = $this->callApi('GET', 'forms/'.$order_id.'/f112pdf');
        return $this->fileAction($file, $action);
    }

    /**
     * Генерация печатных форм для заказа
     *
     * @param int $order_id - уникальный идентификатор заказа
     * @param int $action - действие с файлом
     * @param bool $batch - false (до формирования партии), true (после формирования партии)
     * @param \DateTimeImmutable $sending_date
     * @param string $print_type - тип печати (термопечать или на бумаге)
     * @return string|UploadedFileInterface
     * @throws RussianPostException
     */
    public function generateDocOrderPrintForm($order_id, $action, $batch = true, $sending_date = null, $print_type = null)
    {
        if ($batch)
            $uri = 'forms/'.$order_id.'/forms';
        else
            $uri = 'forms/backlog/'.$order_id.'/forms';

        $file = $this->callApi('GET', $uri, [
            'sending-date' => $sending_date->format('Y-m-d'),
            'print-type' => $print_type
        ]);
        return $this->fileAction($file, $action);
    }

    /**
     * Генерация печатной формы Ф103
     *
     * @param string $batch_name - наименование партии
     * @param int $action - действие с файлом
     * @return string|UploadedFileInterface
     * @throws RussianPostException
     */
    public function generateDocF103($batch_name,  $action = self::PRINT_FILE)
    {
        $file = $this->callApi('GET', 'forms/'.$batch_name.'/f103pdf');
        return $this->fileAction($file, $action);
    }

    /**
     * Генерация печатной формы акта осмотра содержимого
     *
     * @param string $batch_name - наименование партии
     * @param int $action - действие с файлом
     * @return string|UploadedFileInterface
     * @throws RussianPostException
     */
    public function generateDocCheckingAct($batch_name,  $action = self::PRINT_FILE)
    {
        $file = $this->callApi('GET', 'forms/'.$batch_name.'/completeness-checking-form');
        return $this->fileAction($file, $action);
    }

    /**
     * Подготовка и отправка электронной формы Ф103
     *
     * @param string $batch_name - наименование партии
     * @param boolean $online_balance - признак использования онлайн баланса
     * @return boolean
     * @throws RussianPostException
     */
    public function sendingF103form(string $batch_name, bool $online_balance = false): bool
    {
        $method = 'batch/'.$batch_name.'/checkin';
        if ($online_balance)
            $method .= '?useOnlineBalance=true';

        $response = $this->callApi('POST', $method);
        if (!empty($response['f103-sent'])) return true;

        if (!empty($response['error-code'])) {
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$response['error-code'].' (см. https://otpravka.pochta.ru/specification#/enums-errors)');
        }

        throw new RussianPostException('От сервера Почты России при вызове метода ' . $method . ' получен ответ неожиданного формата');
    }

    /**
     * Генерирует и возвращает PDF-файл возвратного ярлыка на одной печатной странице
     *
     * @param string $rpo - ШПИ отправления
     * @param int $action - действие с файлом
     * @param string $print_type - тип печати (термо или на бумаге)
     * @return string|UploadedFileInterface
     * @throws RussianPostException
     */
    public function generateReturnLabel($rpo, $action = self::PRINT_FILE, $print_type = self::PRINT_TYPE_PAPER)
    {
        $file = $this->callApi('GET', 'forms/'.$rpo.'/easy-return-pdf');
        return $this->fileAction($file, $action);
    }

    /**
     * Поиск почтового отделения по индексу
     *
     * @param int $postal_code - Индекс почтового отделения
     * @param null|string $latitude - Широта
     * @param null|string $longitude - Долгота
     * @param \DateTimeImmutable $current_date - Текущее время
     * @param bool $filter_by_office_type - Фильтр по типам объектов в ответе. true: ГОПС, СОПС, ПОЧТОМАТ. false: все. Значение по-умолчанию - true.
     * @param bool $ufps_postal_code - true: добавлять в ответ индекс УФПС для найдённого отделения, false: не добавлять. Значение по-умолчанию: false.
     * @return array
     * @throws RussianPostException
     */
    public function searchPostOfficeByIndex($postal_code, $latitude = null, $longitude = null, $current_date = null, $filter_by_office_type = true, $ufps_postal_code = false)
    {
        $params = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'filter-by-office-type' => $filter_by_office_type,
            'ufps-postal-code' => $ufps_postal_code
        ];

        if ($current_date)
            $params['current-date-time'] = (new \DateTimeImmutable())->format('c');

        return $this->callApi('GET', (string)$postal_code, $params, self::V1, self::POSTOFFICE);
    }

    /**
     * Поиск обслуживающего ОПС по адресу (Следует учесть, что чем точнее адрес, тем точнее будет поиск.)
     *
     * @param string $address - Строка с адресом
     * @param int $count - Количество ближайших почтовых отделений в результате поиска
     * @return array
     * @throws RussianPostException
     */
    public function searchPostOfficeByAddress($address, $count = 3)
    {
        return $this->callApi('GET', 'by-address', [
            'address' => $address,
            'top' => $count
        ], self::V1, self::POSTOFFICE);
    }

    /**
     * Поиск почтовых отделений по координатам
     *
     * @param array $params - массив параметров запроса согласно документации https://otpravka.pochta.ru/specification#/services-postoffice-nearby
     * @return array
     * @throws RussianPostException
     */
    public function searchPostOfficeByCoordinates($params)
    {
        return $this->callApi('GET', 'nearby', $params, self::V1, self::POSTOFFICE);
    }

    /**
     * Поиск почтовых сервисов ОПС с возможностью фильтра по группе сервисов
     * Может возвращать как все доступные сервисы, так и сервисы определенной группы (например: Киберпочт@)
     *
     * @param int $post_code - Индекс почтового отделения
     * @param string $service_group - Идентификатор группы сервисов
     * @return array
     * @throws RussianPostException
     */
    public function getPostOfficeServices($post_code, $service_group = null)
    {
        $method = $post_code.'/services';
        if ($service_group) $method .= '/'.$service_group;

        return $this->callApi('GET', $method, [], self::V1, self::POSTOFFICE);
    }

    /**
     * Поиск почтовых индексов в населённом пункте
     *
     * @param string $locality - Название населённого пункта (например Екатеринбург)
     * @param string $region - Область/край/республика, где расположен населённый пункт (например Свердловская)
     * @param string $district - Район, где расположен населённый пункт (для деревень, посёлков и т. д. - например Сухоложский)
     * @return array
     * @throws RussianPostException
     */
    public function getPostalCodesInLocality(string $locality, string $region = '', string $district = ''): array
    {
        return $this->callApi('GET', 'settlement.offices.codes', [
            'settlement' => $locality,
            'region' => $region,
            'district' => $district
        ], self::V1, self::POSTOFFICE);
    }

    /**
     * Выгружает данные ОПС, ПВЗ, Почтоматов из Паспорта ОПС.
     * Генерирует и возвращает zip архив с текстовым файлом TYPEdd_MMMM_yyyy.txt, где:
     * TYPE - тип объекта
     * dd_MMMM_yyyy - время создания архива
     *
     * @param string $type - Тип объекта
     * @return UploadedFileInterface
     * @throws RussianPostException
     */
    public function getPostOfficeFromPassport($type = OpsObjectType::ALL)
    {
        return $this->callApi('GET', 'unloading-passport/zip', ['type' => $type]);
    }

    /**
     * Создание возвратного отправления для ранее созданного отправления
     *
     * @param string $rpo - ШПИ отправления
     * @param string $mail_type - Вид РПО (https://otpravka.pochta.ru/specification#/enums-base-mail-type)
     * @return array
     * @throws RussianPostException
     */
    public function returnShipment($rpo, $mail_type = 'UNDEFINED')
    {
        return $this->callApi('PUT', 'returns',
            [
                'direct-barcode' => $rpo,
                'mail-type' => $mail_type
            ]
        );
    }

    /**
     * Создание отдельного возвратного отправления
     *
     * @param ReturnShipment[] $return_shipments - массив объектов ReturnShipment
     * @return array
     * @throws RussianPostException
     */
    public function createReturnShipment($return_shipments)
    {
        return $this->callApi('PUT', 'returns/return-without-direct', $return_shipments);
    }

    /**
     * Удаляет отдельное возвратное отправление
     *
     * @param string $rpo - ШПИ возвратного отправления
     * @return array
     * @throws RussianPostException
     */
    public function deleteReturnShipment($rpo)
    {
        return $this->callApi('DELETE', 'returns/delete-separate-return?barcode='.$rpo);
    }

    /**
     * Редактирование отдельного возвратного отправления
     *
     * @param ReturnShipment $return_shipment - данные заказа
     * @param string $rpo - ШПИ возвратного отправления
     * @return array|string
     * @throws RussianPostException
     */
    public function editReturnShipment($return_shipment, $rpo)
    {
        return $this->callApi('POST', 'returns/'.$rpo, $return_shipment->asArr());
    }
}
