# Changelog
- 0.2.0 - Расчет стоимости отправки тарификатором Почты России  
- 0.3.0 - Нормализация данных, упрощенный расчет стоимости отправки  
- 0.4.0 - Единичный и пакетный трекинг отправлений

# Установка
Для установки можно использовать менеджер пакетов Composer

    composer require lapaygroup/russianpost
    
# Тарификатор Почты России  
### Получения списка видов отправления
Для получения списка категорий нужно вызвать метод **parseToArray** класса **\LapayGroup\RussianPost\CategoryList**
```php
<?php
  $CategoryList = \LapayGroup\RussianPost\CategoryList::getInstance();
  $categoryList = $CategoryList->parseToArray();
?>
```
В $categoryList мы получим ассоциативный массив из категорий, их подкатегорий и видов почтовых отправлений с возможными опциями и списком параметров, которые нужно передать для расчета тарифа. По этим данным можно легко и быстро построить форму-калькулятор аналогичную [тарификатору Почты России](http://tariff.russianpost.ru/).
    
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

# Обработка данных
Реализует функции [API](https://otpravka.pochta.ru/specification#/nogroup-normalization_adress) Почты России для работы с данными. 
Для работы данных функций необходим [конфигурационный файл](config.yaml.example) с логином и паролем от сервиса Почты России.

### Нормализация адреса
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
  
  $OtpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
  
  $addressList = new \LapayGroup\RussianPost\AddressList();
  $addressList->add('115551 Кширское шоссе 94-1, 1');
  $result = $OtpravkaApi->clearAddress($addressList);
?>
```
**$addressList** - это объект класса *LapayGroup\RussianPost\AddressList* содержащий список адресов для нормализации.


### Нормализация ФИО
Очищает, разделяет и помещает значения ФИО в соответствующие поля возвращаемого объекта. 
Параметр id (идентификатор записи) используется для установления соответствия переданных и полученных записей, 
так как порядок сортировки возвращаемых записей не гарантируется.  

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  
  $OtpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
  
  $fioList = new \LapayGroup\RussianPost\FioList();
  $fioList->add('Иванов Петр игоревич');
  $result = $OtpravkaApi->clearFio($fioList);
?>
```
**$fioList** - это объект класса *LapayGroup\RussianPost\FioList* содержащий список ФИО для нормализации.


### Нормализация телефона
Принимает номера телефонов в неотформатированном виде, который может включать пробелы, символы: +-(). 
Очищает, разделяет и помещает сущности телефона (код города, номер) в соответствующие поля возвращаемого объекта. 
Параметр id (идентификатор записи) используется для установления соответствия переданных и полученных записей, 
так как порядок сортировки возвращаемых записей не гарантируется.

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  
  $OtpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
  
  $phoneList = new \LapayGroup\RussianPost\PhoneList();
  $phoneList->add('9260120934');
  $result = $OtpravkaApi->clearPhone($phoneList);
?>
```
**$phoneList** - это объект класса *LapayGroup\RussianPost\PhoneList* содержащий список номеров телефлонов для нормализации.


### Расчет стоимости пересылки (Упрощенная версия)
Расчитывает стоимость пересылки в зависимости от указанных входных данных. Индекс ОПС точки отправления берется из профиля клиента. 
Возвращаемые значения указываются в копейках.

**Пример вызова:**
```php
<?php
  use Symfony\Component\Yaml\Yaml;
  use LapayGroup\RussianPost\Providers\OtpravkaApi;
  use LapayGroup\RussianPost\ParcelInfo;
  
  $OtpravkaApi = new OtpravkaApi(Yaml::parse(file_get_contents('path_to_config.yaml')));
  
  $parcelInfo = new ParcelInfo();
  $parcelInfo->setIndexTo(644015);
  $parcelInfo->setMailCategory('ORDINARY'); // https://otpravka.pochta.ru/specification#/enums-base-mail-category
  $parcelInfo->setMailType('POSTAL_PARCEL'); // https://otpravka.pochta.ru/specification#/enums-base-mail-type
  $parcelInfo->setWeight(1000);
  $parcelInfo->setFragile(true);

  $tariffInfo = $OtpravkaApi->getDeliveryTariff($parcelInfo);
  echo $tariffInfo->getTotalRate()/100 . ' руб.';
?>
```
**$parcelInfo** - это объект класса *LapayGroup\RussianPost\ParcelInfo* содержащий данные по отправлению.
**$tariffInfo** - это объект класса *LapayGroup\RussianPost\tariffInfo* содержащий данные по расчитанному тарифу.


# Трекинг почтовых отправлений (РПО)
Реализует функции [API]( https://tracking.pochta.ru/specification) Почты России для работы с отправлениями.
Для работы данных функций необходим [конфигурационный файл](config.yaml.example) с логином и паролем от сервиса Почты России.

Для работы используется экземпляр класса *LapayGroup\RussianPost\Providers\Tracking*.  

**Входные параметры:**
- *$service* - Единочный (single) / Пакетный (pack) 
- *$config* - Массив данных для подключения к API
- *$timeout* - Таймаут HTTP соединения, по умолчанию 60 сек. (Для сервисов Почты России лучше использовать 120 сек.)

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
(на практике бывает и часто, требуется повторный запрос на создание)    

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
объетов в формате Почты России расщиренный свойством *OperCtgName*, 
которое содержит текстовое название подтипа операции.

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
                )

```