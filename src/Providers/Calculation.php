<?php
declare(strict_types=1);
namespace LapayGroup\RussianPost\Providers;

use LapayGroup\RussianPost\Exceptions\RussianPostException;
use LapayGroup\RussianPost\Http\Psr18Transport;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class Calculation implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private const VERSION = 'v2';

    public function __construct(private readonly Psr18Transport $httpTransport) {}

    /**
     * Инициализирует вызов к API
     *
     * @param $method
     * @param $params
     * @return array
     * @throws RussianPostException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    private function callApi(string $type, string $method, array $params = []): array
    {
        $params['json'] = true; // указываем, что ждем ответ в JSON

        switch ($type) {
            case 'GET':
                $request = http_build_query($params);
                $this->logRequest($type, $method);
                $response = $this->httpTransport->send(
                    $type,
                    'https://delivery.pochta.ru/' . self::VERSION . '/' . $method,
                    [],
                    $params
                );
                break;
            case 'POST':
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
                $this->logRequest($type, $method);
                $response = $this->httpTransport->send(
                    $type,
                    'https://delivery.pochta.ru/' . self::VERSION . '/' . $method,
                    ['Content-Type' => 'application/json'],
                    [],
                    $request
                );
                break;
            default:
                throw new \InvalidArgumentException('Неподдерживаемый HTTP-метод: ' . $type);
        }

        $json = $response->getBody()->getContents();

        $this->logger?->info('Russian Post Tariff API response', [
            'method' => $type,
            'path' => '/' . self::VERSION . '/' . $method,
            'http_status' => $response->getStatusCode(),
        ]);

        if ($json === '') {
            throw new RussianPostException('От сервера Почты России при вызове метода ' . $method . ' пришел пустой ответ', $response->getStatusCode(), $json, $request);
        }

        try {
            $resp = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new RussianPostException(
                'От сервера Почты России при вызове метода ' . $method . ' получен некорректный JSON',
                $response->getStatusCode(),
                $json,
                $request,
                $exception
            );
        }

        if (!is_array($resp)) {
            throw new RussianPostException('От сервера Почты России при вызове метода ' . $method . ' получен ответ неожиданного формата', $response->getStatusCode(), $json, $request);
        }

        if ($response->getStatusCode() < 200 || $response->getStatusCode() >= 300) {
            $description = $resp['sub-code'] ?? $resp['error'] ?? $resp['message'] ?? 'HTTP ' . $response->getStatusCode();
            $errorCode = $resp['code'] ?? $resp['status'] ?? $response->getStatusCode();
            throw new RussianPostException(
                'От сервера Почты России при вызове метода ' . $method . ' получена ошибка: ' . $description . ' (' . $errorCode . ')',
                $response->getStatusCode(),
                $json,
                $request
            );
        }

        return $resp;
    }

    private function logRequest(string $method, string $path): void
    {
        $this->logger?->info('Russian Post Tariff API request', [
            'method' => $method,
            'path' => '/' . self::VERSION . '/' . $path,
        ]);
    }

    /**
     * Отправка запрос на расчет тарифа к API тарификатора V2
     *
     * @param $method
     * @param $object_id
     * @param $params
     * @param $services
     * @return array
     * @throws RussianPostException
     */
    private function tariffRequest(string $method, int $object_id, array $params, array $services): array
    {
        $params['object'] = $object_id;
        if (!empty($services))
            $params['service'] = implode(',', $services);

        return $this->callApi('GET', $method, $params);
    }

    /**
     * Получение списка категорий
     *
     * @return array
     * @throws RussianPostException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getCategoryList(): array
    {
        return $this->callApi('GET', 'dictionary/tariff/delivery', ['category' => 'all']);
    }

    /**
     * Описание категории
     *
     * @param $category_id
     * @return array
     * @throws RussianPostException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getCategoryDescription(int $category_id): array
    {
        return $this->callApi('GET', 'dictionary/tariff/delivery', ['category' => $category_id]);
    }

    /**
     * Расчет тарифа
     *
     * @param $object_id
     * @param $params
     * @param $services
     * @return array
     * @throws RussianPostException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getTariff(int $object_id, array $params, array $services): array
    {
        return $this->tariffRequest('calculate/tariff', $object_id, $params, $services);
    }

    /**
     * Расчет тарифа и сроков доставки
     *
     * @param $object_id
     * @param $params
     * @param $services
     * @return array
     * @throws RussianPostException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getTariffAndDeliveryPeriod(int $object_id, array $params, array $services): array
    {
        return $this->tariffRequest('calculate/tariff/delivery', $object_id, $params, $services);
    }

    /**
     * Описание объекта
     *
     * @param $object_id
     * @return array
     * @throws RussianPostException
     * @throws \Psr\Http\Client\ClientExceptionInterface
     */
    public function getObjectInfo(int $object_id): array
    {
        return $this->callApi('GET', 'dictionary/tariff/delivery', ['object' => $object_id]);
    }

    /**
     * Список стран
     *
     * @return array
     * @throws RussianPostException
     */
    public function getCountryList(): array
    {
        $result = $this->callApi('GET', 'dictionary/tariff/delivery', ['country' => false]);
        return !empty($result['country']) ? $result['country'] : [];
    }
}
