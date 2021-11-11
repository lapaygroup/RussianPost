<a href="https://lapay.group/"><img align="left" width="200" src="https://lapay.group/img/lapaygroup.svg"></a>
<a href="https://www.pochta.ru/support/business/api"><img align="right" width="200" src="https://lapay.group/prflogo.svg"></a>    

<br /><br /><br />
[![Latest Stable Version](https://poser.pugx.org/lapaygroup/russianpost/v/stable)](https://packagist.org/packages/lapaygroup/russianpost)
[![Total Downloads](https://poser.pugx.org/lapaygroup/russianpost/downloads)](https://packagist.org/packages/lapaygroup/russianpost)
[![License](https://poser.pugx.org/lapaygroup/russianpost/license)](https://packagist.org/packages/lapaygroup/russianpost)
[![Telegram Chat](https://img.shields.io/badge/telegram-chat-blue.svg?logo=telegram)](https://t.me/phppochtarusdk)

# SDK для интеграции с программным комплексом [Почты России](https://www.pochta.ru/support/business/api).  

Посмотреть все проекты или подарить автору кофе можно [тут](https://lapay.group/opensource).   

# Содержание    
- [Changelog](#changelog)  
- [Тарификатор Почты России](#tariffs)    
- [Конфигурация](#configfile)  
- [Отладка](#debugging)  
- [Трекинг почтовых отправлений (РПО)](#tracking)  
- [Данные](#data)
  - [x] [Нормализация адреса](#clean_address)  
  - [x] [Нормализация ФИО](#clean_fio)  
  - [x] [Нормализация телефона](#clean_phone)  
  - [x] [Расчет стоимости пересылки (Упрощенная версия)](#calc_delivery_tariff) 
  - [x] [Расчет стоимости пересылки ЕКОМ](#calc_delivery_tariff_ecom) 
  - [x] [Расчет сроков доставки](#calc_delivery_period)  
  - [x] [Отображение баланса](#show_balance)   
  - [x] [Неблагонадёжный получатель](#untrustworthy_recipient)    
- [Заказы](#orders)  
  - [x] [Получение списка ПВЗ](#get_pvz_list)    
  - [x] [Создание заказов](#create_orders)  
  - [x] [Создание заказов (v2)](#create_orders_v2)  
  - [x] [Редактирование заказа](#edit_order)   
  - [x] [Удаление заказов](#delete_orders)   
  - [x] [Поиск заказа](#search_order)  
  - [x] [Поиск заказа по идентификатору](#search_order_by_id)  
  - [x] [Возврат заказов в "Новые"](#return_order_to_new)    
- [Партии](#batch)  
  - [x] [Создание партии из N заказов](#create_batch)  
  - [x] [Изменение дня отправки в почтовое отделение](#change_batch_sending_day)  
  - [x] [Перенос заказов в партию](#move_orders_to_batch)    
  - [x] [Поиск партии по наименованию](#find_batch_by_name)  
  - [x] [Поиск заказов с ШПИ](#find_orders_by_rpo)  
  - [x] [Добавление заказов в партию](#add_orders_in_batch)  
  - [x] [Удаление заказов из партии](#delete_orders_in_batch)  
  - [x] [Запрос данных о заказах в партии](#get_orders_in_batch)  
  - [x] [Поиск всех партий](#get_all_bathes)  
  - [x] [Поиск заказа в партии по id](#find_order_by_id_in_batch)  
- [Документы](#documents)  
  - [x] [Генерация пакета документации](#gendocpackage)  
  - [x] [Генерация печатной формы Ф7п](#gendocf7p)  
  - [x] [Генерация печатной формы Ф112ЭК](#gendocf112)  
  - [x] [Генерация печатных форм для заказа](#gendocorder)  
  - [x] [Генерация печатной формы Ф103](#gendocf103)  
  - [x] [Подготовка и отправка электронной формы Ф103](#senddocf103)  
  - [x] [Генерация акта осмотра содержимого](#gendocact)  
  - [x] [Генерация возвратного ярлыка на одной печатной странице](#genreturnlabel)  
- [Архив](#archive) 
  - [x] [Перевод партии в архив](#archiving_batch)  
  - [x] [Возврат партии из архива](#unarchiving_batch)  
  - [x] [Запрос данных о партиях в архиве](#get_archived_batch)  
- [Поиск ОПС](#ops_search)  
  - [x] [По индексу](#ops_search_by_index)  
  - [x] [По адресу](#ops_search_by_address)  
  - [x] [Почтовые сервисы ОПС](#get_ops_services)   
  - [x] [По координатам](#ops_search_by_coord)  
  - [x] [Поиск индексов в населенном пункте](#ops_search_in_locality)  
  - [x] [Выгрузка из паспорта ОПС](#ops-unloading-passport)    
- [Долгосрочное хранение (Не работает в API Почты России!)](#warehouse)
  - [ ] [Запрос данных о заказах в архиве](#)  
- [Возвраты](#returns)
  - [x] [Создание возвратного отправления для ранее созданного отправления](#return-by-pro)  
  - [x] [Создание отдельного возвратного отправления](#return-without-rpo)  
  - [x] [Удаление отдельного возвратного отправления](#return-delete)    
  - [x] [Редактирование отдельного возвратного отправления](#return-edit)   
- [Настройки](#settings)  
  - [x] [Текущие точки сдачи](#settings_shipping_points)  
  - [x] [Текущие настройки пользователя](#get_settings)  

<a name="links"><h1>Changelog</h1></a>.  
- 0.9.17 - Испралвены ошибки в сеттерах у Item.php. За исправление спасибо [SERGEY](https://github.com/AntistressStore);
- 0.9.16 - Актуализированы свойства вложения в заказ. За исправление спасибо [SERGEY](https://github.com/AntistressStore);  
- 0.9.15 - Добавлена возможность задать таймаут в тарификаторе. За исправление спасибо [DarWiM](https://github.com/DarWiM);  
- 0.9.14 - Добавлена поддержка Guzzle 7.3 в зависимостях Composer;  
- 0.9.13 - Добавлено поле [комментарий к заказу](src/Entity/Order.php#L125) при создании заказа V1 и V2;   
- 0.9.12 - Исправлена ошибка с отсутствующим $result->historyRecord в ответе API Почты. За исправление спасибо [Nikita Burichenko](https://github.com/nb-nortus);   
- 0.9.11 - Добавлена поддержка Guzzle 7.2 в зависимостях Composer;  
- 0.9.10 - Добавлена поддержка флага useOnlineBalance в методе отправки электронной формы Ф103;    
- 0.9.9 - Исправлена ошибка при переключении клиента в трекинге. За исправление спасибо [Alliance-X](https://github.com/Alliance-X);    
- 0.9.8 - Исправлены функции для работы с ОПС. За обнаружение и исправление спасибо [Sergey Voronov](https://github.com/srgvrnv);    
- 0.9.7 - Исправлена работы GET методов API. За обнаружение спасибо [GrayWolfy](https://github.com/GrayWolfy);    
- 0.9.6 - Добавлена функция создания заказа V2 с возвратом ШК и номеров клиентской ИС, спасибо [GrayWolfy](https://github.com/GrayWolfy) за помощь;   
- 0.9.5 - Актуализирован список статусов отправления, изменено поведение пакетного трекинга, подробнее [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.9.5);   
- 0.9.4 - Добавлена поддержка Guzzle 7.1 в зависимостях Composer;  
- 0.9.3 - Добавлена поддержка Guzzle 7 в зависимостях Composer;  
- 0.9.2 - У заказа у вложений в декларацию добавлено новое поле trademark (Торговая марка), спасибо [PankovAlxndr](https://github.com/PankovAlxndr) за актуализацию;  
- 0.9.1 - Актуализация списка статусов отправления, добавлена генерация печатных форм для заказа до формирования партии, подробнее [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.9.1); 
- 0.9.0 - Актуализация списка статусов отправления, легкий возврат, выгрузка из паспорта ОПС, подробнее [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.9.0);  
- 0.8.6 - Исправление ошибки API отправки с desc в ответе вместо sub-code;  
- 0.8.5 - Зависимость с Guzzle 6.3+ вместо строгой 6.3;  
- 0.8.3 - Доработана поддержка расчета тарифов для посылок EKOM, спасибо [Konstantin Shevsky](https://github.com/Shevsky) за доработку;
- 0.8.2 - Актуализированы параметры запроса и ответа тарификатора, за актуализацию выражаем благодарность [Konstantin Shevsky](https://github.com/Shevsky);
- 0.8.1 - Добавлена функция [получения списка ПВЗ](#get_pvz_list) для ЕКОМ, исправлена ошибка создания http-клиента к API;  
- 0.8.0 - Описание можно посмотреть [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.8.0);  
- 0.7.4 - Добавлено сохранение ошибок расчета тарифа в объект CalculateInfo с разделением на сообщение и код ошибки;    
- 0.7.3 - Исправлена ошибка при сохранении документов;  
- 0.7.2 - Актуализирован список статусов отправлений Почты России;  
- 0.7.1 - Доработана генерация RussianPostException, спасибо [toporchillo](https://github.com/toporchillo) за исправление. Добавлена расширенная информация в логировании;  
- 0.7.0 - Описание можно посмотреть [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.7.0);  
- 0.6.6 - Исправлено формирование и проверка параметров для запроса на создание заказа;  
- 0.6.5 - Реализованы функции работы с архивом;  
- 0.6.0 - Долгожданная работа с заказами, подробнее [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.6.0);  
- 0.5.4 - Правки composer.json;  
- 0.5.3 - Описание можно посмотреть [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.5.3);  
- 0.5.2 - Исправлена ошибка получения информации о сроках доставки в формате HTML;  
- 0.5.1 - Описание можно посмотреть [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.5.1);  
- 0.5.0 - Описание можно посмотреть [тут](https://github.com/lapaygroup/RussianPost/releases/tag/0.5.0);  
- 0.4.12 - Скорректировано описание упрощенной версии расчета тарифов, добавлен метод получения списка точек сдачи;  
- 0.4.11 - Актуализирован список статусов Почты России;  
- 0.4.10 - Актуализирован расчет стоимости пересылки (Упрощенная версия), за актуализацию спасибо [rik43](https://github.com/rik43);  
- 0.4.9 - Исправлена ошибка выставления флага isFinal в пакетном трекинге отправлений, за обнаружение спасибо [Dmitry Sobchenko](https://github.com/sharpensteel);    
- 0.4.8 - Изменен адрес калькулятора Почты России, старый будет отключен 01.01.2019;  
- 0.4.7 - Актуализация списка статусов;  
- 0.4.6 - Было принято решение исключить зависимость с [symfony/yaml](https://packagist.org/packages/symfony/yaml) и понизить требуемую версию PHP до 5.5+. Подробнее в разделе [Конфигурация](#configfile);  
- 0.4.5 - Актуализация списка статусов, признак конечного статуса в пакетном трекинге;  
- 0.4.0 - Единичный и пакетный трекинг отправлений;  
- 0.3.0 - Нормализация данных, упрощенный расчет стоимости отправки;  
- 0.2.0 - Расчет стоимости отправки тарификатором Почты России.  


# Установка  
Для установки можно использовать менеджер пакетов Composer

    composer require lapaygroup/russianpost
       
<a name="tariffs"><h1>Тарификатор Почты России</h1></a>  

### Получения списка видов отправления
Для получения списка категорий нужно вызвать метод **parseToArray** класса **\LapayGroup\RussianPost\CategoryList**
```php
<?php
  $CategoryList = new \LapayGroup\RussianPost\CategoryList();
  $categoryList = $CategoryList->parseToArray();
?>
```
В $categoryList мы получим ассоциативный массив из категорий, их подкатегорий и видов почтовых отправлений с возможными опциями и списком параметров, которые нужно передать для расчета тарифа. По этим данным можно легко и быстро построить форму-калькулятор аналогичную [тарификатору Почты России](https://tariff.pochta.ru/).
    
Если нужно исключить категории из выборки, то перед вызовом **parseToArray** вызываем метод **setCategoryDelete** и передаем ему массий ID категорий, которые нужно исключить.
```php
<?php
  $CategoryList = new \LapayGroup\RussianPost\CategoryList();
  $CategoryList->setCategoryDelete([100,200,300]);
  $categoryList = $CategoryList->parseToArray();
?>
```
### Расчет стоимости отправки
**objectId**, список параметров в **$params** и список дополнительных услуг **$service** берутся из массива **$categoryList**.
```php
<?php
try {
  $objectId = 2020; // Письмо с объявленной ценностью
  // Минимальный набор параметров для расчета стоимости отправления
  $params = [
              'weight' => 20, // Вес в граммах
              'sumoc' => 10000, // Сумма объявленной ценности в копейках
              'from' => 109012 // Почтовый индекс места отправления
              ];

  // Список ID дополнительных услуг 
  // 2 - Заказное уведомление о вручении 
  // 21 - СМС-уведомление о вручении
  $services = [2,21];

  $TariffCalculation = new \LapayGroup\RussianPost\TariffCalculation();
  $calcInfo = $TariffCalculation->calculate($objectId, $params, $services);
}

catch (\LapayGroup\RussianPost\Exceptions\RussianPostTarrificatorException $e) {
    // Обработка ошибок тарификатора 
    $errors = $e->getErrors(); // Массив вида [['msg' => 'текст ошибки', 'code' => код ошибки]]
}

catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
    // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
    // Обработка нештатной ситуации
}

?>
```
**$calcInfo** - это объект класса *LapayGroup\RussianPost\CalculateInfo*
Доступные методы:
 - *getCategoryItemId()* - ID вида отправления
 - *getCategoryItemName()* - название вида отправления
 - *getWeight()* - вес отправления в граммах
 - *getTransportationName()* - способ пересылки
 - *getPay()* - итого стоимоть без НДС
 - *getPayNds()* - итого стоимоть c НДС
 - *getPayMark()* - итого стоимость при оплате марками
 - *getGround()* - почтовый сбор без НДС
 - *getGroundNds()* - почтовый сбор с НДС
 - *getCover()* - страхование без НДС
 - *getCoverNds()* - страхование с НДС
 - *getService()* - дополнительные услуги без НДС
 - *getServiceNds()* - ополнительные услуги с НДС
 - *getTariffList()* - массив тарифов из которых складывается итоговая стоимость доставки  

Массив тарифов состоит из объектов класса *LapayGroup\RussianPost\Tariff*
Доступные методы:
 - *getId()* - ID тарифа
 - *getName()* - название тарифа
 - *getValue()* - стоимость без НДС
 - *getValueNds()* - стоимость с НДС
 - *getValueMark()* - стоимость при оплате марками

***Полученная информация может быть отображена так:***

**Процесс тарификации:**  
Способ пересылки: НАЗЕМНО (код РТМ2: 1).  
Плата за пересылку письма с объявленной ценностью /230/ : 106.20 с НДС  
Плата за объявленную ценность /215/ : 3.54 с НДС  
Заказное уведомление о вручении /213/ услуга 2: 56.64 с НДС  
СМС-уведомление о вручении /119/ услуга 21: 10.00 с НДС  

**Результат:**  
Почтовый сбор: 106.20 (с НДС).  
Страхование: 3.54 (с НДС).  
Дополнительные услуги: 66.64 (с НДС).  
Итого сумма без НДС: 149.47.  
Итого сумма с НДС 18%: 176.38.  


<a name="configfile"><h1>Конфигурация</h1></a>  

Для использования сервисов Почты России, не считая [тарификатор](#tariffs), необходимы аутентификационные данные.
Их можно хранить в [ассоциативном массиве](config.php.example) или [yaml-файле](config.yaml.example). В примерах ниже я буду использовать yaml-файл, а парсить его с помощью [symfony/yaml](https://packagist.org/packages/symfony/yaml).    

Информацию о аутентификационных данных можно получить [здесь](https://otpravka.pochta.ru/specification#/authorization-token) и [здесь](https://otpravka.pochta.ru/specification#/authorization-key).   

На запросы к API отправки Почтой России установлены лимиты на количество запросов в сутки. Для их увеличения необходимо написать письмо на почту support.parcel@russianpost.ru.    


<a name="debugging"><h1>Отладка</h1></a>  

Для логирования запросов и ответов используется [стандартный PSR-3 логгер](https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md). 
Рассмотрим пример логирования используя [Monolog](https://github.com/Seldaek/monolog).  

```php
<?php
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    
    $log = new Logger('name');
    $log->pushHandler(new StreamHandler('log.txt', Logger::INFO));
    
    // Логирование расчета тарифа
    $tariffCalculation = new \LapayGroup\RussianPost\TariffCalculation();
    $tariffCalculation->setLogger($log);
    
    $res = $tariffCalculation->calculate(23030, ['from' => 101000, 'to' => 101000, 'weight' => 100, 'sumoc' => 0]);
    
    
    // Логирования API отправки
    $otpravkaApi = new \LapayGroup\RussianPost\Providers\OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $otpravkaApi->setLogger($log);
    
    $addressList = new \LapayGroup\RussianPost\AddressList();
    $addressList->add('115551 Кширское шоссе 94-1, 1');
    $result = $otpravkaApi->clearAddress($addressList);
    
    
    // Логирование API трекинга
    $config['auth']['tracking']['login'] = 'login';
    $config['auth']['tracking']['password'] = 'password';
    
    $Tracking = new \LapayGroup\RussianPost\Providers\Tracking('single', $config);
    $Tracking->setLogger($log);
    
    $result = $Tracking->getOperationsByRpo('10944022440321');
```

Лог в файле выглядит так:
```
[2019-09-26 12:00:59] name.INFO: Russian Post Tariff API GET request /v1/calculate: from=101000&to=101000&weight=100&sumoc=0&date=20190926&object=23030&jsontext=1 [] []
[2019-09-26 12:01:04] name.INFO: Russian Post Tariff API GET response /v1/calculate: {"caption": "Ошибки тарификации", "version": "1.11.37.333", "data": {"id": 23030, "typ": 23, "cat": 3, "dir": 0, "name": "Посылка онлайн обыкновенная", "seq": 50, "date": 20190926, "date-first": 20190821}, "error": ["Неверное значение параметра \"Индекс места отправления\" (from). Не указано значение. (1301)"], "errors": [{"msg":"Неверное значение параметра \"Индекс места отправления\" (from). Не указано значение.","code":1301}]} {"Server":["nginx"],"Date":["Thu, 26 Sep 2019 12:00:53 GMT"],"Content-Type":["text/plain;charset=utf-8"],"Content-Length":["603"],"Connection":["keep-alive"],"Access-Control-Allow-Origin":["*"],"http_status":200} []

[2019-09-26 11:59:10] name.INFO: Russian Post Otpravka API POST request /1.0/tariff: {"fragile":true,"index-from":109440,"index-to":644015,"mail-category":"ORDINARY","mail-type":"POSTAL_PARCEL","mass":1000,"payment-method":"CASHLESS","with-order-of-notice":false,"with-simple-notice":false} [] []
[2019-09-26 11:59:11] name.INFO: Russian Post Otpravka API POST response /1.0/tariff: {   "delivery-time" : {     "max-days" : 3,     "min-days" : 1   },   "fragile-rate" : {     "rate" : 7075,     "vat" : 1415   },   "ground-rate" : {     "rate" : 30658,     "vat" : 6132   },   "notice-payment-method" : "CASHLESS",   "payment-method" : "CASHLESS",   "total-rate" : 30658,   "total-vat" : 6132 } {"Server":["nginx"],"Date":["Thu, 26 Sep 2019 11:59:11 GMT"],"Content-Type":["application/json;charset=UTF-8"],"Transfer-Encoding":["chunked"],"Connection":["keep-alive"],"Expires":["0"],"Cache-Control":["no-cache, no-store, max-age=0, must-revalidate"],"X-XSS-Protection":["1; mode=block"],"Pragma":["no-cache"],"X-Frame-Options":["DENY"],"X-Content-Type-Options":["nosniff"],"Strict-Transport-Security":["max-age=31536000; includeSubDomains"],"http_status":200} []

[2019-07-19 12:14:10] name.INFO: Russian Post Tracking API request:   <?xml version="1.0" encoding="UTF-8"?> <env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope" xmlns:ns1="http://russianpost.org/operationhistory/data" xmlns:ns2="http://russianpost.org/operationhistory"><env:Body><ns2:getOperationHistory><ns1:OperationHistoryRequest><ns1:Barcode>10944022440321</ns1:Barcode><ns1:MessageType>0</ns1:MessageType><ns1:Language>RUS</ns1:Language></ns1:OperationHistoryRequest><ns1:AuthorizationHeader><ns1:login>login</ns1:login><ns1:password>password</ns1:password></ns1:AuthorizationHeader></ns2:getOperationHistory></env:Body></env:Envelope>  [] []    
```

<a name="tracking"><h1>Трекинг почтовых отправлений (РПО)</h1></a>  

Реализует функции [API]( https://tracking.pochta.ru/specification) Почты России для работы с отправлениями.
Для работы данных функций необходим [конфигурационный файл](config.yaml.example) с логином и паролем от сервиса Почты России.

Для работы используется экземпляр класса *LapayGroup\RussianPost\Providers\Tracking*.  

**Входные параметры:**
- *$service* - Единочный (single) / Пакетный (pack);  
- *$config* - Массив данных для подключения к API;  
- *$timeout* - Таймаут HTTP соединения, по умолчанию 60 сек. (Для сервисов Почты России лучше использовать 120 сек.).  

### Единичный доступ
Метод **getOperationsByRpo** используется для получения информации о конкретном отправлении. 
Возвращает подробную информацию по всем операциям, совершенным над отправлением.  
**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\Tracking;
  
  $Tracking = new Tracking('single', Yaml::parse(file_get_contents('path_to_config.yaml')));
  $result = $Tracking->getOperationsByRpo('10944022440321');
?>
```  

**$result** - Массив с объектами операций над отправлением в формате Почты России.    

Метод **getNpayInfo** позволяет получить информацию об операциях с наложенным платежом, который связан 
с конкретным почтовым отправлением.

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\Tracking;
  
  $Tracking = new Tracking('single', Yaml::parse(file_get_contents('path_to_config.yaml')));
  $result = $Tracking->getNpayInfo('10944022440321');
?>
```  

**$result** - Массив с объектами операций с наложенным платежом в формате Почты России.


### Пакетный доступ (включается через поддержу или в через заявку на странице трекинга).  
Метод **getTickets** создает заявку в сервисе Почты России на предоставление информации 
по всем операциям по списку отправлений. На практике сервис Почты России не может отдать ответ 
по заявке с 3000 отправлений на финальной стадии из-за размера HTTP пакета, поэтому данная функция
разбивает список на части по 500 отправлений в каждой и создает по каждой заявку.

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\Tracking;
  
  $Tracking = new Tracking('pack', Yaml::parse(file_get_contents('path_to_config.yaml')));
  $result = $Tracking->getTickets(['10944022440321', '11172522364055', '10944022490302']);
?>
```  

**$result** - Ассоциативный массив данных, который содержит ключи:
- tickets - одномерный массив с номерами успешно созданных заявок 
- not_create - одномерный массив с номерами РПО, по которым не удалось создать заявку 
(на практике бывает часто, требуется повторный запрос на создание по этим РПО)    

```
Array
(
    [not_create] => Array
        (
        )

    [tickets] => Array
        (
            [0] => 20180506151902355WANVOUGROWKXUN
        )

)

```

Метод **getOperationsByTicket** возвращает массив с информацией по отправлениям по ранее созданной заявке.

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\Tracking;
  
  $Tracking = new Tracking('pack', Yaml::parse(file_get_contents('path_to_config.yaml')));
  $result = $Tracking->getOperationsByTicket('20180506151902355WANVOUGROWKXUN');
?>
```  

**$result** - Ассоциативный массив данных ключи которого - номера РПО, а значение - массив
объектов в формате Почты России расширенный свойствами:  
- *OperCtgName* - текстовое название подтипа операции;  
- *isFinal* - признак конечного статуса (после получения запрашивать статусы у этого РПО не требуется).

```
Array
(
    [10944022440321] => Array
        (
            [0] => stdClass Object
                (
                    [OperTypeID] => 1
                    [OperCtgID] => 2
                    [OperName] => Прием
                    [DateOper] => 28.04.2018 19:48:47
                    [IndexOper] => 109440
                    [OperCtgName] => Партионный
                    [isFinal] => false
                )

```

<a name="data"><h1>Данные</h1></a>  

Реализует функции [API](https://otpravka.pochta.ru/specification#/nogroup-normalization_adress) Почты России для работы с данными. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.  

<a name="clean_address"><h3>Нормализация адреса</h3></a>  

Разделяет и помещает сущности переданных адресов (город, улица) в соответствующие поля возвращаемого объекта. 
Параметр id (идентификатор записи) используется для установления соответствия переданных и полученных записей, 
так как порядок сортировки возвращаемых записей не гарантируется. Метод автоматически ищет и возвращает индекс 
близлежащего ОПС по указанному адресу.  

**Адрес считается корректным к отправке, если в ответе запроса:**
 - quality-code=GOOD, POSTAL_BOX, ON_DEMAND или UNDEF_05;
 - validation-code=VALIDATED, OVERRIDDEN или CONFIRMED_MANUALLY.  

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  
  try {
      $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
      
      $addressList = new \LapayGroup\RussianPost\AddressList();
      $addressList->add('115551 Кширское шоссе 94-1, 1');
      $result = $otpravkaApi->clearAddress($addressList);
      
      /*
      Array
      (
          [0] => Array
              (
                  [address-type] => DEFAULT
                  [corpus] => 1
                  [house] => 94
                  [id] => 0
                  [index] => 115551
                  [original-address] => 115551 Кширское шоссе 94-1, 1
                  [place] => г. Москва
                  [quality-code] => GOOD
                  [region] => г. Москва
                  [room] => 1
                  [street] => шоссе Каширское
                  [validation-code] => VALIDATED
              )
      
      )
     */
  }
              
  catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
      // Обработка ошибочного ответа от API ПРФ
  }
      
  catch (\Exception $e) {
      // Обработка нештатной ситуации
  }
?>
```
**$addressList** - это объект класса *LapayGroup\RussianPost\AddressList* содержащий список адресов для нормализации.


<a name="clean_fio"><h3>Нормализация ФИО</h3></a>  

Очищает, разделяет и помещает значения ФИО в соответствующие поля возвращаемого объекта. 
Параметр id (идентификатор записи) используется для установления соответствия переданных и полученных записей, 
так как порядок сортировки возвращаемых записей не гарантируется.  

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  
  try {
      $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
      
      $fioList = new \LapayGroup\RussianPost\FioList();
      $fioList->add('Иванов Петр игоревич');
      $result = $otpravkaApi->clearFio($fioList);
      
      /*
       Array
       (
           [0] => Array
               (
                   [id] => 0
                   [middle-name] => Игоревич
                   [name] => Петр
                   [original-fio] => Иванов Петр игоревич
                   [quality-code] => EDITED
                   [surname] => Иванов
               )
       
       )
       */
  }
              
  catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
      // Обработка ошибочного ответа от API ПРФ
  }
      
  catch (\Exception $e) {
      // Обработка нештатной ситуации
  }
?>
```
**$fioList** - это объект класса *LapayGroup\RussianPost\FioList* содержащий список ФИО для нормализации.


<a name="clean_phone"><h3>Нормализация телефона</h3></a>  

Принимает номера телефонов в неотформатированном виде, который может включать пробелы, символы: +-(). 
Очищает, разделяет и помещает сущности телефона (код города, номер) в соответствующие поля возвращаемого объекта. 
Параметр id (идентификатор записи) используется для установления соответствия переданных и полученных записей, 
так как порядок сортировки возвращаемых записей не гарантируется.

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  
  try {
      $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
      
      $phoneList = new \LapayGroup\RussianPost\PhoneList();
      $phoneList->add('9260120935');
      $result = $otpravkaApi->clearPhone($phoneList);
      
      /*
       Array
         (
             [0] => Array
                 (
                     [id] => 0
                     [original-phone] => 9260120935
                     [phone-city-code] => 926
                     [phone-country-code] => 7
                     [phone-extension] =>
                     [phone-number] => 0120935
                     [quality-code] => GOOD
                 )
         
         )
       */
  }
            
  catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
      // Обработка ошибочного ответа от API ПРФ
  }
    
  catch (\Exception $e) {
      // Обработка нештатной ситуации
  }
?>
```
**$phoneList** - это объект класса *LapayGroup\RussianPost\PhoneList* содержащий список номеров телефлонов для нормализации.


<a name="calc_delivery_tariff"><h3>Расчет стоимости пересылки (Упрощенная версия)</h3></a>  

Расчитывает стоимость пересылки в зависимости от указанных входных данных. Индекс ОПС точки отправления берется из профиля клиента. 
Возвращаемые значения указываются в копейках.

**Важно! Индекс отправления должен быть указан одного из пунктов сдачи, иначе будет возвращена ошибка 1001!**  

**Пример получения списка пунктов сдачи отправлений:**
```php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

$OtpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
$list = $OtpravkaApi->shippingPoints();
```

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  use LapayGroup\RussianPost\ParcelInfo;
  
  try {
      $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
      
      $parcelInfo = new ParcelInfo();
      $parcelInfo->setIndexFrom($list[0]['operator-postcode']); // Индекс пункта сдачи из функции $OtpravkaApi->shippingPoints()
      $parcelInfo->setIndexTo(644015);
      $parcelInfo->setMailCategory('ORDINARY'); // https://otpravka.pochta.ru/specification#/enums-base-mail-category
      $parcelInfo->setMailType('POSTAL_PARCEL'); // https://otpravka.pochta.ru/specification#/enums-base-mail-type
      $parcelInfo->setWeight(1000);
      $parcelInfo->setFragile(true);
    
      $tariffInfo = $otpravkaApi->getDeliveryTariff($parcelInfo);
      echo $tariffInfo->getTotalRate()/100 . ' руб.';
      
      /*
       LapayGroup\RussianPost\TariffInfo Object
       (
           [totalRate:LapayGroup\RussianPost\TariffInfo:private] => 30658
           [totalNds:LapayGroup\RussianPost\TariffInfo:private] => 6132
           [aviaRate:LapayGroup\RussianPost\TariffInfo:private] => 0
           [aviaNds:LapayGroup\RussianPost\TariffInfo:private] => 0
           [deliveryMinDays:LapayGroup\RussianPost\TariffInfo:private] => 1
           [deliveryMaxDays:LapayGroup\RussianPost\TariffInfo:private] => 3
           [fragileRate:LapayGroup\RussianPost\TariffInfo:p rivate] => 7075
           [fragileNds:LapayGroup\RussianPost\TariffInfo:private] => 1415
           [groundRate:LapayGroup\RussianPost\TariffInfo:private] => 30658
           [groundNds:LapayGroup\RussianPost\TariffInfo:private] => 6132
           [insuranceRate:LapayGroup\RussianPost\TariffInfo:private] => 0
           [insuranceNds:LapayGroup\RussianPost\TariffInfo:private] => 0
           [noticeRate:LapayGroup\RussianPost\TariffInfo:private] => 0
           [noticeNds:LapayGroup\RussianPost\TariffInfo:private] => 0
           [oversizeRate:LapayGroup\RussianPost\TariffInfo:private] => 0
           [oversizeNds:LapayGroup\RussianPost\TariffInfo:private] => 0
       )
       */
  }
          
  catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
      // Обработка ошибочного ответа от API ПРФ
  }
  
  catch (\Exception $e) {
      // Обработка нештатной ситуации
  }
?>
```
**$parcelInfo** - это объект класса *LapayGroup\RussianPost\ParcelInfo* содержащий данные по отправлению.
**$tariffInfo** - это объект класса *LapayGroup\RussianPost\tariffInfo* содержащий данные по расчитанному тарифу.

<a name="calc_delivery_tariff_ecom"><h3>Расчет стоимости пересылки ЕКОМ</h3></a>

Стоимость пересылки для ЕКОМ расчитывается по аналогичному вышеуказанному алгоритму, за исключением некоторых входных параметров.

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  use LapayGroup\RussianPost\ParcelInfo;
  
  try {
      $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
      
      $parcelInfo = new ParcelInfo();
      $parcelInfo->setIndexFrom($list[0]['operator-postcode']); // Индекс пункта сдачи из функции $OtpravkaApi->shippingPoints()
      $parcelInfo->setMailCategory('ORDINARY'); // https://otpravka.pochta.ru/specification#/enums-base-mail-category
      $parcelInfo->setWeight(1000);
      $parcelInfo->setFragile(true);
      
      // Параметры только для ЕКОМ
      $parcelInfo->setMailType('ECOM'); // Вид РПО ЕКОМ https://otpravka.pochta.ru/specification#/enums-base-mail-type
      $parcelInfo->setDeliveryPointindex(644015); // Вместо индекса назначения указывается индекс ПВЗ
      $parcelInfo->setEntriesType('SALE_OF_GOODS'); // Категория вложения https://otpravka.pochta.ru/specification#/enums-base-entries-type
      $parcelInfo->setFunctionalityChecking(true); // Признак услуги проверки работоспособности
      $parcelInfo->setGoodsValue(1588000); // Стоимость
      $parcelInfo->setWithFitting(true); // Признак услуги 'Возможность примерки'
    
      $tariffInfo = $otpravkaApi->getDeliveryTariff($parcelInfo);
      echo $tariffInfo->getTotalRate()/100 . ' руб.';
      
      /*
       LapayGroup\RussianPost\TariffInfo Object
       (
           [functionalityCheckingRate:LapayGroup\RussianPost\TariffInfo:private] => 30658
           [functionalityCheckingNds:LapayGroup\RussianPost\TariffInfo:private] => 6132
           [withFittingRate:LapayGroup\RussianPost\TariffInfo:private] => 0
           [withFittingNds:LapayGroup\RussianPost\TariffInfo:private] => 0
       )
       */
  }
          
  catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
      // Обработка ошибочного ответа от API ПРФ
  }
  
  catch (\Exception $e) {
      // Обработка нештатной ситуации
  }
?>
```

<a name="calc_delivery_period"><h3>Расчет сроков доставки</h3></a>  

Расчитывает сроки доставки по типам отправлений используя [API доставки Почты России](https://delivery.pochta.ru/)

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  
  try {
      $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
      $res = $otpravkaApi->getDeliveryPeriod(\LapayGroup\RussianPost\PostType::EMS, 115551, 115551);
      
      /*
       Array
       (
           [version] => 1.2.10.28
           [date] => 20190621
           [datefirst] => 20190411
           [posttype] => 7
           [posttypename] => EMS
           [from] => 115551
           [fromname] => МОСКВА 551
           [to] => 115551
           [toname] => МОСКВА 551
           [route] => 43-45000000-45000000
           [routename] => МОСКВА 551-МОСКВА 551
           [delivery] => Array
               (
                   [min] => 1
                   [max] => 1
               )
       
       )
       */
  }
          
  catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
      // Обработка ошибочного ответа от API ПРФ
  }
  
  catch (\Exception $e) {
      // Обработка нештатной ситуации
  }
?>
```

<a name="show_balance"><h3>Отображение баланса</h3></a>  

Отображает баланс расчетного счета. Возвращаемые значения указываются в копейках.

**Пример вызова:**
```php
<?php

  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  try {
      $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
      $result = $otpravkaApi->getBalance();
      
      /*
       Array
       (
           [balance] => 0
           [balance-date] => 2019-06-21
           [work-with-balance] => 1
       )
       */
    }
        
    catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
        // Обработка ошибочного ответа от API ПРФ
    }
    
    catch (\Exception $e) {
        // Обработка нештатной ситуации
    }
?>
```

<a name="untrustworthy_recipient"><h3>Неблагонадёжный получатель</h3></a>  

Актуально для отправлений с наложенным платежом. Определяет, является ли получатель благонадёжным, есть ли прецеденты невыкупа.  

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  
  $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
  
  $recepient = new \LapayGroup\RussianPost\Entity\Recipient();
  $recepient->setAddress('650905 ЯГУНОВСКИЙ, КЕМЕРОВСКАЯ ОБЛАСТЬ, УЛ БЕЛОЗЕРНАЯ, ДОМ 21,КВ 1');
  $recepient->setName('Иванов Петр Николаевич');
  $recepient->setPhone('79260112367');
  
  try {
      $res = $otpravkaApi->untrustworthyRecipient($recepient);
      /*
       Array
       (  
           [raw-address] => 650905 ЯГУНОВСКИЙ, КЕМЕРОВСКАЯ ОБЛАСТЬ, УЛ БЕЛОЗЕРНАЯ,ДОМ 21,КВ 1
           [raw-full-name] => Иванов Петр Николаевич
           [raw-telephone] => 79260112367
           [unreliability] => RELIABLE
       )
       */
  }
  
  catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
      // Обработка ошибочного ответа от API ПРФ
  }
  
  catch (\Exception $e) {
      // Обработка нештатной ситуации
  }
  
  
  // Обработка списка получателей
  $recepients[0] = $recepient;
  $recepients[1] = $recepient;
  
  try {
        $res = $otpravkaApi->untrustworthyRecipients($recepients);
        /*
         Array
         (
             [0] => Array
                 (
                     [raw-address] => 650905 ЯГУНОВСКИЙ, КЕМЕРОВСКАЯ ОБЛАСТЬ, УЛ БЕЛОЗЕРНАЯ,ДОМ 21,КВ 1
                     [raw-full-name] => Иванов Петр Николаевич
                     [raw-telephone] => 79260112367
                     [unreliability] => RELIABLE
                 ),
             [1] => Array
                  (
                      [raw-address] => 650905 ЯГУНОВСКИЙ, КЕМЕРåОВСКАЯ ОБЛАСТЬ, УЛ БЕЛОЗЕРНАЯ,ДОМ 21,КВ 1
                      [raw-full-name] => Иванов Петр Николаевич
                      [raw-telephone] => 79260112367
                      [unreliability] => RELIABLE
                  )
         )
         */
    }
    
    catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
        // Обработка ошибочного ответа от API ПРФ
    }
    
    catch (\Exception $e) {
        // Обработка нештатной ситуации
    }
?>
```

<a name="orders"><h1>Заказы</h1></a>   
Реализует функции [API](https://otpravka.pochta.ru/specification#/orders-creating_order) Почты России для работы с заказами. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.

<a name="get_pvz_list"><h3>Получение списка ПВЗ</h3></a>  
Возвращает список ПВЗ для заказов ЕКОМ.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $pvz_list = $otpravkaApi->getPvzList();
    /*
    Array
    (
        [0] => Array
            (
                [address] => Array
                    (
                        [addressType] => DEFAULT
                        [house] => 186
                        [index] => 656067
                        [manualInput] =>
                        [place] => г. Барнаул
                        [region] => край Алтайский
                        [street] => ул. Попова
                    )
    
                [brand-name] => Почта России
                [card-payment] =>
                [cash-payment] =>
                [closed] =>
                [contents-checking] => 1
                [delivery-point-index] => 656067
                [delivery-point-type] => DELIVERY_POINT
                [functionality-checking] =>
                [id] => 33815
                [latitude] => 53.341753
                [legal-name] => УФПС Алтайского края - филиал ФГУП "Почта России", Барнаульский почтамт, Отделение почтовой связи Барнаул  656067
                [legal-short-name] => БАРНАУЛ 67
                [longitude] => 83.667594
                [partial-redemption] =>
                [temporary-closed] =>
                [with-fitting] =>
                [work-time] => Array
                    (
                        [0] => пн, открыто: 08:00 - 20:00
                        [1] => вт, открыто: 08:00 - 20:00
                        [2] => ср, открыто: 08:00 - 20:00
                        [3] => чт, открыто: 08:00 - 20:00
                        [4] => пт, открыто: 08:00 - 20:00
                        [5] => сб, открыто: 09:00 - 18:00
                        [6] => вс, выходной
                    )
    
            )
    
        [1] => Array
            (
                [address] => Array
                    (
                        [addressType] => DEFAULT
                        [corpus] => 2
                        [house] => 8
                        [index] => 119526
                        [manualInput] =>
                        [place] => г. Москва
                        [region] => г. Москва
                        [street] => ул. 26-ти Бакинских Комиссаров
                    )
    
                [brand-name] => Почта России
                [card-payment] =>
                [cash-payment] =>
                [closed] =>
                [contents-checking] => 1
                [delivery-point-index] => 119526
                [delivery-point-type] => DELIVERY_POINT
                [functionality-checking] =>
                [id] => 35009
                [latitude] => 55.659170
                [legal-name] => УФПС г. Москвы-филиал ФГУП Почта России ММП 6 ОПС 526
                [legal-short-name] => МОСКВА 526
                [longitude] => 37.491359
                [partial-redemption] =>
                [temporary-closed] =>
                [with-fitting] =>
                [work-time] => Array
                    (
                        [0] => пн, открыто: 08:00 - 20:00
                        [1] => вт, открыто: 08:00 - 20:00
                        [2] => ср, открыто: 08:00 - 20:00
                        [3] => чт, открыто: 08:00 - 20:00
                        [4] => пт, открыто: 08:00 - 20:00
                        [5] => сб, открыто: 09:00 - 18:00
                        [6] => вс, открыто: 09:00 - 14:00
                    )
    
            )
    */
}
    
catch (\InvalidArgumentException $e) {
  // Обработка ошибки заполнения параметров
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```  

<a name="create_orders"><h3>Создание заказа</h3></a> 
Создает новый заказ. Автоматически рассчитывает и проставляет плату за пересылку.  
Метод asArr() проверяет заполнение необходимых для создания заказа полей и в случае незаполнения выбрасывает \InvalidArgumentException.  

**Важно!**  
Для внутренних отправлений должен задаваться цифровой почтовый индекс *$order->setIndexTo(115551)*.  
Для зарубежных отправлений должен задаваться зарубежный почтовый индекс *$order->setStrIndexTo('ab5551')*.  
По умолчанию выбран динамический ДТИ. Для изменения диапазона ДТИ нужно обратиться в службу поддержки Почты России.    

**Пример создания заказа:**
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;
use LapayGroup\RussianPost\Entity\Order;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    
    $orders = [];
    $order = new Order();
    $order->setIndexTo(115551);
    $order->setPostOfficeCode(109012);
    $order->setGivenName('Иван');
    $order->setHouseTo('92');
    $order->setCorpusTo('3');
    $order->setMass(1000);
    $order->setOrderNum('2');
    $order->setPlaceTo('Москва');
    $order->setRecipientName('Иванов Иван');
    $order->setRegionTo('Москва');
    $order->setStreetTo('Каширское шоссе');
    $order->setRoomTo('1');
    $order->setSurname('Иванов');
    $orders[] = $order->asArr();
    
    $result = $otpravkaApi->createOrders($orders);
    
    // Успешный ответ
    /*Array
    (
        [result-ids] => Array
            (
                [0] => 115322331
            )
    
    )
    */
    
    // Ответ с ошибкой
    /*Array
    (
        [errors] => Array
            (
                [0] => Array
                    (
                        [error-codes] => Array
                            (
                                [0] => Array
                                    (
                                        [code] => EMPTY_INDEX_TO
                                        [description] => Почтовый индекс не указан
                                    )
    
                            )
    
                        [position] => 0
                    )
    
            )
    
    )*/
}
    
catch (\InvalidArgumentException $e) {
  // Обработка ошибки заполнения параметров
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```  



<a name="create_orders_v2"><h3>Создание заказа V2</h3></a> 
Создает новый заказ. Автоматически рассчитывает и проставляет плату за пересылку.  
Метод asArr() проверяет заполнение необходимых для создания заказа полей и в случае незаполнения выбрасывает \InvalidArgumentException.  

**Важно!**  
Для внутренних отправлений должен задаваться цифровой почтовый индекс *$order->setIndexTo(115551)*.  
Для зарубежных отправлений должен задаваться зарубежный почтовый индекс *$order->setStrIndexTo('ab5551')*.  
По умолчанию выбран динамический ДТИ. Для изменения диапазона ДТИ нужно обратиться в службу поддержки Почты России.    

**Пример создания заказа:**
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;
use LapayGroup\RussianPost\Entity\Order;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    
    $orders = [];
    $order = new Order();
    $order->setIndexTo(115551);
    $order->setPostOfficeCode(109012);
    $order->setGivenName('Иван');
    $order->setHouseTo('92');
    $order->setCorpusTo('3');
    $order->setMass(1000);
    $order->setOrderNum('2');
    $order->setPlaceTo('Москва');
    $order->setRecipientName('Иванов Иван');
    $order->setRegionTo('Москва');
    $order->setStreetTo('Каширское шоссе');
    $order->setRoomTo('1');
    $order->setSurname('Иванов');
    $orders[] = $order->asArr();
    
    $result = $otpravkaApi->createOrdersV2($orders);
    
    // Успешный ответ
    /*Array
    (
        [orders] => Array
            (
                [barcode] => 80093053624992
                [order-num] => 3
                [result-id] => 310115153
            )
    )*/
    
    // Ответ с ошибкой
    /*Array
    (
        [errors] => Array
            (
                [0] => Array
                    (
                        [error-codes] => Array
                            (
                                [0] => Array
                                    (
                                        [code] => EMPTY_INDEX_TO
                                        [description] => Почтовый индекс не указан
                                    )
    
                            )
    
                        [position] => 0
                    )
    
            )
    
    )*/
}
    
catch (\InvalidArgumentException $e) {
  // Обработка ошибки заполнения параметров
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
``` 



<a name="edit_order"><h3>Редактирование заказа</h3></a> 
Изменение ранее созданного заказа. Автоматически рассчитывает и проставляет плату за пересылку.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;
use LapayGroup\RussianPost\Entity\Order;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    
    $order = new Order();
    $order->setIndexTo(115551);
    $order->setPostOfficeCode(109012);
    $order->setGivenName('Иван');
    $order->setHouseTo('92');
    $order->setCorpusTo('3');
    $order->setMass(1000);
    $order->setOrderNum('333'); // Меняем внутренний номер заказа
    $order->setPlaceTo('Москва');
    $order->setRecipientName('Иванов Иван');
    $order->setRegionTo('Москва');
    $order->setStreetTo('Каширское шоссе');
    $order->setRoomTo('1');
    $order->setSurname('Иванов');
    
    $result = $otpravkaApi->editOrder($order, 115322331);
    
    // Успешный ответ
    /*Array
    (
        [result-ids] => Array
            (
                [0] => 115322331
            )
    
    )
    */
    
    // Ответ с ошибкой
    /*Array
      (
          [error-codes] => Array
              (
                  [0] => Array
                      (
                          [code] => EMPTY_INDEX_TO
                          [description] => Почтовый индекс не указан
                      )

              )

          [position] => 0
      )*/
}
    
catch (\InvalidArgumentException $e) {
  // Обработка ошибки заполнения параметров
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="delete_orders"><h3>Удаление заказов</h3></a> 
Удаление заказов, который еще не добавлены в партию.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->deleteOrders([115322331]);
    /* 
    Array Успешный ответ
    (
        [result-ids] => Array
            (
                [0] => 115322331
            )
    )
    
    Array Ответ с ошибкой
    (
        [errors] => Array
            (
                [0] => Array
                    (
                        [error-code] => NOT_FOUND
                        [position] => 0
                    )
    
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```


<a name="search_order"><h3>Поиск заказа</h3></a> 
Ищет заказы по назначенному магазином идентификатору.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->findOrderByShopId(1);
    /*
    Array
    (
        [0] => Array
            (
                [address-type-to] => DEFAULT
                // По умолчанию выбран динамический ДТИ. Для изменения диапазона ДТИ нужно обратиться в службу поддержки Почты России
                [barcode] => 80082240994512
                [corpus-to] => 3
                [delivery-time] => Array
                    (
                        [max-days] => 1
                    )
    
                [given-name] => Иван
                [ground-rate] => 16500
                [ground-rate-with-vat] => 19800
                [ground-rate-wo-vat] => 16500
                [house-to] => 92
                [id] => 115322331
                [index-to] => 115551
                [mail-category] => ORDINARY
                [mail-direct] => 643
                [mail-rank] => WO_RANK
                [mail-type] => POSTAL_PARCEL
                [manual-address-input] =>
                [mass] => 1000
                [mass-rate] => 16500
                [mass-rate-with-vat] => 19800
                [mass-rate-wo-vat] => 16500
                [order-num] => 1
                [payment-method] => CASHLESS
                [place-to] => Москва
                [postmarks] => Array
                    (
                        [0] => NONSTANDARD
                    )
    
                [postoffice-code] => 109012
                [recipient-name] => Иванов Иван
                [region-to] => Москва
                [room-to] => 1
                [str-index-to] => 115551
                [street-to] => Каширское шоссе
                [surname] => Иванов
                [total-rate-wo-vat] => 16500
                [total-vat] => 3300
                [transport-type] => SURFACE
                [version] => 0
        )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```


<a name="search_order_by_id"><h3>Поиск заказа по идентификатору</h3></a> 
Ищет заказ по идентификатору Почты России.   

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->findOrderById(115322331);
    /*
    Array
    (
        [address-type-to] => DEFAULT
        [barcode] => 80082240994512
        [corpus-to] => 3
        [delivery-time] => Array
            (
                [max-days] => 1
            )
    
        [given-name] => Иван
        [ground-rate] => 16500
        [ground-rate-with-vat] => 19800
        [ground-rate-wo-vat] => 16500
        [house-to] => 92
        [id] => 115322331
        [index-to] => 115551
        [mail-category] => ORDINARY
        [mail-direct] => 643
        [mail-rank] => WO_RANK
        [mail-type] => POSTAL_PARCEL
        [manual-address-input] =>
        [mass] => 1000
        [mass-rate] => 16500
        [mass-rate-with-vat] => 19800
        [mass-rate-wo-vat] => 16500
        [order-num] => 1
        [payment-method] => CASHLESS
        [place-to] => Москва
        [postmarks] => Array
            (
                [0] => NONSTANDARD
            )
    
        [postoffice-code] => 109012
        [recipient-name] => Иванов Иван
        [region-to] => Москва
        [room-to] => 1
        [str-index-to] => 115551
        [street-to] => Каширское шоссе
        [surname] => Иванов
        [total-rate-wo-vat] => 16500
        [total-vat] => 3300
        [transport-type] => SURFACE
        [version] => 0
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```


<a name="return_order_to_new"><h3>Возврат заказов в "Новые"</h3></a> 
Метод переводит заказы из партии в раздел Новые. Партия должна быть в статусе CREATED.   

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->returnToNew([115527611]);
    /*
    Array Успешный ответ
    (
        [result-ids] => Array
            (
                [0] => 115527611
            )
    
    )

    Array Ответ с ошибкой
    (
        [errors] => Array
            (
                [0] => Array
                    (
                        [error-code] => NOT_FOUND
                        [position] => 0
                    )
    
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="party"><h1>Партии</h1></a>   
Реализует функции [API](https://otpravka.pochta.ru/specification#/batches-create_batch_from_N_orders) Почты России для работы с партиями. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.  

<a name="create_batch"><h3>Создание партии из N заказов</h3></a> 
Автоматически создает партию и переносит указанные подготовленные заказы в эту партию. 
Если заказы относятся к разным типам и категориям – создается несколько партий. 
Заказы распределяются по соответствующим партиям. 
Каждому перенесенному заказу автоматически присваивается ШПИ.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->createBatch([115527611], new DateTimeImmutable('2019-09-20'));
    /*
     Array Успешный ответ
        (
        [batches] => Array
            (
                [0] => Array
                    (
                        [batch-name] => 24
                        [batch-status] => CREATED
                        [batch-status-date] => 2019-09-03T11:37:17.589Z
                        [combined-batch-mail-types] => Array
                            (
                                [0] => POSTAL_PARCEL
                            )
    
                        [courier-order-statuses] => Array
                            (
                                [0] => NOT_REQUIRED
                            )
    
                        [international] =>
                        [list-number-date] => 2019-09-20
                        [mail-category] => COMBINED
                        [mail-category-text] => Комбинированно
                        [mail-rank] => WO_RANK
                        [mail-type] => COMBINED
                        [mail-type-text] => Комбинированно
                        [payment-method] => CASHLESS
                        [postmarks] => Array
                            (
                                [0] => NONSTANDARD
                            )
    
                        [postoffice-code] => 109012
                        [postoffice-name] => ОПС 109012
                        [shipment-avia-rate-sum] => 0
                        [shipment-avia-rate-vat-sum] => 0
                        [shipment-completeness-checking-rate-sum] => 0
                        [shipment-completeness-checking-rate-vat-sum] => 0
                        [shipment-contents-checking-rate-sum] => 0
                        [shipment-contents-checking-rate-vat-sum] => 0
                        [shipment-count] => 1
                        [shipment-ground-rate-sum] => 16500
                        [shipment-ground-rate-vat-sum] => 3300
                        [shipment-insure-rate-sum] => 0
                        [shipment-insure-rate-vat-sum] => 0
                        [shipment-inventory-rate-sum] => 0
                        [shipment-inventory-rate-vat-sum] => 0
                        [shipment-mass] => 1000
                        [shipment-mass-rate-sum] => 16500
                        [shipment-mass-rate-vat-sum] => 3300
                        [shipment-notice-rate-sum] => 0
                        [shipment-notice-rate-vat-sum] => 0
                        [shipment-sms-notice-rate-sum] => 0
                        [shipment-sms-notice-rate-vat-sum] => 0
                        [shipping-notice-type] => SIMPLE
                        [transport-type] => SURFACE
                        [use-online-balance] =>
                        [wo-mass] =>
                    )
            )
    
        [result-ids] => Array
            (
                [0] => 115527611
            )
    )
    
    Array Ответ с ошибкой
    (
        [errors] => Array
            (
                [0] => Array
                    (
                        [error-code] => NOT_FOUND
                        [error-description] => Отправление не найдено
                        [position] => 0
                    )
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="change_batch_sending_day"><h3>Изменение дня отправки в почтовое отделение</h3></a> 
Изменяет (устанавливает) новый день отправки в почтовое отделение.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->changeBatchSendingDay(25, new DateTimeImmutable('2019-09-08'));
    
}
catch (\InvalidArgumentException $e) {
    // Обработка ошибки
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="move_orders_to_batch"><h3>Перенос заказов в партию</h3></a> 
Переносит подготовленные заказы в указанную партию. 
Если часть заказов не может быть помещена в партию (тип и категория партии не соответствует типу и категории заказа) - 
возвращается json объект с указанием индекса заказа в переданном массиве и типом ошибки, остальные заказы помещаются в указанную партию. 
Каждому перенесенному заказу автоматически присваивается ШПИ.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->moveOrdersToBatch('24', [115685148]);
    /*Array
    (
        [result-ids] => Array
            (
                [0] => 115685148
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="find_batch_by_name"><h3>Поиск партии по наименованию</h3></a> 
Возвращает параметры партии по ее наименованию (batch-name).

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->findBatchByName('24');
    /*
    Array
      (
          [batch-name] => 24
          [batch-status] => CREATED
          [batch-status-date] => 2019-09-03T11:37:17.589Z
          [combined-batch-mail-types] => Array
              (
                  [0] => POSTAL_PARCEL
              )
      
          [courier-order-statuses] => Array
              (
                  [0] => NOT_REQUIRED
              )
      
          [international] =>
          [list-number-date] => 2019-09-16
          [mail-category] => COMBINED
          [mail-category-text] => Комбинированно
          [mail-rank] => WO_RANK
          [mail-type] => COMBINED
          [mail-type-text] => Комбинированно
          [payment-method] => CASHLESS
          [postmarks] => Array
              (
                  [0] => NONSTANDARD
              )
      
          [postoffice-code] => 109012
          [postoffice-name] => ОПС 109012
          [shipment-avia-rate-sum] => 0
          [shipment-avia-rate-vat-sum] => 0
          [shipment-completeness-checking-rate-sum] => 0
          [shipment-completeness-checking-rate-vat-sum] => 0
          [shipment-contents-checking-rate-sum] => 0
          [shipment-contents-checking-rate-vat-sum] => 0
          [shipment-count] => 2
          [shipment-ground-rate-sum] => 33000
          [shipment-ground-rate-vat-sum] => 6600
          [shipment-insure-rate-sum] => 0
          [shipment-insure-rate-vat-sum] => 0
          [shipment-inventory-rate-sum] => 0
          [shipment-inventory-rate-vat-sum] => 0
          [shipment-mass] => 2000
          [shipment-mass-rate-sum] => 33000
          [shipment-mass-rate-vat-sum] => 6600
          [shipment-notice-rate-sum] => 0
          [shipment-notice-rate-vat-sum] => 0
          [shipment-sms-notice-rate-sum] => 0
          [shipment-sms-notice-rate-vat-sum] => 0
          [shipping-notice-type] => SIMPLE
          [transport-type] => SURFACE
          [use-online-balance] =>
          [wo-mass] =>
      )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="find_orders_by_rpo"><h3>Поиск заказов с ШПИ</h3></a> 
Возвращает данные заказа в партии по присвоенному ему ШПИ.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->findOrderByRpo(80083740712514);
    /*
    Array
    (
        [0] => Array
            (
                [address-type-to] => DEFAULT
                [barcode] => 80083740712514
                [batch-category] => COMBINED
                [batch-name] => 24
                [batch-status] => CREATED
                [completeness-checking] =>
                [corpus-to] => 3
                [delivery-time] => Array
                    (
                        [max-days] => 1
                    )
    
                [given-name] => Иван
                [ground-rate] => 16500
                [ground-rate-with-vat] => 19800
                [ground-rate-wo-vat] => 16500
                [house-to] => 92
                [id] => 115527611
                [index-to] => 115551
                [legal-hid] => 15b12c4c-96ff-4548-8e15-aeab82c8e927
                [mail-category] => ORDINARY
                [mail-direct] => 643
                [mail-rank] => WO_RANK
                [mail-type] => POSTAL_PARCEL
                [manual-address-input] =>
                [mass] => 1000
                [mass-rate] => 16500
                [mass-rate-with-vat] => 19800
                [mass-rate-wo-vat] => 16500
                [order-num] => 223
                [payment-method] => CASHLESS
                [place-to] => Москва
                [pochtaid-hid] => 816284
                [postmarks] => Array
                    (
                        [0] => NONSTANDARD
                    )
    
                [postoffice-code] => 109012
                [recipient-name] => Иванов Иван
                [region-to] => Москва
                [room-to] => 1
                [str-index-to] => 115551
                [street-to] => Каширское шоссе
                [surname] => Иванов
                [total-rate-wo-vat] => 16500
                [total-vat] => 3300
                [transport-type] => SURFACE
                [version] => 0
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="add_orders_in_batch"><h3>Добавление заказов в партию</h3></a> 
Создает массив заказов и помещает непосредственно в партию. 
Автоматически рассчитывает и проставляет плату за пересылку. 
Каждому заказу автоматически присваивается ШПИ.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $orders = []; // Массив заказов
    $result = $otpravkaApi->addOrdersToBatch('24', $orders); // Ответ аналогичен созданию заказов
}

catch (\InvalidArgumentException $e) {
  // Обработка ошибки заполнения параметров
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="delete_orders_in_batch"><h3>Удаление заказов из партии</h3></a> 
Удаляет заказы, которые уже были добавлены в партию.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->deleteOrdersInBatch([115527611]);
    /*
     Array Успешный ответ
    (
        [result-ids] => Array
            (
                [0] => 115685148
            )
    )
   
    Array Ответ с ошибкой
    (
        [errors] => Array
            (
                [0] => Array
                    (
                        [error-code] => NOT_FOUND
                        [position] => 0
                    )
    
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="get_orders_in_batch"><h3>Запрос данных о заказах в партии</h3></a> 
Возвращает заказы в партии по заданным параметрам.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->getOrdersInBatch(25);
    /*
    Array
    (
        [0] => Array
            (
                [address-type-to] => DEFAULT
                [barcode] => 80084740397510
                [batch-category] => COMBINED
                [batch-name] => 25
                [batch-status] => CREATED
                [completeness-checking] =>
                [corpus-to] => 3
                [delivery-time] => Array
                    (
                        [max-days] => 1
                    )
    
                [given-name] => Иван
                [ground-rate] => 16500
                [ground-rate-with-vat] => 19800
                [ground-rate-wo-vat] => 16500
                [house-to] => 92
                [human-operation-name] => Присвоен трек-номер
                [id] => 115689758
                [index-to] => 115551
                [last-oper-attr] => ID_ASSIGNED
                [last-oper-date] => 2019-09-03T11:48:20.759Z
                [last-oper-type] => ID_ASSIGNMENT
                [legal-hid] => 15b12c4c-96ff-4548-8e15-aeab82c8e927
                [mail-category] => ORDINARY
                [mail-direct] => 643
                [mail-rank] => WO_RANK
                [mail-type] => POSTAL_PARCEL
                [manual-address-input] =>
                [mass] => 1000
                [mass-rate] => 16500
                [mass-rate-with-vat] => 19800
                [mass-rate-wo-vat] => 16500
                [order-num] => 2
                [payment-method] => CASHLESS
                [place-to] => Москва
                [pochtaid-hid] => 816284
                [postmarks] => Array
                    (
                        [0] => NONSTANDARD
                    )
    
                [postoffice-code] => 109012
                [recipient-name] => Иванов Иван
                [region-to] => Москва
                [room-to] => 1
                [str-index-to] => 115551
                [street-to] => Каширское шоссе
                [surname] => Иванов
                [total-rate-wo-vat] => 16500
                [total-vat] => 3300
                [transport-type] => SURFACE
                [version] => 1
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="get_all_bathes"><h3>Поиск всех партий</h3></a> 
Возвращает партии по заданным фильтрам.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->getAllBatches(); // Может вызываться с фильтрами
    /*
    Array
    (
        [0] => Array
            (
                [batch-name] => 24
                [batch-status] => CREATED
                [batch-status-date] => 2019-09-03T11:37:17.589Z
                [combined-batch-mail-types] => Array
                    (
                        [0] => POSTAL_PARCEL
                    )
    
                [courier-order-statuses] => Array
                    (
                        [0] => NOT_REQUIRED
                    )
    
                [international] =>
                [list-number-date] => 2019-09-16
                [mail-category] => COMBINED
                [mail-category-text] => Комбинированно
                [mail-rank] => WO_RANK
                [mail-type] => COMBINED
                [mail-type-text] => Комбинированно
                [payment-method] => CASHLESS
                [postmarks] => Array
                    (
                        [0] => NONSTANDARD
                    )
    
                [postoffice-address] => ул Никольская, д.7-9, стр.3, г Москва
                [postoffice-code] => 109012
                [postoffice-name] => ОПС 109012
                [shipment-avia-rate-sum] => 0
                [shipment-avia-rate-vat-sum] => 0
                [shipment-completeness-checking-rate-sum] => 0
                [shipment-completeness-checking-rate-vat-sum] => 0
                [shipment-contents-checking-rate-sum] => 0
                [shipment-contents-checking-rate-vat-sum] => 0
                [shipment-count] => 1
                [shipment-ground-rate-sum] => 16500
                [shipment-ground-rate-vat-sum] => 3300
                [shipment-insure-rate-sum] => 0
                [shipment-insure-rate-vat-sum] => 0
                [shipment-inventory-rate-sum] => 0
                [shipment-inventory-rate-vat-sum] => 0
                [shipment-mass] => 1000
                [shipment-mass-rate-sum] => 16500
                [shipment-mass-rate-vat-sum] => 3300
                [shipment-notice-rate-sum] => 0
                [shipment-notice-rate-vat-sum] => 0
                [shipment-sms-notice-rate-sum] => 0
                [shipment-sms-notice-rate-vat-sum] => 0
                [shipping-notice-type] => SIMPLE
                [transport-type] => SURFACE
                [use-online-balance] =>
                [wo-mass] =>
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="find_order_by_id_in_batch"><h3>Поиск заказа в партии по id</h3></a> 


```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->findOrderInBatch(115689758);
    /*
    Array
    (
        [address-type-to] => DEFAULT
        [barcode] => 80084740397510
        [batch-category] => COMBINED
        [batch-name] => 25
        [batch-status] => CREATED
        [completeness-checking] =>
        [corpus-to] => 3
        [delivery-time] => Array
            (
                [max-days] => 1
            )
    
        [given-name] => Иван
        [ground-rate] => 16500
        [ground-rate-with-vat] => 19800
        [ground-rate-wo-vat] => 16500
        [house-to] => 92
        [human-operation-name] => Присвоен трек-номер
        [id] => 115689758
        [index-to] => 115551
        [last-oper-attr] => ID_ASSIGNED
        [last-oper-date] => 2019-09-03T11:48:20.759Z
        [last-oper-type] => ID_ASSIGNMENT
        [legal-hid] => 15b12c4c-96ff-4548-8e15-aeab82c8e927
        [mail-category] => ORDINARY
        [mail-direct] => 643
        [mail-rank] => WO_RANK
        [mail-type] => POSTAL_PARCEL
        [manual-address-input] =>
        [mass] => 1000
        [mass-rate] => 16500
        [mass-rate-with-vat] => 19800
        [mass-rate-wo-vat] => 16500
        [order-num] => 2
        [payment-method] => CASHLESS
        [place-to] => Москва
        [pochtaid-hid] => 816284
        [postmarks] => Array
            (
                [0] => NONSTANDARD
            )
    
        [postoffice-code] => 109012
        [recipient-name] => Иванов Иван
        [region-to] => Москва
        [room-to] => 1
        [str-index-to] => 115551
        [street-to] => Каширское шоссе
        [surname] => Иванов
        [total-rate-wo-vat] => 16500
        [total-vat] => 3300
        [transport-type] => SURFACE
        [version] => 1
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="documents"><h1>Документы</h1></a>   
Реализует функции [API](https://otpravka.pochta.ru/specification#/documents-create_all_docs) Почты России для работы с документами. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.    

Все функции работы с документами принимают параметр action, который принимает два значения:  
 - OtpravkaApi::DOWNLOAD_FILE - выводит соответствующие header для скачивания файла в браузере;  
 - OtpravkaApi::PRINT_FILE - возврат объекта GuzzleHttp\Psr7\UploadedFile с данными о файле.  
 
 
**Важно!** Перед печатью любого документа нужно зафиксировать изменения в партии вызовом функции *sendingF103form()*:  
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $otpravkaApi->sendingF103form(28);
    $otpravkaApi->sendingF103form(28, true); // С онлайн балансом 

}

catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="gendocpackage"><h3>Генерация пакета документации</h3></a> 
Генерирует и возвращает zip архив с 4-мя файлами:
 - Export.xls , Export.csv - список с основными данными по заявкам в составе партии
 - F103.pdf - форма ф103 по заявкам в составе партии
 - В зависимости от типа и категории отправлений, формируется комбинация из сопроводительных документов в формате pdf ( формы: f7, f112, f22)

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->generateDocPackage(28, OtpravkaApi::PRINT_FILE);
    /*
    GuzzleHttp\Psr7\UploadedFile Object
    (
        [clientFilename:GuzzleHttp\Psr7\UploadedFile:private] => all-pdf.zip
        [clientMediaType:GuzzleHttp\Psr7\UploadedFile:private] => application/zip; charset=UTF-8
        [error:GuzzleHttp\Psr7\UploadedFile:private] => 0
        [file:GuzzleHttp\Psr7\UploadedFile:private] =>
        [moved:GuzzleHttp\Psr7\UploadedFile:private] =>
        [size:GuzzleHttp\Psr7\UploadedFile:private] => 290398
        [stream:GuzzleHttp\Psr7\UploadedFile:private] => GuzzleHttp\Psr7\Stream Object
            (
                [stream:GuzzleHttp\Psr7\Stream:private] => Resource id #56
                [size:GuzzleHttp\Psr7\Stream:private] => 290398
                [seekable:GuzzleHttp\Psr7\Stream:private] => 1
                [readable:GuzzleHttp\Psr7\Stream:private] => 1
                [writable:GuzzleHttp\Psr7\Stream:private] => 1
                [uri:GuzzleHttp\Psr7\Stream:private] => php://temp
                [customMetadata:GuzzleHttp\Psr7\Stream:private] => Array
                    (
                    )
    
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="gendocf7p"><h3>Генерация печатной формы Ф7п</h3></a> 
Генерирует и возвращает pdf файл с формой ф7п для указанного заказа. Опционально в файл прикрепляется форма Ф22 (посылка онлайн).
Если параметр sending-date не передается, берется текущая дата.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->generateDocF7p(123645924, OtpravkaApi::PRINT_FILE, new DateTimeImmutable('now'));
    /*
    GuzzleHttp\Psr7\UploadedFile Object
    (
        [clientFilename:GuzzleHttp\Psr7\UploadedFile:private] => f7p.pdf
        [clientMediaType:GuzzleHttp\Psr7\UploadedFile:private] => application/pdf; charset=UTF-8
        [error:GuzzleHttp\Psr7\UploadedFile:private] => 0
        [file:GuzzleHttp\Psr7\UploadedFile:private] =>
        [moved:GuzzleHttp\Psr7\UploadedFile:private] =>
        [size:GuzzleHttp\Psr7\UploadedFile:private] => 99776
        [stream:GuzzleHttp\Psr7\UploadedFile:private] => GuzzleHttp\Psr7\Stream Object
            (
                [stream:GuzzleHttp\Psr7\Stream:private] => Resource id #56
                [size:GuzzleHttp\Psr7\Stream:private] => 99776
                [seekable:GuzzleHttp\Psr7\Stream:private] => 1
                [readable:GuzzleHttp\Psr7\Stream:private] => 1
                [writable:GuzzleHttp\Psr7\Stream:private] => 1
                [uri:GuzzleHttp\Psr7\Stream:private] => php://temp
                [customMetadata:GuzzleHttp\Psr7\Stream:private] => Array
                    (
                    )
    
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="gendocf112"><h3>Генерация печатной формы Ф112ЭК</h3></a> 
Генерирует и возвращает pdf-файл с заполненной формой Ф112ЭК для указанного заказа. Только для заказа с «наложенным платежом». 
Если заказ не имеет данного атрибута, метод вернет ошибку. Если параметр sending-date не передается, берется текущая дата.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->generateDocF112ek(123645924, OtpravkaApi::PRINT_FILE);
    /*
    GuzzleHttp\Psr7\UploadedFile Object
    (
        [clientFilename:GuzzleHttp\Psr7\UploadedFile:private] => f112.pdf
        [clientMediaType:GuzzleHttp\Psr7\UploadedFile:private] => application/pdf; charset=UTF-8
        [error:GuzzleHttp\Psr7\UploadedFile:private] => 0
        [file:GuzzleHttp\Psr7\UploadedFile:private] =>
        [moved:GuzzleHttp\Psr7\UploadedFile:private] =>
        [size:GuzzleHttp\Psr7\UploadedFile:private] => 149702
        [stream:GuzzleHttp\Psr7\UploadedFile:private] => GuzzleHttp\Psr7\Stream Object
            (
                [stream:GuzzleHttp\Psr7\Stream:private] => Resource id #56
                [size:GuzzleHttp\Psr7\Stream:private] => 149702
                [seekable:GuzzleHttp\Psr7\Stream:private] => 1
                [readable:GuzzleHttp\Psr7\Stream:private] => 1
                [writable:GuzzleHttp\Psr7\Stream:private] => 1
                [uri:GuzzleHttp\Psr7\Stream:private] => php://temp
                [customMetadata:GuzzleHttp\Psr7\Stream:private] => Array
                    (
                    )
    
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="gendocorder"><h3>Генерация печатных форм для заказа</h3></a>
Генерирует и возвращает pdf файл, который может содержать в зависимости от типа отправления:  
 - форму ф7п (посылка, посылка-онлайн, бандероль, курьер-онлайн);
 - форму Е-1 (EMS, EMS-оптимальное, Бизнес курьер, Бизнес курьер экспресс)
 - конверт (письмо заказное).    
 
Опционально прикрепляются формы: Ф112ЭК (отправление с наложенным платежом), Ф22 (посылка онлайн), уведомление (для заказного письма или бандероли).

**ВАЖНО:** Для генерации печатных форм до формирования партии в третьем параметре **batch** необходимо передавать **false**.  
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    // Генерация печатных форм до формирования партии
    $result = $otpravkaApi->generateDocOrderPrintForm(123645924, OtpravkaApi::PRINT_FILE, false, new DateTimeImmutable('now'));
    // Генерация печатных форм после формирования партии
    $result = $otpravkaApi->generateDocOrderPrintForm(123645924, OtpravkaApi::PRINT_FILE, true, new DateTimeImmutable('now'));

    /*
    GuzzleHttp\Psr7\UploadedFile Object
    (
        [clientFilename:GuzzleHttp\Psr7\UploadedFile:private] => form.pdf
        [clientMediaType:GuzzleHttp\Psr7\UploadedFile:private] => application/pdf; charset=UTF-8
        [error:GuzzleHttp\Psr7\UploadedFile:private] => 0
        [file:GuzzleHttp\Psr7\UploadedFile:private] =>
        [moved:GuzzleHttp\Psr7\UploadedFile:private] =>
        [size:GuzzleHttp\Psr7\UploadedFile:private] => 251338
        [stream:GuzzleHttp\Psr7\UploadedFile:private] => GuzzleHttp\Psr7\Stream Object
            (
                [stream:GuzzleHttp\Psr7\Stream:private] => Resource id #70
                [size:GuzzleHttp\Psr7\Stream:private] => 251338
                [seekable:GuzzleHttp\Psr7\Stream:private] => 1
                [readable:GuzzleHttp\Psr7\Stream:private] => 1
                [writable:GuzzleHttp\Psr7\Stream:private] => 1
                [uri:GuzzleHttp\Psr7\Stream:private] => php://temp
                [customMetadata:GuzzleHttp\Psr7\Stream:private] => Array
                    (
                    )
    
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="gendocf103"><h3>Генерация печатной формы Ф103</h3></a>
Генерирует и возвращает pdf файл с формой Ф103 для указанной партии.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->generateDocF103(28, OtpravkaApi::PRINT_FILE);
    /*
    GuzzleHttp\Psr7\UploadedFile Object
    (
        [clientFilename:GuzzleHttp\Psr7\UploadedFile:private] => f103.pdf
        [clientMediaType:GuzzleHttp\Psr7\UploadedFile:private] => application/pdf; charset=UTF-8
        [error:GuzzleHttp\Psr7\UploadedFile:private] => 0
        [file:GuzzleHttp\Psr7\UploadedFile:private] =>
        [moved:GuzzleHttp\Psr7\UploadedFile:private] =>
        [size:GuzzleHttp\Psr7\UploadedFile:private] => 131856
        [stream:GuzzleHttp\Psr7\UploadedFile:private] => GuzzleHttp\Psr7\Stream Object
            (
                [stream:GuzzleHttp\Psr7\Stream:private] => Resource id #74
                [size:GuzzleHttp\Psr7\Stream:private] => 131856
                [seekable:GuzzleHttp\Psr7\Stream:private] => 1
                [readable:GuzzleHttp\Psr7\Stream:private] => 1
                [writable:GuzzleHttp\Psr7\Stream:private] => 1
                [uri:GuzzleHttp\Psr7\Stream:private] => php://temp
                [customMetadata:GuzzleHttp\Psr7\Stream:private] => Array
                    (
                    )
    
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="senddocf103"><h3>Подготовка и отправка электронной формы Ф103</h3></a>
Присваивает уникальную версию партии для дальнейшего приема этой партии сотрудниками ОПС. 
Отправляет по e-mail электронную форму Ф103 в ОПС для регистрации.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->sendingF103form(28); // return boolean
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="gendocact"><h3>Генерация печатной формы акта осмотра содержимого</h3></a>
Генерирует и возвращает pdf файл с формой акта осмотра содержимого для указанной партии.       

**Важно! Дананя функция работает только, если включена услуга проверки комплектности по отправлению.**  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->generateDocCheckingAct(28, OtpravkaApi::PRINT_FILE);
    // TODO если у Вас есть пример ответа, просьба приложить его через pull request :)
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```


<a name="genreturnlabel"><h3>Генерация возвратного ярлыка на одной печатной странице</h3></a>
Генерирует и возвращает pdf файл возвратного ярлыка на одной печатной странице.            

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->generateReturnLabel(123456, OtpravkaApi::PRINT_FILE, OtpravkaApi::PRINT_TYPE_PAPER);
    // TODO если у Вас есть пример ответа, просьба приложить его через pull request :)
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```


<a name="archive"><h1>Архив</h1></a>   
Реализует функции [API](https://otpravka.pochta.ru/specification#/archive-search_batches) Почты России для работы с архивом. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.  


<a name="archiving_batch"><h3>Перевод партии в архив</h3></a> 
Перевод списка партий в архив.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->archivingBatch([25]);
    /*
    Array    (

        [0] => Array
            (
                [batch-name] => 25
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="unarchiving_batch"><h3>Возврат партии из архива</h3></a> 
Возврат списка партий из архива.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->unarchivingBatch([25]);
    /*
    Array Успешный ответ
    (
        [0] => Array
            (
                [batch-name] => 25
            )
    
    )
    
    Array Ответ с ошибкой
    (
        [0] => Array
            (
                [batch-name] => 26
                [error-code] => NOT_FOUND
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="get_archived_batch"><h3>Запрос данных о партиях в архиве</h3></a> 
Возврат списка партий в архиве.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->getArchivedBatches();
    /*
    Array
    (
        [0] => Array
            (
                [batch-name] => 25
                [batch-status] => ARCHIVED
                [batch-status-date] => 2019-09-03T13:17:59.237Z
                [combined-batch-mail-types] => Array
                    (
                        [0] => POSTAL_PARCEL
                    )
    
                [courier-order-statuses] => Array
                    (
                        [0] => NOT_REQUIRED
                    )
    
                [international] =>
                [list-number-date] => 2019-09-08
                [mail-category] => COMBINED
                [mail-category-text] => Комбинированно
                [mail-rank] => WO_RANK
                [mail-type] => COMBINED
                [mail-type-text] => Комбинированно
                [payment-method] => CASHLESS
                [postmarks] => Array
                    (
                        [0] => NONSTANDARD
                    )
    
                [postoffice-address] => ул Никольская, д.7-9, стр.3, г Москва
                [postoffice-code] => 109012
                [postoffice-name] => ОПС 109012
                [shipment-avia-rate-sum] => 0
                [shipment-avia-rate-vat-sum] => 0
                [shipment-completeness-checking-rate-sum] => 0
                [shipment-completeness-checking-rate-vat-sum] => 0
                [shipment-contents-checking-rate-sum] => 0
                [shipment-contents-checking-rate-vat-sum] => 0
                [shipment-count] => 1
                [shipment-ground-rate-sum] => 16500
                [shipment-ground-rate-vat-sum] => 3300
                [shipment-insure-rate-sum] => 0
                [shipment-insure-rate-vat-sum] => 0
                [shipment-inventory-rate-sum] => 0
                [shipment-inventory-rate-vat-sum] => 0
                [shipment-mass] => 1000
                [shipment-mass-rate-sum] => 16500
                [shipment-mass-rate-vat-sum] => 3300
                [shipment-notice-rate-sum] => 0
                [shipment-notice-rate-vat-sum] => 0
                [shipment-sms-notice-rate-sum] => 0
                [shipment-sms-notice-rate-vat-sum] => 0
                [shipping-notice-type] => SIMPLE
                [transport-type] => SURFACE
                [use-online-balance] =>
                [wo-mass] =>
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="ops_search"><h1>Поиск ОПС</h1></a>   
Реализует функции [API](https://otpravka.pochta.ru/specification#/services-postoffice) Почты России для поиска ОПС. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.  



<a name="ops_search_by_index"><h3>Поиск почтового отделения по индексу</h3></a> 
Возвращает информацию о ОПС.  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->searchPostOfficeByIndex(115551, 0, 0);
    /*
    Array
    (
        [address-source] => Домодедовская ул, 20, к.3, стр.2
        [distance] => 7059103.1165241
        [holidays] => Array
            (
            )
    
        [is-closed] =>
        [is-private-category] =>
        [is-temporary-closed] =>
        [latitude] => 55.612772
        [longitude] => 37.704862
        [postal-code] => 115551
        [region] => Москва г
        [settlement] => Москва
        [type-code] => ГОПС
        [type-id] => 8
        [working-hours] => Array
            (
                [0] => Array
                    (
                        [begin-worktime] => 08:00:00.000
                        [end-worktime] => 20:00:00.000
                        [lunches] => Array
                            (
                            )
    
                        [weekday-id] => 1
                    )
    
                [1] => Array
                    (
                        [begin-worktime] => 08:00:00.000
                        [end-worktime] => 20:00:00.000
                        [lunches] => Array
                            (
                            )
    
                        [weekday-id] => 2
                    )
    
                [2] => Array
                    (
                        [begin-worktime] => 08:00:00.000
                        [end-worktime] => 20:00:00.000
                        [lunches] => Array
                            (
                            )
    
                        [weekday-id] => 3
                    )
    
                [3] => Array
                    (
                        [begin-worktime] => 08:00:00.000
                        [end-worktime] => 20:00:00.000
                        [lunches] => Array
                            (
                            )
    
                        [weekday-id] => 4
                    )
    
                [4] => Array
                    (
                        [begin-worktime] => 08:00:00.000
                        [end-worktime] => 20:00:00.000
                        [lunches] => Array
                            (
                            )
    
                        [weekday-id] => 5
                    )
    
                [5] => Array
                    (
                        [begin-worktime] => 09:00:00.000
                        [end-worktime] => 18:00:00.000
                        [lunches] => Array
                            (
                            )
    
                        [weekday-id] => 6
                    )
    
                [6] => Array
                    (
                        [begin-worktime] => 09:00:00.000
                        [end-worktime] => 14:00:00.000
                        [lunches] => Array
                            (
                            )
    
                        [weekday-id] => 7
                    )
    
            )
    
        [works-on-saturdays] => 1
        [works-on-sundays] => 1
        [phones] => Array
            (
                [0] => Array
                    (
                        [is-fax] =>
                        [phone-number] => 1000000
                        [phone-town-code] => 800
                        [phone-type-name] => Прочее
                    )
    
            )
    
        [service-groups] => Array
            (
                [0] => Array
                    (
                        [group-id] => 2101
                        [group-name] => Почтовые услуги
                    )
    
                [1] => Array
                    (
                        [group-id] => 2315
                        [group-name] => Коммерческие услуги
                    )
    
                [2] => Array
                    (
                        [group-id] => 2259
                        [group-name] => Финансовые услуги
                    )
    
            )
    
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="ops_search_by_address"><h3>Поиск обслуживающего ОПС по адресу</h3></a> 
Возвращает список почтовых индексов ОПС и признак является ли переданный адрес точным адресом ОПС.

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->searchPostOfficeByAddress('Санкт-Петербург, улица Победы, 15к1');
    /*
    Array
    (
        [is-matched] =>
        [postoffices] => Array
            (
                [0] => 196070
            )
    
    )
    */
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="get_ops_services"><h3>Поиск почтовых сервисов ОПС</h3></a> 
Может возвращать как все доступные сервисы, так и сервисы определенной группы (например: Киберпочт@).

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->getPostOfficeServices(196070);
    $result = $otpravkaApi->getPostOfficeServices(196070, 2101); // С фильтром по группе
    /*
    Array
    (
        [0] => Array
            (
                [code] => 1090900
                [group-id] => 2101
                [name] => Хранение и возврат почтовых отправлений, периодических изданий: Абонирование ячейки абонементного почтового шкафа, Возврат посылок, Возврат РПО (кроме посылок), Хранение РПО в ОПС
            )
    
        [1] => Array
            (
                [code] => 1090200
                [group-id] => 2101
                [name] => Информирование отправителей и получателей РПО, ЕМS -отправлений об их статусе: SMS-уведомление, Заказное уведомление о вручении внутреннего  РПО, Простое уведомление о вручении внутреннего РПО, Простое уведомление о получении международного почтового отправления, Электронное уведомление о вручении внутреннего РПО
            )
    
        [2] => Array
            (
                [code] => 1090100
                [group-id] => 2101
                [name] => Доставка и оказание услуг по адресу расположения (проживания) клиента: Доставка посылок и мелких пакетов по местонахождению клиента
            )
    */
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="ops_search_by_coord"><h3>Поиск почтовых отделений по координатам</h3></a> 
Возвращает список ОПС по переданному массиву параметров согласно [документации](https://otpravka.pochta.ru/specification#/services-postoffice-nearby).  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->searchPostOfficeByCoordinates($params); // $params - массив параметров поиска
    /*
        Ответ аналогичен функции searchPostOfficeByIndex только список.
    */
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```


<a name="ops_search_in_locality"><h3>Поиск почтовых индексов в населённом пункте</h3></a> 
Возвращает список индексов.   

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->getPostalCodesInLocality('Екатеринбург');
    /*
    Array
    (
        [0] => 620000
        [1] => 620002
        [2] => 620004
        [3] => 620007
        [4] => 620010
        [5] => 620012
    */
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="ops-unloading-passport"><h3>Выгрузка из паспорта ОПС</h3></a> 
Выгружает данные ОПС, ПВЗ, Почтоматов из Паспорта ОПС.  
Генерирует и возвращает zip архив с текстовым файлом TYPEdd_MMMM_yyyy.txt, где:  
* TYPE - тип объекта  
* dd_MMMM_yyyy - время создания архива   

**Входные параметры:** 
- *$type* - [Тип объекта ОПС](Enum/OpsObjectType.php).  

```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    $result = $otpravkaApi->getPostOfficeFromPassport(\LapayGroup\RussianPost\Enum\OpsObjectType::OPS);
    /*
    GuzzleHttp\Psr7\UploadedFile Object
    (
        [clientFilename:GuzzleHttp\Psr7\UploadedFile:private] => OPS02_May_2020.zip.octet-stream
        [clientMediaType:GuzzleHttp\Psr7\UploadedFile:private] => application/octet-stream; charset=UTF-8
        [error:GuzzleHttp\Psr7\UploadedFile:private] => 0
        [file:GuzzleHttp\Psr7\UploadedFile:private] =>
        [moved:GuzzleHttp\Psr7\UploadedFile:private] =>
        [size:GuzzleHttp\Psr7\UploadedFile:private] => 4203382
        [stream:GuzzleHttp\Psr7\UploadedFile:private] => GuzzleHttp\Psr7\Stream Object
            (
                [stream:GuzzleHttp\Psr7\Stream:private] => Resource id #56
                [size:GuzzleHttp\Psr7\Stream:private] => 4203382
                [seekable:GuzzleHttp\Psr7\Stream:private] => 1
                [readable:GuzzleHttp\Psr7\Stream:private] => 1
                [writable:GuzzleHttp\Psr7\Stream:private] => 1
                [uri:GuzzleHttp\Psr7\Stream:private] => php://temp
                [customMetadata:GuzzleHttp\Psr7\Stream:private] => Array
                    (
                    )
    
            )
    )*/
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```


<a name="warehouse"><h1>Долгосрочное хранение</h1></a>     
  
**!!!Данный раздел не работает в API Почты России!!!**    


Реализует функции [API](https://otpravka.pochta.ru/specification#/long-term-archive-search_shipments) Почты России для работы с долгосрочным хранением. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.  



<a name="returns"><h1>Возвраты</h1></a>  
Реализует функции [API](https://otpravka.pochta.ru/specification#/returns-create_for_direct) Почты России для работы с услугой Легкий возврат. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.  

<a name="return-by-pro"><h3>Создание возвратного отправления для ранее созданного отправления</h3></a> 
Создает возвратное отправление (ЛВ) для уже созданного в ЛК отправления.     

**Входные параметры:**
- *$rpo* - ШПИ прямого отправления;  
- *$mail_type* - Вид РПО. [См. Вид РПО](Enum/MailType.php).    


**Пример получения списка текущих точек сдачи:**
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

$otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
$result = $otpravkaApi->returnShipment(123456, \LapayGroup\RussianPost\Enum\MailType::UNDEFINED);

// Успешный ответ
/*Array
(
    [return-barcode] => 1234567890 // ШПИ возвратного отправления
)

// Ответ с ошибкой
/*Array
(
    [errors] => Array
        (
            [0] => Array
                (
                    [code] => DIRECT_SHIPMENT_NOT_FOUND
                    [description] => Прямое отправление не найдено
                )

        )

)*/
```


<a name="return-without-rpo"><h3>Создание отдельного возвратного отправления</h3></a> 
Создает возвратное отправление (ЛВ) без прямого.  
Метод asArr() проверяет заполнение необходимых для создания возвратного отправления полей и в случае незаполнения выбрасывает \InvalidArgumentException.  

**Пример создания заказа:**
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    
    $addressFrom = new \LapayGroup\RussianPost\Entity\AddressReturn();
    $addressFrom->setAddressType(\LapayGroup\RussianPost\Enum\AddressType::DEFAULT);
    $addressFrom->setIndex(125009);
    $addressFrom->setPlace('Москва');
    $addressFrom->setRegion('Москва');

    $addressTo = new \LapayGroup\RussianPost\Entity\AddressReturn();
    $addressTo->setAddressType(\LapayGroup\RussianPost\Enum\AddressType::DEFAULT);
    $addressTo->setIndex(115551);
    $addressTo->setPlace('Москва');
    $addressTo->setRegion('Москва');

    $return_shipment = new \LapayGroup\RussianPost\Entity\ReturnShipment();
    $return_shipment->setMailType(\LapayGroup\RussianPost\Enum\MailType::UNDEFINED);
    $return_shipment->setSenderName('Иванов Иван');
    $return_shipment->setRecipientName('Петров Петр');
    $return_shipment->setOrderNum(1234);
    $return_shipment->setAddressFrom($addressFrom);
    $return_shipment->setAddressTo($addressTo);

    $result = $otpravkaApi->createReturnShipment([$return_shipment->asArr()]);
    
    // Успешный ответ
    // TODO добавьте в PR, если у кого есть реальный пример, пожалуйста :-)
    
    // Ответ с ошибкой
    /*Array
    (
        [0] => Array
        (
            [errors] => Array
            (
                [0] => Array
                (
                    [code] => FREE_ER_ADDRESS_NOT_ENABLED
                    [description] => Свободный ввод адреса не доступен
                        )

                )

            [position] => 0
        )
    )*/
}
    
catch (\InvalidArgumentException $e) {
  // Обработка ошибки заполнения параметров
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```

<a name="return-delete"><h3>Удаление отдельного возвратного отправления</h3></a> 
Удаляет отдельное возвратное отправление.     

**Входные параметры:**
- *$rpo* - ШПИ возвратного отправления.

**Выходные параметры:**
- *code* - [Код ошибки](https://otpravka.pochta.ru/specification#/enums-returns-errors);  
- *description* - Описание ошибки.  


**Пример получения списка текущих точек сдачи:**
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

$otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
$result = $otpravkaApi->deleteReturnShipment(123456);
   
/*Array
(
    [code] => RETURN_SHIPMENT_NOT_FOUND
    [description] => Возвратное отправление не найдено
)*/
```

<a name="return-edit"><h3>Редактирование отдельного возвратного отправления</h3></a> 
Редактирование отдельного возвратного отправления (ЛВ).  
Метод asArr() проверяет заполнение необходимых для создания возвратного отправления полей и в случае незаполнения выбрасывает \InvalidArgumentException.  

**Пример создания заказа:**
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

try {
    $otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
    
    $addressFrom = new \LapayGroup\RussianPost\Entity\AddressReturn();
    $addressFrom->setAddressType(\LapayGroup\RussianPost\Enum\AddressType::DEFAULT);
    $addressFrom->setIndex(125009);
    $addressFrom->setPlace('Москва');
    $addressFrom->setRegion('Москва');

    $addressTo = new \LapayGroup\RussianPost\Entity\AddressReturn();
    $addressTo->setAddressType(\LapayGroup\RussianPost\Enum\AddressType::DEFAULT);
    $addressTo->setIndex(115551);
    $addressTo->setPlace('Москва');
    $addressTo->setRegion('Москва');

    $return_shipment = new \LapayGroup\RussianPost\Entity\ReturnShipment();
    $return_shipment->setMailType(\LapayGroup\RussianPost\Enum\MailType::UNDEFINED);
    $return_shipment->setSenderName('Иванов Иван');
    $return_shipment->setRecipientName('Петров Петр');
    $return_shipment->setOrderNum(1234);
    $return_shipment->setAddressFrom($addressFrom);
    $return_shipment->setAddressTo($addressTo);

    $result = $otpravkaApi->editReturnShipment($return_shipment, 123456);
    
    // Успешный ответ
    // TODO добавьте в PR, если у кого есть реальный пример, пожалуйста :-)
    
    // Ответ с ошибкой
    /* Array
    (
        [errors] => Array
            (
                [0] => Array
                    (
                        [code] => FREE_ER_ADDRESS_NOT_ENABLED
                        [description] => Свободный ввод адреса не доступен
                    )

            )

    )*/
}
    
catch (\InvalidArgumentException $e) {
  // Обработка ошибки заполнения параметров
}
        
catch (\LapayGroup\RussianPost\Exceptions\RussianPostException $e) {
  // Обработка ошибочного ответа от API ПРФ
}

catch (\Exception $e) {
  // Обработка нештатной ситуации
}
```


<a name="settings"><h1>Настройки</h1></a>  
Реализует функции [API](https://otpravka.pochta.ru/specification#/settings-shipping_points) Почты России для работы с настройками. 
Для работы данных функций необходимы аутентификационные данные. Подробнее в разделе [Конфигурация](#configfile).

В случае возникновеня ошибок при обмене выбрасывает исключение *\LapayGroup\RussianPost\Exceptions\RussianPostException*
в котором будет текст и код ошибки от API Почты России и дамп сырого ответа с HTTP-кодом.  

<a name="settings_shipping_points"><h3>Текущие точки сдачи</h3></a> 
Возвращает список текущих точек сдачи.  

**Пример получения списка текущих точек сдачи:**
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

$otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
$list = $otpravkaApi->shippingPoints();
```

<a name="get_settings"><h3>Текущие настройки пользователя</h3></a> 
Возвращает текущие настройки пользователя.  

**Пример получения списка текущих точек сдачи:**
```php
<?php
use Symfony\Component\Yaml\Yaml;
use LapayGroup\RussianPost\Providers\OtpravkaApi;

$otpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
$info = $otpravkaApi->settings();
```
