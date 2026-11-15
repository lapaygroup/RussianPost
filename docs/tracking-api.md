# Tracking API 3.0.0

Проверено по официальной документации 15 ноября 2026 года. Эта разработка ещё не опубликована как релиз. Удалённый tag `2.0.0` уже существует и указывает на `284dc41b678025bb0b33470d7aabbf3c20a54658`; перезаписывать его нельзя. Целевая основная версия по решению владельца от 15.11.2026 — **3.0.0**. Обратная совместимость нового Tracking API с прежним SOAP-контрактом не является требованием.

## Ветки и совместимость

| Ветка | База | PHP | Существующие потребители |
| --- | --- | --- | --- |
| `feature/new-tracking-api` → 3.0.0 | master / опубликованный 2.0.0 | ^8.3 | Новый TrackingApi/DTO; остальные провайдеры остаются на текущей PSR-18 архитектуре |
| `feature/php74-new-tracking-api` | git-база version_0_9; нетрекинговые классы сверены и восстановлены из SDK 1.0.1 (`4e5708b`) | ^7.4 или ^8.0 | Тот же новый TrackingApi/DTO; тарифы v2 и методы нормализации сохраняют контракт LC с SDK 1.0.1 |

Новые `TrackingApi`, DTO, исключения и контрактные fixtures одинаковы в обеих ветках. PHP 7.4 получает добавленный PSR-18 транспорт без PHP 8 property promotion/readonly. Ветка не обещает PHP 5.5–7.3, несмотря на прежнее ограничение `>=5.5` в 0.9.23. Это отдельная ветка нового API 3.0 для PHP 7.4, а не обещание обратной совместимости старого Tracking. Она не публикуется под уже занятым тегом 0.9.23; отдельный номер пакета/тега определяется перед её выпуском.

Новый API 3.0 реализован в `Providers\TrackingApi`; адаптера или автоматического fallback к прежнему SOAP нет. Старый исходник `Providers\Tracking` остаётся из базовой ветки, но не входит в новый контракт и не вызывается новым провайдером. Работоспособность прежнего сервиса Почты не гарантируется. Новый класс не возвращает старые SOAP `OperationHistoryData/historyRecord` и не реализует `getNpayInfo`. Приложения-потребители в этой работе не обновляются.

## Источники и граница проверки

- [Журнал изменений Почты России](https://otpravka.pochta.ru/new/support/business/news): переход 09.10.2026 и пакетный доступ 10.11.2026.
- [Single REST](https://otpravka.pochta.ru/new/support/business/tracking-api-specification-single).
- [Batch SOAP](https://otpravka.pochta.ru/new/support/business/tracking-api-specification-batch).
- [Активация отдельного Tracking ApiKey](https://otpravka.pochta.ru/new/settings/integrations/tracking-api).
- [WSDL](https://postapi.pochta.ru/fc?wsdl): анонимный запрос 15.11.2026 перенаправляется на `/fc/?wsdl` и отвечает HTTP 403. Схема не получена; 403 не подтверждает её содержание.

У batch документации осталась пометка о подготовке к выпуску; таблица `answerByTicketRequest` требует `login`, а пример передаёт только `ticket` и HTTP `Authorization: ApiKey …`. Также примеры запроса/ответа содержат незакрытые или повреждённые XML-теги. Реализация использует имена элементов/namespaces из опубликованных примеров, исправляя синтаксис XML. Авторизация передаётся только HTTP-заголовком; `login/password` в XML не добавляются. **Это ещё не сверка с фактическим WSDL или live-приёмка.**

Поэтому batch по умолчанию отключён, включение требует `tracking.batch_enabled => true`. До production-выпуска необходимо закрыть противоречия с владельцем API, подтвердить доступ/квоты и провести отдельно разрешённый smoke. SDK не обращается к WSDL во время создания клиента. Тесты работают без сети, реальных ключей и клиентских РПО.

Single `language` находится в query, несмотря на написание `Language` в таблице. Это проверено интерактивным генератором официальной страницы: выбор EN формирует `?language=EN&barcodeList=…`; в списке HTTP-заголовков остаются Authorization и Content-Type. Пример не отправлялся в API.

## Подключение

`api_key` — отдельный токен Tracking. Старые tracking login/password и токены Отправки автоматически не используются. Передавайте сырой ключ без префикса `ApiKey`. Значения берутся из внешней конфигурации приложения.

```php
use LapayGroup\RussianPost\Http\Psr18Transport;
use LapayGroup\RussianPost\Providers\TrackingApi;
use Nyholm\Psr7\Factory\Psr17Factory;

$factory = new Psr17Factory();
// $httpClient реализует PSR-18; его реализацию выбирает приложение.
$transport = new Psr18Transport($httpClient, $factory, $factory, $factory);
$tracking = new TrackingApi([
    'auth' => ['tracking' => ['api_key' => getenv('POSTAL_TRACKING_API_KEY')]],
    'tracking' => [
        'single_url' => 'https://postapi.pochta.ru/tracking/api/external/v1/info',
        'batch_url' => 'https://postapi.pochta.ru/fc/',
        'batch_enabled' => false, // Включить явно после оценки ограничений выше.
    ],
], $transport);
```

Для основной ветки можно использовать `Symfony\Component\HttpClient\Psr18Client` с настроенными `timeout`, `max_duration` и `max_redirects => 0`. Для PHP 7.4 допустим Guzzle 7 (`GuzzleHttp\Client`); существующий Guzzle 6 / HTTPlug 1 подключается через optional `Http\HttplugClient` без обновления Mailgun 2. [Проверенный состав и пример интеграции LC](lc-php74.md). PSR-18 не определяет API таймаутов, поэтому `timeout`, `connect_timeout` и `allow_redirects => false` настраиваются в клиенте. Транспорт должен сохранять заголовок ApiKey только для целевого endpoint; редиректы отключаются на стороне клиента.

Для тестов разрешён HTTP только на loopback. URL не должен содержать userinfo, query или fragment. По умолчанию batch использует канонический `/fc/` из примеров, чтобы не редиректить POST с `/fc`.

## Single: результат и миграция

```php
$result = $tracking->getTrackingInfo('00000000000001', 'RU'); // Только синтетический пример.
$detail = $result->getData();       // TrackedItemDetail, включая исходные nullable/неизвестные поля.
$events = $result->getOperations(); // Список ['operation' => TrackingHistoryItem, 'group' => …].
```

| Прежний контракт | Новый контракт |
| --- | --- |
| `Tracking('single', $config, $timeout)` | `TrackingApi($config, $transport)` |
| tracking login/password | отдельный `auth.tracking.api_key` |
| `getOperationsByRpo($rpo, 'RUS')` | `getTrackingInfo($rpo, 'RU')` |
| `historyRecord->OperationParameters->OperType->Id` | `event['operation']['operationType']` |
| `…->OperAttr->Id` | `event['operation']['operationAttr']` |
| `…->OperDate` | `event['operation']['date']`, исходный ISO8601 offset сохранён |
| адрес операции | `index`, `cityName`, `countryName`, `description` в operation при наличии |
| прежние поля истории SOAP | исходный `TrackedItemDetail` + фактические events; автоматического старого DTO нет |
| `getNpayInfo` / ESPP | подтверждённого аналога в изученных новых спецификациях нет |

Группы `future=true` остаются в `getData()`, но их события не попадают в `getOperations()`. Возвратное направление `TO_SENDER` сохраняется вместе с группой. История не сортируется и не дедуплицируется без правила потребителя. Неизвестные коды сохраняются; `isFinal` не выводится из заголовка группы. `null` не заменяется фиктивным значением.

Пустой массив групп означает пустую историю. Повреждённая/неподходящая структура JSON — `invalid_response`, не пустая история. SDK не проверяет контрольную цифру ШПИ: валидируются только 14 цифр либо структура S10 (2 заглавные буквы, 9 цифр, 2 заглавные буквы).

`cashOnDelivery` — сумма наложенного платежа, **не подтверждение оплаты или выплаты**. В SDK нет автоматических финансовых переходов. До интеграции в приложение нужен подтверждённый источник финансовых событий либо явное согласованное ограничение. Отсутствие нового аналога не доказывает доступность старого ESPP.

## Batch: билеты и частичные результаты

После явного включения:

```php
$tickets = $tracking->createTickets($barcodes, 'RUS');
foreach ($tickets as $ticket) {
    // Сохранить id, barcodes, getReceivedAt(), getFirstPollAt() в очереди приложения.
    // В отдельном задании после наступления разрешённого времени:
    // $result = $tracking->getResponseByTicket($ticket->getId());
}
```

`createTicket()` принимает от 1 до 3000 РПО. `createTickets()` валидирует весь вход до сети, разбивает его на независимые пакеты и возвращает `Ticket[]`; пустой вход не делает запросов. Дубликаты из входа не удаляются; накопления элементов между пакетами нет.

Если поздний пакет не создан, `TicketSubmissionException::getCompletedTickets()` сохраняет успешные билеты, `getRemainingBarcodes()` — неуспешный и ещё не отправленные пакеты. При timeout/сетевой ошибке принятие последнего POST сервером неизвестно. **Не повторяйте создание билета автоматически:** это может создать второй билет. SDK не делает retries, sleep или polling.

`getResponseByTicket()` возвращает `BatchResult`: исходный ticket id, metadata (в том числе `DatePreparation`) и список `items`: `barcode`, `operations`, `errors`. Частичный `ErrorTypeID=12` сохраняется у соответствующего элемента вместе с остальными успешными РПО. Имена и коды атрибутов операций сохранены; `OperTypeID`/`OperCtgID` представлены целыми числами, индексы и ШПИ — строками. `ErrorName` сервера не включается в исключение/результат ошибок, чтобы не возвращать возможные секреты через диагностический текст; доступны код и категория.

`DateOper` в batch — локальная дата без offset согласно документации, её нельзя автоматически считать московским временем или приравнивать к ISO8601 single. `DatePreparation` описана как московское время. Приложение должно отдельно согласовать нормализацию/дедупликацию.

Первый запрос результата — не раньше 900 секунд после локального получения билета, повторный после pending — не чаще раза в 900 секунд. `getFirstPollAt()` рассчитывает первую границу; следующие запросы планирует приложение. `getRetentionDeadlineEstimate()` — локальная оценка 32 часов от получения билета, а не гарантированная дата серверного удаления. Код 17 является окончательным сигналом истечения. SDK не очищает очередь и не отмечает билет применённым.

## Ошибки

`TrackingApiException` наследует старый `TrackingException` и предоставляет `getCategory()`, `getHttpStatus()`, `getCarrierCode()`, `getRetryAfter()`, `isTransient()`. `isTransient()` — признак класса ошибки, **не разрешение автоматически повторить POST**.

| Условие | Категория | Действие потребителя |
| --- | --- | --- |
| HTTP 401/403, batch 3 | auth | Исправить доступ, не повторять бесконечно |
| HTTP 429 | rate_limit | Учесть валидный Retry-After и квоты |
| PSR-18 timeout/network | transport | Ограниченный повтор чтения; создание билета имеет неопределённый результат |
| HTTP 5xx, batch 16 | server | Ограниченный backoff, учитывать идемпотентность |
| Другой HTTP не 2xx | http | Явная ошибка, включая редиректы |
| Невалидный JSON/XML/структура | invalid_response | Сигнал об изменении/повреждении контракта |
| SOAP Fault при HTTP 2xx/500 | soap_fault | Отдельная нетранзиентная ошибка без faultstring; HTTP 500 без SOAP Fault и остальные 5xx — server |
| batch 6 | pending | Повторить получение не раньше чем через 900 секунд |
| batch 17 | expired | Управляемое создание нового билета только для нужных РПО |
| batch 12 | not_found | Нет сведений, не считать доставкой/оплатой |
| batch 2/18/20/21/22 | validation | Исправить запрос |
| Неизвестный код | carrier | Сохранить code, не считать pending |

Ошибки входных параметров дают `InvalidArgumentException` до отправки, отключённый batch — `LogicException`. SOAP разбирается с проверкой namespaces, без DTD/ENTITY и сетевой загрузки. Авторизация, barcodes, тела запросов/ответов и сырой текст исключений транспорта не логируются; встроенный лог содержит только operation и HTTP status. Middleware выбранного HTTP-клиента должно соблюдать такое же правило.

## Проверки и выпуск

Fixtures в `tests/fixtures/tracking` созданы вручную по структуре указанных официальных страниц; все номера, токены и события синтетические. XML исправляет повреждённую разметку примеров и не является скопированным ответом production. Контракты покрывают обычную доставку, возврат, прогноз, null, неизвестные коды, пустую историю, даты, query, HTTP/transport ошибки, 0/1/3000/3001 РПО, независимость пакетов, сохранение прогресса, pending/expired/auth, частичные ошибки и защищённый XML-разбор.

Проверки: `composer validate --strict`, `composer test`, `composer analyse`. CI основной ветки: PHP 8.3/8.4/8.5; совместимой: PHP 7.4/8.1/8.4/8.5. Проверки CI не вызывают carrier API. Перед выпуском: подтвердить WSDL/активацию/квоты и финансовое решение, разрешённый live smoke, подготовить основной tag 3.0.0 и отдельно определить публикацию PHP 7.4 ветки, затем отдельный согласованный шаг tags/Packagist.
