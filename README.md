[![Latest Stable Version](https://poser.pugx.org/lapaygroup/russianpost/v/stable)](https://packagist.org/packages/lapaygroup/russianpost)
[![Total Downloads](https://poser.pugx.org/lapaygroup/russianpost/downloads)](https://packagist.org/packages/lapaygroup/russianpost)
[![License](https://poser.pugx.org/lapaygroup/russianpost/license)](https://packagist.org/packages/lapaygroup/russianpost)
[![Telegram Chat](https://img.shields.io/badge/telegram-chat-blue.svg?logo=telegram)](https://t.me/phppochtarusdk)

# SDK для интеграции с программным комплексом [Почты России](https://www.pochta.ru/support/business/api).  

Посмотреть все проекты или поддержать автора можно [тут](https://lapay.group/opensource).  

**Почта России** ищет опытных разработчиков, которые будут заниматься созданием модулей для CMS, CRM и ERP систем, а так же  интеграциями с ПО ключевых клиентов. Работа в Москве, БЦ Даниловская Мануфактура (м. Тульская). Хочешь принять участие в масштабировании национального почтового оператора? Тогда эта вакансия для тебя. Ознакомиться с вакансиями можно [тут](https://hr.pochta.tech/vacancies?department=tehnologii-dlya-elektronnoy-kommertsii).  

# Содержание    
- [Changelog](#changelog)  
- [Тарификатор Почты России](#tariffs)    
- [Конфигурация](#configfile)  
- [Трекинг почтовых отправлений (РПО)](#tracking)  
- [Данные](#data)
  - [Нормализация адреса](#clean_address)  
  - [Нормализация ФИО](#clean_fio)  
  - [Нормализация телефона](#clean_phone)  
  - [Расчет стоимости пересылки (Упрощенная версия)](#calc_delivery_tariff) 
  - [Расчет сроков доставки](#calc_delivery_period)  
  - [Отображение баланса](#show_balance)   
  - [Неблагонадёжный получатель](#untrustworthy_recipient)    
- [Заказы](#orders)  
  - [Создание заказа](#create_order)  
  - [Редактирование заказа](#edit_order)   
  - [Удаление заказа](#delete_order)   
  - [Поиск заказа](#search_order)  
  - [Поиск заказа по идентификатору](#search_order_by_id)  
  - [Возврат заказов в "Новые"](#return_order_to_new)    
- [Партии](#party)  
  - [Создание партии из N заказов](#)  
  - [Изменение дня отправки в почтовое отделение](#)  
  - [Перенос заказов в партию](#)  
  - [Поиск партии по наименованию](#)  
  - [Поиск заказов с ШПИ](#)  
  - [Добавление заказов в партию](#)  
  - [Удаление заказов из партии](#)  
  - [Запрос данных о заказах в партии](#)  
  - [Поиск всех партий](#)  
  - [Поиск заказа в партии по id](#)  
- [Документы](#documents)  
- [Архив](#archive)
- [Поиск ОПС](#ops_search)  
- [Долгосрочное хранение](#warehouse)  
- [Настройки](#settings)  

<a name="links"><h1>Changelog</h1></a>

- 0.2.0 - Расчет стоимости отправки тарификатором Почты России;   
- 0.3.0 - Нормализация данных, упрощенный расчет стоимости отправки;    
- 0.4.0 - Единичный и пакетный трекинг отправлений;  
- 0.4.5 - Актуализация списка статусов, признак конечного статуса в пакетном трекинге;  
- 0.4.6 - Было принято решение исключить зависимость с [symfony/yaml](https://packagist.org/packages/symfony/yaml) и понизить требуемую версию PHP до 5.5+. Подробнее в разделе [Конфигурация](#configfile);
- 0.4.7 - Актуализация списка статусов;
- 0.4.8 - Изменен адрес калькулятора Почты России, старый будет отключен 01.01.2019;
- 0.4.9 - Исправлена ошибка выставления флага isFinal в пакетном трекинге отправлений, за обнаружение спасибо [Dmitry Sobchenko](https://github.com/sharpensteel);  
- 0.4.10 - Актуализирован расчет стоимости пересылки (Упрощенная версия), за актуализацию спасибо [rik43](https://github.com/rik43);  
- 0.4.11 - Актуализирован список статусов Почты России;
- 0.4.12 - Скорректировано описание упрощенной версии расчета тарифов, добавлен метод получения списка точек сдачи.


# Установка  
Для установки можно использовать менеджер пакетов Composer

    composer require lapaygroup/russianpost
       
<a name="tariffs"><h1>Тарификатор Почты России</h1></a>  

### Получения списка видов отправления
Для получения списка категорий нужно вызвать метод **parseToArray** класса **\LapayGroup\RussianPost\CategoryList**
```php
<?php
  $CategoryList = \LapayGroup\RussianPost\CategoryList::getInstance();
  $categoryList = $CategoryList->parseToArray();
?>
```
В $categoryList мы получим ассоциативный массив из категорий, их подкатегорий и видов почтовых отправлений с возможными опциями и списком параметров, которые нужно передать для расчета тарифа. По этим данным можно легко и быстро построить форму-калькулятор аналогичную [тарификатору Почты России](https://tariff.pochta.ru/).
    
Если нужно исключить категории из выборки, то перед вызовом **parseToArray** вызываем метод **setCategoryDelete** и передаем ему массий ID категорий, которые нужно исключить.
```php
<?php
  $CategoryList = \LapayGroup\RussianPost\CategoryList::getInstance();
  $CategoryList->setCategoryDelete([100,200,300]);
  $categoryList = $CategoryList->parseToArray();
?>
```
### Расчет стоимости отправки
**objectId**, список параметров в **$params** и список дополнительных услуг **$service** берутся из массива **$categoryList**.
```php
<?php
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
  $service = [2,21];

  $TariffCalculation = LapayGroup\RussianPost\TariffCalculation::getInstance();
  $calcInfo = $TariffCalculation->calculate($objectId, $params, $services);
?>
```
**$calcInfo** - это объект класса *LapayGroup\RussianPost\CalculateInfo*
Доступные методы:
 - *getError()* - возвращает флаг наличия ошибки при расчета (true - есть ошибка, false - нет ошибки)
 - *getErrorList()* - массив с текстовым описанием ошибок при расчете
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


### Пакетный доступ
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
use LapayGroup\RussianPost\ParcelInfo;

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

