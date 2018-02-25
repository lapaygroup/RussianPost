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


### Рассчет стоимости пересылки (Упрощенная версия)
Рассчитывает стоимость пересылки в зависимости от указанных входных данных. Индекс ОПС точки отправления берется из профиля клиента. 
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
**$tariffInfo** - это объект класса *LapayGroup\RussianPost\tariffInfo* содержащий данные по рассчитанному тарифу.


# Трекинг почтовых отправлений (РПО)
// Скоро!

