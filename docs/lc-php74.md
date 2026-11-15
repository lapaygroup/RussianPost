# PHP 7.4 и существующий LC

Проверка от 15.11.2026: baseline LC `174aaad`, установленный SDK `1.0.1` (`4e5708be53c6e46da67a0b071f7cba28304232d3`). Git-база этой ветки — `version_0_9`, однако возвращать LC к поведению 0.9.23 нельзя. Основной PR нового API для PHP ^8.3 остаётся отдельным; этот документ относится только к PHP 7.4-ветке. Новый Tracking API имеет новый контракт, прежний SOAP DTO не эмулируется. Номер отдельного PHP 7.4-релиза ещё не утверждён; тег/публикация не выполняются.

## Сохранённые контракты SDK 1.0.1

Нетрекинговые изменения между 0.9.23 и 1.0.1 перенесены из опубликованного исходника 1.0.1: `Calculation`, `TariffCalculation`, `CalculateInfo`, `Tariff`, `OtpravkaApi`, `AddressList`, `FioList`, `PhoneList`, `StatusList`. Удалён уже отсутствующий в 1.0.1 deprecated `PostType` (актуальный класс — `Enum\PostType`). Исправлена опечатка `nul` → `null` в значении по умолчанию `OtpravkaApi::createBatch`.

- LC `index.php` передаёт `calculate($object, $params, false, $services, $date)`. Сохранены третий аргумент `delivery_period`, delivery API v2, `items`, поля сроков доставки и денежные значения DTO.
- Нормализация LC использует `clearAddress`, `clearFio`, `clearPhone`, коллекции `add()`/`getIterator()` с сохранением ID.
- В baseline LC `system/Classes/CalculatePrf.php:23` ещё передаёт `$services` третьим аргументом. Это существующая ошибка потребителя: для контракта 1.0.1 нужен `calculate($object, $params, false, $services)`. SDK не угадывает смысл аргумента и не меняет файлы LC.

## Минимальный транспорт

| Пакет | Baseline LC | Проверенный состав |
| --- | --- | --- |
| guzzlehttp/guzzle | 6.5.8 | 6.5.8 |
| guzzlehttp/psr7 | 1.9.1 | 1.9.1 |
| guzzlehttp/promises | 1.5.3 | 1.5.3 |
| php-http/guzzle6-adapter | 1.1.1 | 1.1.1 |
| php-http/httplug | 1.1.0 | 1.1.0 |
| http-interop/http-factory-guzzle | 1.1.1 | 1.2.0 |
| psr/http-message | 1.0.1 | 1.0.1 |
| psr/http-factory | 1.0.2 | 1.0.2 |
| psr/http-client | отсутствует | 1.0.3 |
| mailgun/mailgun-php | 2.8.1 | 2.8.1 |
| silex/silex | 2.2.4 | 2.2.4 |

В изолированной копии `composer.lock` LC точечный update SDK + `http-interop/http-factory-guzzle:1.2.0` изменил **только эти два пакета и добавил psr/http-client:1.0.3**. Все остальные записи пакетов lock остались неизменны. Это проверка зависимостей, не развёртывание приложения. Для установки в потребителе следует закрепить утверждённый SDK commit и обновлять только эти пакеты, без общего `composer update`.

```php
use GuzzleHttp\Client;
use Http\Adapter\Guzzle6\Client as GuzzleAdapter;
use Http\Factory\Guzzle\RequestFactory;
use Http\Factory\Guzzle\StreamFactory;
use Http\Factory\Guzzle\UploadedFileFactory;
use LapayGroup\RussianPost\Http\HttplugClient;
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Providers\TrackingApi;

$guzzle = new Client([
    'timeout' => 30,
    'connect_timeout' => 10,
    'allow_redirects' => false,
    'http_errors' => false,
]);
$transport = new Psr18Transport(
    new HttplugClient(new GuzzleAdapter($guzzle)),
    new RequestFactory(),
    new StreamFactory(),
    new UploadedFileFactory()
);
$tracking = new TrackingApi([
    'auth' => ['tracking' => ['api_key' => getenv('POSTAL_TRACKING_API_KEY')]],
    'tracking' => ['batch_enabled' => false],
], $transport);
```

`HttplugClient` — optional мост к уже установленному `Http\Client\HttpClient`. SDK не принуждает потребителей PSR-18 устанавливать HTTPlug. Мост возвращает 4xx/5xx даже при `HttpException` старого адаптера и преобразует HTTPlug transport/network/request ошибки в PSR-18 исключения без исходного диагностического текста/previous. `getRequest()` сетевой/request ошибки предусмотрен PSR-18 и содержит исходный запрос; не журналируйте его. Провайдер TrackingApi наружу возвращает только безопасную категорию/metadata. Мост не повторяет запросы; отключение редиректов и таймауты задаёт приложение.

## Проверки и ограничения

CI закрепляет перечисленный Guzzle 6 / HTTPlug 1 stack и выполняет synthetic PHPUnit + PHPStan на PHP 7.4, 8.1, 8.4, 8.5. Контракты проверяют нормализацию LC, tariff v2 endpoints/аргументы/DTO, single REST, batch SOAP 500 Fault, HTTP errors, network/request errors и отсутствие redirect/retry.

Composer 2.11 блокирует старый Guzzle 6/PSR-7 по существующим advisories. Для воспроизведения явно выбранного baseline применяется только локальное исключение при разрешении **тестовых** зависимостей (`COMPOSER_NO_SECURITY_BLOCKING=1` в CI, разовый `--no-blocking` локально). Оно не меняет политику приложения и не означает устранение этих advisories. Переход на новую HTTP/mail библиотеку не входит в этот scope.

Batch WSDL и live-приёмка остаются открытыми, `batch_enabled` по умолчанию false. Реальные API-ключи/операции не использовались; финансовые события не выводятся из cashOnDelivery. Production и исходники LC этой SDK-работой не изменяются. См. [основной migration guide](tracking-api.md).

Исходные интерфейсы: [HTTPlug Guzzle 6 adapter 1.1.1](https://github.com/php-http/guzzle6-adapter/blob/v1.1.1/src/Client.php), [Guzzle PSR-17 factory 1.2.0](https://github.com/http-interop/http-factory-guzzle/blob/1.2.0/composer.json).
