<?php
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\AddressList;
use LapayGroup\RussianPost\Entity\Order;
use LapayGroup\RussianPost\Entity\Recipient;
use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\FioList;
use LapayGroup\RussianPost\PhoneList;
use LapayGroup\RussianPost\TariffInfo;
use LapayGroup\RussianPost\ParcelInfo;
use GuzzleHttp\Psr7\UploadedFile;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class OtpravkaApi implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    const VERSION = '1.0';
    const DELIVERY_VERSION = 'v1';
    const PRINT_TYPE_PAPER  = 'PAPER';
    const PRINT_TYPE_THERMO = 'THERMO';
    const PRINT_ONE_SIDED = 'ONE_SIDED';
    const PRINT_TWO_SIDED = 'TWO_SIDED';
    const DOWNLOAD_FILE = 1;
    const PRINT_FILE = 2;

    /** @var \GuzzleHttp\Client  */
    private $otpravkaClient;

    /** @var \GuzzleHttp\Client  */
    private $deliveryClient;

    function __construct($config, $timeout = 60)
    {
        $this->otpravkaClient = new \GuzzleHttp\Client([
            'base_uri' => 'https://otpravka-api.pochta.ru/'.self::VERSION.'/',
            'headers' => ['Authorization' => 'AccessToken '. $config['auth']['otpravka']['token'],
                          'X-User-Authorization' => 'Basic '.$config['auth']['otpravka']['key'],
                          'Content-Type' => 'application/json',
                          'Accept' => 'application/json;charset=UTF-8'
            ],
            'timeout' => $timeout,
            'http_errors' => false
        ]);

        $this->deliveryClient = new \GuzzleHttp\Client([
            'base_uri'=>'https://delivery.pochta.ru/delivery/'.self::DELIVERY_VERSION.'/',
            'timeout' => $timeout,
            'http_errors' => false
        ]);
    }

    /**
     * Инициализирует вызов к API
     *
     * @param $method
     * @param $params
     * @return array | string | UploadedFile
     * @throws RussianPostException
     */
    private function callApi($type, $method, $params = [], $endpoint = false)
    {
        $is_file = false;

        switch ($endpoint) {
            case 'otpravka':
                $client = $this->otpravkaClient;
                break;

            case 'delivery':
                $client = $this->deliveryClient;
                break;

            default:
                $client = $this->otpravkaClient;
        }

        switch ($type) {
            case 'GET':
                $request = http_build_query($params);
                if ($this->logger) {
                    $this->logger->info('Russian Post Otpravka API request: '.$request);
                }
                $response = $client->get($method, ['query' => $params]);
                break;
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $request = json_encode($params);
                if ($this->logger) {
                    $this->logger->info('Russian Post Otpravka API request: '.$request);
                }

                $response = $client->{strtolower($type)}($method, ['json' => $params]);

                break;
        }

        $content_type = $response->getHeaderLine('Content-Type');
        $response_contents = $response->getBody()->getContents();

        if (preg_match('~^application/(pdf|zip)~', $content_type, $matches_type)) {
            $is_file = true;
            if ($this->logger) {
                $this->logger->info('Russian Post Otpravka API response: получен файл с расширением '.$matches_type[1]);
            }
        } else {
            if ($this->logger) {
                $this->logger->info('Russian Post Otpravka API response: ' . $response_contents);
            }
        }

        if (!in_array($response->getStatusCode(), [200,400,404,407]))
            throw new RussianPostException('Неверный код ответа от сервера Почты России при вызове метода '.$method.': ' . $response->getStatusCode(), $response->getStatusCode(), $response_contents, $request);

        if (empty($response_contents))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' пришел пустой ответ', $response->getStatusCode(), $response_contents, $request);

        if ($is_file) {
            preg_match('~=(.+)~', $response->getHeaderLine('Content-Disposition'), $matches_name);
            return new UploadedFile(
                $response->getBody(),
                $response->getBody()->getSize(),
                UPLOAD_ERR_OK,
                "{$matches_name[1]}.{$matches_type[1]}",
                $response->getHeaderLine('Content-Type')
            );
        }

        $resp = json_decode($response_contents, true);

        if (empty($resp) && $endpoint == 'delivery' && json_last_error() == JSON_ERROR_SYNTAX) {
            return $response_contents;
        }

        if ($response->getStatusCode() == 407 && !empty($resp['status']) && $resp['status'] == 'ERROR')
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['message'] . " (".$resp['status'].")", $response->getStatusCode(), $response_contents, $request);

        if ($response->getStatusCode() == 404 && !empty($resp['code']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['sub-code'] . " (".$resp['code'].")", $response->getStatusCode(), $response_contents, $request);

        if ($response->getStatusCode() == 400 && !empty($resp['error']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['error'] . " (".$resp['status'].")", $response->getStatusCode(), $response_contents, $request, $request);

        if (!empty($resp['error']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$resp['error'] . " (".$resp['status'].")", $response->getStatusCode(), $response_contents, $request);

        return $resp;
    }

    /**
     * Действие с файлом
     *
     * @param UploadedFile $file
     * @param int $action
     * @return mixed
     */
    private function fileAction($file, $action)
    {
        if ($action == self::DOWNLOAD_FILE) {
            header("Content-type:".$file->getClientMediaType());
            header("Content-Disposition:inline;filename='".$file->getClientFilename()."'");
            echo $file->getStream()->getContents();
            exit;
        } else {
            return $file;
        }
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
        return $this->callApi('POST', 'clean/address',  $addressList->get());
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
        return $this->callApi('POST', 'clean/physical',  $fioList->get());
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
        return $this->callApi('POST', 'clean/phone',  $phoneList->get());
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
     * Расчет периода доставки
     *
     * @param $post_type
     * @param $index_prom
     * @param $index_to
     * @return array
     * @throws RussianPostException
     */
    public function getDeliveryPeriod($post_type, $index_from, $index_to, $as_html = false)
    {
        $params = [];
        if (!$as_html)
            $params['jsontext'] = true;
        else
            $params['html'] = true;

        $params['posttype'] = $post_type;
        $params['from'] = $index_from;
        $params['to'] = $index_to;

        return $this->callApi('GET', 'calculate', $params, 'delivery');
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
     * @param array $orders - массив объектов Order
     * @return array
     * @throws RussianPostException
     */
    public function createOrders($orders)
    {
        return $this->callApi('PUT', 'user/backlog', $orders);
    }


    /**
     * Редактирование заказа
     *
     * @param Order $order - данные заказа
     * @param $id - id заказа в системе Почты России
     * @return array|string
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
     * @param \DateTimeImmutable $sending_date - дата отправки
     * @return array
     * @throws RussianPostException
     */
    public function createBatch($id_list, $sending_date = null)
    {
        $method = 'user/shipment';
        if ($sending_date)
            $method .= '?sending-date='.$sending_date->format('Y-m-d');

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
     * @return array|string
     * @throws RussianPostException
     * @throws \InvalidArgumentException
     */
    public function changeBatchSendingDay($batch_name, $date)
    {
        $year = $date->format('Y');
        $month = $date->format('m');
        $day = $date->format('d');
        $result = $this->callApi('POST', 'batch/'.$batch_name.'/sending/'.$year.'/'.$month.'/'.$day);
        if (empty($result)) return true;

        throw new \InvalidArgumentException($result['error-code']);
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
     * @return string|UploadedFile
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
     * @return string|UploadedFile
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
     * @return string|UploadedFile
     * @throws RussianPostException
     */
    public function generateDocF112ek($order_id, $action)
    {
        $file = $this->callApi('GET', 'forms/'.$order_id.'/f112pdf');
        return $this->fileAction($file, $action);
    }

    /**
     * Генерация печатных форм для заказа (до формирования партии)
     *
     * @param int $order_id - уникальный идентификатор заказа
     * @param int $action - действие с файлом
     * @param \DateTimeImmutable $sending_date
     * @param string $print_type - тип печати (термопечать или на бумаге)
     * @return string|UploadedFile
     * @throws RussianPostException
     */
    public function generateDocOrderPrintForm($order_id, $action, $sending_date = null, $print_type = null)
    {
        $file = $this->callApi('GET', 'forms/'.$order_id.'/forms', [
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
     * @return string|UploadedFile
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
     * @return string|UploadedFile
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
     * @return boolean
     * @throws RussianPostException
     */
    public function sendingF103form($batch_name)
    {
        $method = 'batch/'.$batch_name.'/checkin';
        $response = $this->callApi('POST', $method);
        if (!empty($response['f103-sent'])) return true;

        if (!empty($response['error-code']))
            throw new RussianPostException('От сервера Почты России при вызове метода '.$method.' получена ошибка: '.$response['error-code'].' (см. https://otpravka.pochta.ru/specification#/enums-errors)');
    }
}
