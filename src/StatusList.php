<?php
namespace LapayGroup\RussianPost;

use LapayGroup\RussianPost\Exceptions\StatusValidationException;

// Список статусов Почты России
class StatusList
{
    // Карта сопоставления статусов по кодам статуса и подстатуса
    private $status_map = [
        '1' => [ // Прием
            '1' => 'Единичный',
            '2' => 'Партионный',
            '3' => 'Партионный электронно',
            '4' => 'Упрощенный единичный',
            '5' => 'По ведомости в РЦ (распределительном центре)',
            '6' => 'По ведомости в СЦ (сортировочном центре',
            '7' => 'Упрощенный предзаполненный',
            '8' => 'Упрощенный предоплаченный',
            '9' => 'В соответствие с Поручением экспедитору',
            '10' => 'По поручению адресата',
            '11' => 'Партионный почтальоном',
        ],
        '2' => [ // Вручение
            '0' => '',
            '1' => 'Вручение адресату', // Конечный
            '2' => 'Вручение отправителю', // Конечный
            '3' => 'Выдано адресату через почтомат', // Конечный
            '4' => 'Выдано отправителю через почтомат', // Конечный
            '5' => 'Адресату электронно', // Конечный
            '6' => 'Адресату почтальоном', // Конечный
            '7' => 'Отправителю почтальоном', // Конечный
            '8' => 'Адресату курьером', // Конечный
            '9' => 'Отправителю курьером', // Конечный
            '10' => 'Адресату с контролем ответа', // Конечный
            '11' => 'Адресату с контролем ответа почтальоном', // Конечный
            '12' => 'Адресату с контролем ответа курьером', // Конечный
            '13' => 'Вручение адресату по ПЭП', // Конечный
            '14' => 'Для передачи на оцифровку', // Конечный
            '15' => 'Адресату Экспедитором', // Конечный
            '16' => 'Отправителю Экспедитором', // Конечный
            '17' => 'Адресату почтальоном по ПЭП', // Конечный
            '18' => 'Адресату курьером по ПЭП', // Конечный
            '19' => 'Вручение адресату роботом-курьером', // Конечный
            '20' => 'Вручение отправителю роботом-курьером', // Конечный
            '21' => 'Вручение адресату через ААПС', // Конечный
            '22' => 'Вручение отправителю через ААПС', // Конечный
            '23' => 'Вручение адресату через АПШ', // Конечный
            '24' => 'Вручение отправителю через АПШ', // Конечный
            '25' => 'Адресату по QR коду',
            '26' => 'Адресату почтальоном по QR коду',
            '27' => 'Адресату курьером по QR коду',
            '28' => 'Отправителю по QR коду',
            '29' => 'Отправителю почтальоном по QR коду',
            '30' => 'Отправителю курьером по QR коду',
            '31' => 'Отправителю курьером по ПЭП',
        ],
        '3' => [ // Возврат
            '1' => 'Истёк срок хранения',
            '2' => 'Заявление отправителя',
            '3' => 'Отсутствие адресата по указанному адресу',
            '4' => 'Отказ адресата',
            '5' => 'Смерть адресата',
            '6' => 'Невозможно прочесть адрес адресата',
            '7' => 'Отказ в выпуске таможней',
            '8' => 'Адресат, абонирующий абонементный почтовый шкаф, не указан или указан неправильно',
            '9' => 'Иные обстоятельства',
            '10' => 'Неверный адрес',
            '11' => 'Несоответствие комплектности',
            '12' => 'Запрещено САБ',
            '13' => 'Для проведения таможенных операций',
            '14' => 'Распоряжение ЭТП',
            '15' => 'Частичный выкуп',
            '16' => 'По согласованию с адресатом',
            '17' => 'Несоответствие возраста получателя 18+',
            '18' => 'ПВЗ закрыт',
            '19' => 'Отказ ПВЗ в приеме',
            '20' => 'Отсутствие беспрепятственного доступа к адресату',
            '21' => 'Нет доступа к абонентским п/я или они неисправны',
            '22' => 'Адрес отсутствует',
            '23' => 'По причине дефектности	',
        ],
        '4' => [ // Досылка почты
            '1' => 'По заявлению пользователя',
            '2' => 'Выбытие адресата по новому адресу',
            '3' => 'Засылка',
            '4' => 'Запрещено САБ',
            '5' => 'Передача на временное хранение',
            '6' => 'Передача в невостребованные',
            '7' => 'По техническим причинам',
            '8' => 'По согласованию с адресатом',
            '9' => 'Отсутствует ПЭП',
            '10' => 'Требуется оплата ТПО',
            '11' => 'Дефектность отправления',
            '12' => 'Превышение габаритных размеров',
            '13' => 'Истечение срока хранения в АПС',
            '14' => 'Нет свободных ячеек',
            '15' => 'ПЭП подключен',
            '16' => 'ТПО оплачен',
            '17' => 'ПЭП подключен и ТПО оплачен',
            '18' => 'По заявлению пользователя на конкретное РПО',
            '19' => 'По заявлению адресата на все поступающие РПО	',
        ],
        '5' => [ // Невручение
            '1' => 'Утрачено', // Конечный
            '2' => 'Изъято', // Конечный
            '3' => 'Засылка',
            '8' => 'Решение таможни',
            '9' => '(зарезервировано)'
        ],
        '6' => [ // Хранение
            '1' => 'До востребования',
            '2' => 'На абонементный ящик',
            '3' => 'Установленный срок хранения',
            '4' => 'Продление срока хранения по заявлению адресата',
            '5' => 'Продление срока хранения по заявлению отправителя'
        ],
        '7' => [ // Временное хранение
            '1' => 'Нероздано',
            '2' => 'Невостребовано',
            '3' => 'Содержимое запрещено к пересылке',
            '4' => 'Ожидает результаты экспертизы'
        ],
        '8' => [ // Обработка
            '0' => 'Сортировка',
            '1' => 'Покинуло место приёма',
            '2' => 'Прибыло в место вручения',
            '3' => 'Прибыло в сортировочный центр',
            '4' => 'Покинуло сортировочный центр',
            '5' => 'Прибыло в место международного обмена',
            '6' => 'Покинуло место международного обмена',
            '7' => 'Прибыло в место транзита',
            '8' => 'Покинуло место транзита',
            '9' => 'Прибыло в почтомат',
            '10' => 'Истекает срок хранения в почтомате',
            '11' => 'Переадресовано в почтомат',
            '12' => 'Изъято из почтомата',
            '13' => 'Прибыло на территорию РФ',
            '14' => 'Прибыло в Центр выдачи посылок',
            '15' => 'Передано курьеру',
            '16' => 'Доставлено для вручения электронно',
            '17' => 'Прибыло в ЦГП',
            '18' => 'Передано почтальону',
            '19' => 'Передача в кладовую хранения',
            '20' => 'Покинуло место возврата/досылки',
            '21' => 'Уточнение адреса',
            '22' => 'Ожидает курьерской доставки',
            '23' => 'Продление срока хранения',
            '24' => 'Направлено извещение',
            '25' => 'Доставлено извещение',
            '26' => 'Зарегистрировано с новым номером',
            '27' => 'Истекает срок хранения (осталось 25 дней)',
            '28' => 'Истекает срок хранения (осталось 5 дней)',
            '29' => 'Находится на техническом исследовании',
            '30' => 'Поступило в ЦГП',
            '31' => 'Покинуло ЦГП',
            '32' => 'Направлено в УКД',
            '33' => 'Истекает срок хранения (осталось 23 дня)',
            '34' => 'До ввод данных',
            '35' => 'Истекает срок хранения (осталось 10 дней)',
            '36' => 'Зарегистрировано заявление на возврат',
            '37' => 'Зарегистрировано заявление отправителя на продление срока хранения',
            '38' => 'Зарегистрировано заявление адресата на продление срока хранения',
            '39' => 'Сдано в ПВЗ',
            '40' => 'Истек срок хранения',
            '41' => 'Возврат скомплектован',
            '42' => 'Истекает срок хранения (остался 1 день)',
            '43' => 'Истек срок хранения (осталось 2 дня)',
            '44' => 'Сдано в СЦ',
            '45' => 'Резерв',
            '46' => 'Резерв',
            '47' => 'Приостановлена обработка по 115-ФЗ',
            '48' => 'Присвоено место адресного хранения',
            '49' => 'Направлено в пункт выдачи',
            '50' => 'Отправка запроса в Колл-центр',
            '51' => 'Фиксация веса после таможенного осмотра',
            '52' => 'Вручение разрешено',
            '53' => 'Вручение не разрешено',
            '54' => 'Бронирование ячейки почтомата, отправление в очереди',
            '55' => 'Ожидает закладку',
            '56' => 'Хранение отправления в адресном ОПС 2 дня до окончания срока хранения',
            '57' => 'Хранение отправления в адресном ОПС 5 дней после доставки в ОПС',
            '58' => 'Хранение отправления в адресном ПВЗ 5 дней',
            '59' => 'Хранение отправления в адресном ОПС 5 дней после доставки в ОПС',
            '60' => 'Заложено в ячейку АПШ/ААПС',
            '61' => 'Зарегистрировано заявление получателя на досыл',
            '62' => 'Передано в клиентский зал',
            '63' => 'Отправка заявки в КЦ',
            '64' => 'Изъятие из АПШ/ААПС',
            '65' => 'Отправление отсутствует',
            '66' => 'Запрос согласия на вручение через а/я',
            '67' => 'Согласие на вручение через а/я',
            '68' => 'Передано курьеру для доставки в АПС',
            '69' => 'Передано курьеру для доставки в ПВЗ',
            '70' => 'Принято курьером для доставки в УКД',
            '71' => 'Направлено в ОПС вручения',
            '72' => 'Направлено в УОПК',
            '73' => 'Прибыло в УОПК',
            '74' => 'Заложено в ячейку АПС',
            '75' => 'Забор из АПС',
            '76' => 'Вынужденная переадресация отправлений в ОПС',
            '77' => 'Принято курьером для доставки в СЦ',
            '78' => 'Запрос отмены',
        ],
        '9' => [ // Импорт международной почты
            '1' => 'Поступило приписано',
            '2' => 'Поступило не приписано',
        ],
        '10' => [ // Экспорт международной почты (БЕЗ АТРИБУТА)
            '0' => 'Экспорт международной почты'
        ],
        '11' => [ // Прием на таможню (БЕЗ АТРИБУТА)
            '0' => 'Прием на таможню'
        ],
        '12' => [ // Неудачная попытка вручения
            '1' => 'Временное отсутствие адресата',
            '2' => 'Доставка отложена по просьбе адресата',
            '3' => 'Неполный адрес',
            '4' => 'Неправильный адрес',
            '5' => 'Адресат выбыл',
            '6' => 'Отказ от получения',
            '7' => 'Обстоятельства непреодолимой силы',
            '8' => 'Иная',
            '9' => 'Адресат заберет отправление сам',
            '10' => 'Нет адресата',
            '11' => 'По техническим причинам',
            '12' => 'Истек срок хранения в почтомате',
            '13' => 'По требованию отправителя',
            '14' => 'Отправление повреждено и/или без вложения',
            '15' => 'В ожидании оплаты сбора',
            '16' => 'Адресат переехал',
            '17' => 'У адресата есть абонентский ящик',
            '18' => 'Нет доставки на дом',
            '19' => 'Не отвечает таможенным требованиям',
            '20' => 'Неполные/недостаточные/неверные документы',
            '21' => 'Невозможно связаться с клиентом',
            '22' => 'Адресат бастует',
            '23' => 'Запрещенные вложения – отправление не доставлено',
            '24' => 'Отказ в импорте – запрещенные вложения',
            '25' => 'Засыл отправления',
            '26' => 'За смертью получателя',
            '27' => 'Национальный праздник',
            '28' => 'Утрата',
            '29' => 'По распоряжению адресата',
            '30' => 'Несоответствие возраста получателя 18+',
        ],
        '13' => [ // Регистрация отправки (БЕЗ АТРИБУТА)
            '0' => 'Регистрация отправки'
        ],
        '14' => [ // Таможенное оформление
            '1' => 'Выпущено таможней',
            '2' => 'Возвращено таможней',
            '3' => 'Осмотрено таможней',
            '4' => 'Отказ в выпуске',
            '5' => 'Направлено с таможенным уведомлением',
            '6' => 'Направлено с обязательной уплатой таможенных платежей',
            '7' => 'Требуется досмотр',
            '8' => 'Выпуск приостановлен',
            '9' => 'Отказ в приеме на таможню',
            '10' => 'Проведен осмотр с использованием ТСТК',
            '11' => 'ТК в рамках СУР*',
            '12' => 'Досмотр завершен',
            '13' => 'Выпуск разрешен. Таможенные платежи уплачены',
            '14' => 'Отказ в выпуске. Таможенные платежи не уплачены',
            '15' => 'Таможенная декларация не прошла ФЛК',
            '16' => 'Выпущено таможней. Средства зарезервированы',
            '17' => 'Отказ в выпуске по решению должностного лица',
            '18' => 'Требуется предъявление в ТО',
            '19' => 'Отказ в автоматическом выпуске',
            '20' => 'Отказ в выпуске. Товары не предъявлены',
            '21' => 'Требуются результаты осмотра',
            '22' => 'Возврат разрешен',
            '23' => 'Отказ в выпуске возвращаемых товаров',
            '24' => 'Требуется рентген-контроль',
            '25' => 'В убытии отказано',
            '26' => 'Убытие разрешено',
        ],
        '15' => [ // Передача на временное хранение (БЕЗ АТРИБУТА) (КОНЕЧНЫЙ)
            '0' => 'Передача на временное хранение',
        ],
        '16' => [ // Уничтожение (БЕЗ АТРИБУТА) (КОНЕЧНЫЙ)
            '0' => 'Уничтожение',
        ],
        '17' => [ // Передача вложения на баланс (БЕЗ АТРИБУТА) (КОНЕЧНЫЙ)
            '0' => 'Передача вложения на баланс',
        ],
        '18' => [ // Регистрация утраты (БЕЗ АТРИБУТА) (КОНЕЧНЫЙ)
            '0' => 'Регистрация утраты',
        ],
        '19' => [ // Таможенные платежи поступили (БЕЗ АТРИБУТА)
            '0' => 'Таможенные платежи поступили',
        ],
        '20' => [ // Регистрация (БЕЗ АТРИБУТА)
            '0' => 'Регистрация',
        ],
        '21' => [ // Доставка
            '1' => 'Доставлено в почтовый ящик',
            '2' => 'Доставлено в руки адресату под роспись',
            '3' => 'Доставлено на ресепшн или секретариат под роспись'
        ],
        '22' => [ // Недоставка
            '1' => 'Отсутствие п/я',
            '2' => 'Отсутствие улицы, дома, квартиры',
            '3' => 'Несоответствие индекса ОПС выдачи',
            '4' => 'Неверный адрес/адрес не существует',
            '5' => 'Отказ в получении',
            '6' => 'Нет доступа по адресу',
            '7' => 'Организация сменила адрес',
            '8' => 'Адресат выбыл',
            '9' => 'Отправление не поместилось в п/я'
        ],
        '23' => [ // Поступление на временное хранение
            '0' => 'Поступление на временное хранение'
        ],
        '24' => [ // Продление срока выпуска таможней
            '1' => 'Предъявить на ветеринарный контроль',
            '2' => 'Предъявить на фитосанитарный контроль',
            '3' => 'Отбор проб и образцов',
            '4' => 'Прочее',
            '5' => 'Контакт с клиентом для запроса информации невозможен',
            '1019' => 'Запрещенные объекты',
            '1020' => 'Имеются ограничения на импортируемые вложения',
            '1050' => 'Счет отсутствует',
            '1051' => 'Счет некорректен',
            '1052' => 'Сертификат отсутствует или некорректен',
            '1053' => 'Сертификат некорректен',
            '1054' => 'Таможенная декларация отсутствует или некорректна',
            '1055' => 'CN 22/23 некорректна',
            '1056' => 'Вложения с высокой стоимостью - требуется ТД',
            '1057' => 'Контакт с клиентом невозможен',
            '1058' => 'Ожидается передача в таможенный орган',
            '1059' => 'Требуются данные НДС/Номер ввоза',
            '1060' => 'Требуется сертификат для возвращенных вложений',
            '1061' => 'Требуется форма перевода для банка',
            '1062' => 'Некомплектная поставка',
            '1063' => 'Передано в таможенный орган',
            '1064' => 'Задержано в таможенном органе без указания причины',
            '1065' => 'Ожидается подтверждение стоимости от получателя',
            '1070' => 'Имеются ограничения на экспортируемые вложения',
            '1073' => 'Неполная или некорректная документация',
            '1099' => 'Прочее',
        ],
        '25' => [ // Вскрытие (БЕЗ АТРИБУТА)
            '0' => 'Вскрытие'
        ],
        '26' => [ // Отмена
            '1' => 'Требование отправителя',
            '2' => 'Ошибка оператора'
        ],
        '27' => [ // Получена электронная регистрация (БЕЗ АТРИБУТА)
            '0' => 'Получена электронная регистрация'
        ],
        '28' => [ // Присвоение идентификатора (БЕЗ АТРИБУТА)
            '0' => 'Присвоение идентификатора'
        ],
        '29' => [ // Регистрация прохождения в ММПО (БЕЗ АТРИБУТА)
            '0' => 'Регистрация прохождения в ММПО'
        ],
        '30' => [ // Отправка SRM (БЕЗ АТРИБУТА)
            '0' => 'Отправка SRM'
        ],
        '31' => [ // Обработка перевозчиком
            '1' => 'Транспорт прибыл',
            '5' => 'Бронирование подтверждено',
            '6' => 'Включено в план погрузки',
            '7' => 'Исключено из плана погрузки',
            '14' => 'Транспортный участок завершен',
            '21' => 'Доставлено',
            '23' => 'Почта в месте назначения',
            '24' => 'Погружено на борт',
            '31' => 'В пути',
            '40' => 'Прибыло на склад перевозчика',
            '41' => 'Перегрузка',
            '42' => 'Передано другому перевозчику',
            '43' => 'Получено от другого перевозчика',
            '48' => 'Погружено',
            '57' => 'Не погружено',
            '59' => 'Выгружено',
            '74' => 'Принято к перевозке',
            '82' => 'Возвращено'
        ],
        '32' => [ // Поступление АПО (БЕЗ АТРИБУТА)
            '0' => 'Поступление АПО'
        ],
        '33' => [ // Международная обработка
            '1' => 'Передано перевозчику',
            '2' => 'Получено назначенным оператором',
            '3' => 'Обработка назначенным оператором'
        ],
        '34' => [ // Электронное уведомление загружено (БЕЗ АТРИБУТА)
            '0' => 'Электронное уведомление загружено'
        ],
        '35' => [ // Отказ в курьерской доставке
            '1' => 'Не подлежащий доставке вид почтового отправления',
            '2' => 'Превышение предельного веса, подлежащее доставке',
            '3' => 'Превышение габаритных размеров, подлежащее доставке',
            '4' => 'Дефектное почтовое отправление',
            '5' => 'Наличие Таможенного уведомления',
            '6' => 'Отсутствие Соглашения об обмене почтовыми отправлениями с наложенным платежом',
            '7' => 'Возвращенное почтовое отправление',
            '8' => 'Превышение суммы наложенного платежа, подлежащей взиманию на дому',
            '9' => 'Неверно оформленные бланки или их отсутствие'
        ],
        '36' => [ // Уточнение вида оплаты доставки
            '0' => 'Включена в тариф',
            '1' => 'Платная',
            '2' => 'Предоплаченная'
        ],
        '37' => [ // Предварительное оформление (БЕЗ АТРИБУТА)
            '0' => 'Предварительное оформление'
        ],
        '38' => [ // Задержка для уточнений у отправителя
            '4' => 'Неправильный/нечитаемый/неполный адрес',
            '13' => 'По требованию отправителя',
            '25' => 'Засыл отправления'
        ],
        '39' => [ // Предварительное таможенное декларирование
            '1' => 'Регистрация',
            '2' => 'Предварительное решение "выпуск разрешен"',
            '3' => 'Отказ в выпуске товаров. Требуется предъявление таможенному органу без осмотра',
            '4' => 'Отказ в выпуске товаров. Требуется предъявление таможенному органу с осмотром',
            '5' => 'Отказ в регистрации',
            '6' => 'Отказ в выпуске. Товары не предъявлены',
            '7' => 'Данные от торговой площадки получены',
            '8' => 'Выпуск разрешен',
            '9' => 'Отказ в выпуске'
        ],
        '40' => [ // Таможенный контроль
            '1' => 'Платежи уплачены',
            '2' => 'Уведомление о проведении контроля',
            '3' => 'Отказ в выпуске по решению должностного лица',
        ],
        '41' => [ // Обработка таможенных платежей
            '1' => 'Сумма платежа удержана УО',
            '2' => 'Сумма платежа списана ФТС',
            '3' => 'Сумма платежа для удержания УО',
            '4' => 'Сумма платежа удержана УО полностью',
            '5' => 'Сумма платежа рассчитана УО',
            '6' => 'Сумма платежа удержана ФТС полностью'
        ],
        '42' => [ // Вторая неудачная попытка вручения
            '1' => 'Временное отсутствие адресата',
            '2' => 'Доставка отложена по просьбе адресата',
            '3' => 'Неполный адрес',
            '4' => 'Неправильный/нечитаемый/неполный адрес',
            '5' => 'Адресат выбыл',
            '6' => 'Адресат отказался от отправления',
            '7' => 'Форс-мажор/непредвиденные обстоятельства',
            '8' => 'Иная',
            '9' => 'Адресат заберет отправление сам',
            '10' => 'Адресат не доступен',
            '11' => 'Неудачная доставка',
            '12' => 'Истек срок хранения в почтомате',
            '13' => 'По требованию отправителя',
            '14' => 'Отправление повреждено и/или без вложения',
            '15' => 'В ожидании оплаты сбора',
            '16' => 'Адресат переехал',
            '17' => 'У адресата есть абонентский ящик',
            '18' => 'Нет доставки на дом',
            '19' => 'Не отвечает таможенным требованиям',
            '20' => 'Неполные/недостаточные/неверные документы',
            '21' => 'Невозможно связаться с клиентом',
            '22' => 'Адресат бастует',
            '23' => 'Запрещенные вложения – отправление не доставлено',
            '24' => 'Отказ в импорте – запрещенные вложения',
            '25' => 'Засыл отправления',
            '26' => 'За смертью получателя',
            '27' => 'Национальный праздник',
            '28' => 'Утрата',
            '29' => 'По распоряжению адресата',
            '30' => 'Несоответствие возраста получателя «18+»',
        ],
        '43' => [ // Вручение разрешено (БЕЗ АТРИБУТА)
            '0' => 'Вручение разрешено'
        ],
        '44' => [ // Отказ в приеме
            '1' => 'Нарушение применения тарифов',
            '2' => 'Несоответствие фактической массы',
            '3' => 'Несоответствие оформления',
            '4' => 'Нечитаемый ШИ',
            '5' => 'Повреждение упаковки',
            '6' => 'Нарушение подлинности ГЗПО',
            '7' => 'Штрих-код РПО был отсканирован ранее',
            '8' => 'Ошибка в адресе',
            '9' => 'Превышение габаритов',
            '10' => 'Некорректный индекс отправителя',
            '11' => 'Некорректный ЗОО',
            '12' => 'Отсутствует отправление',
            '13' => 'Несоответствие вложения',
            '14' => 'Возврат по условиям договора',
            '15' => 'Распоряжение ЭТП',
        ],
        '45' => [ // Отказ от отправки электронного уведомления получателем (БЕЗ АТРИБУТА)
            '0' => 'Отказ от отправки электронного уведомления получателем'
        ],
        '46' => [ // Отмена присвоения идентификатора (БЕЗ АТРИБУТА)
            '0' => 'Отмена присвоения идентификатора'
        ],
        '47' => [ // Подтверждение возможности приема (БЕЗ АТРИБУТА)
            '0' => 'Подтверждение возможности приема'
        ],
        '48' => [ // Частичное вручение (БЕЗ АТРИБУТА)
            '0' => 'Частичное вручение'
        ],
        '49' => [ // Отказ в продлении срока хранения
            '1' => 'Отправление уже вручено',
            '2' => 'Заявка на продление срока хранения получена повторно',
            '3' => 'Заявка на продление срока хранения подана позже, чем за 24 часа окончания нормативного срока хранения',
            '4' => 'Наличие дебиторской задолженности по корпоративному клиенту',
            '5' => 'Наличие запрета со стороны Компании дистанционной торговли (интернет-магазина) на продление срока хранения',
            '6' => 'ПВЗ закрыт'
        ],
        '50' => [ // Неудачная доставка в АПС
            '1' => 'АПС отсутствует',
            '2' => 'АПС не работает',
            '3' => 'К АПС нет доступа',
            '4' => 'Курьер не успел',
            '5' => 'Нет свободных ячеек в АПС',
            '6' => 'Курьер не смог совершить закладку',
            '7' => 'Размер открывшейся ячейки меньше размера отправления',
            '8' => 'Ячейка не открывается, сломана',
        ],
        '51' => [ // Неудачная доставка в ПВЗ
            '1' => 'ПВЗ закрыт временно',
            '2' => 'ПВЗ закрыт постоянно',
            '3' => 'Отказ ПВЗ в приеме',
            '4' => 'Курьер не успел',
            '5' => 'Отправление отсутствует',
            '6' => 'Засыл',
        ],
        '52' => [ // Отказ в резервировании ячейки АПС (БЕЗ АТРИБУТА)
            '0' => 'Отказ в резервировании ячейки АПС'
        ],
        '53' => [ // Бронирование интервала доставки
            '1' => 'Назначен интервал доставки при поступлении в место вручения',
            '2' => 'Назначен интервал доставки при оформлении отправления интернет-магазином',
            '3' => 'Назначен интервал доставки адресатом',
        ],
        '54' => [ // Изменение (перебронирование) интервала доставки
            '1' => 'Перенос по согласованию с клиентом',
            '2' => 'Перенос по инициативе Почты России',
        ],
        '55' => [ // Отказ от бронирования интервала доставки
            '1' => 'Возврат отправителю',
            '2' => 'Самостоятельный забор',
            '3' => 'Техническая причина',
            '4' => 'Нарушен срок сдачи отправителем',
            '5' => 'Отмена заявки на доставку клиентом',
            '6' => 'Отправление не прибыло в место вручения',
        ],
        '56' => [ // Возврат в адресное ОПС
            '1' => 'Адресат оставил РПО в ячейке ААПС',
        ],
        '57' => [ // Отказ закладки в АПС (БЕЗ АТРИБУТА)
            '0' => 'Отказ закладки в АПС'
        ],
        '58' => [ // Изменение способа вручения ММО (БЕЗ АТРИБУТА)
            '0' => 'Изменение способа вручения ММО'
        ],
        '59' => [ // Регистрация внешнего идентификатора (БЕЗ АТРИБУТА)
            '0' => 'Регистрация внешнего идентификатора'
        ],
        '60' => [
            '1' => 'Ячейка АПС пуста',
            '2' => 'Ячейка АПС не открылась	',
            '3' => 'АПС не работает',
            '4' => 'К АПС нет доступа',
            '5' => 'Дефектное отправление',
        ],
        '61' => [ // Передано ведомственному перевозчику (БЕЗ АТРИБУТА)
            '0' => 'Передано ведомственному перевозчику'
        ],
        '62' => [ // Возвращено клиенту (БЕЗ АТРИБУТА)
            '0' => 'Возвращено клиенту'
        ],
        '63' => [ // НП оплачен (БЕЗ АТРИБУТА)
            '0' => 'НП оплачен'
        ],
        '1027' => [
            '1' => 'Отсутствие в ёмкости',
            '2' => 'Отсутствие при инвентаризации',
            '3' => 'При приеме с транспортного средства',
            '4' => 'Отсутствие в наличии в ОСП',
        ]
    ];

    private $status_name_list = [
                                1 => 'Прием',
                                2 => 'Вручение',
                                3 => 'Возврат',
                                4 => 'Досылка почты',
                                5 => 'Невручение',
                                6 => 'Хранение',
                                7 => 'Временное хранение',
                                8 => 'Обработка',
                                9 => 'Импорт международной почты',
                                10 => 'Экспорт международной почты',
                                11 => 'Прием на таможню',
                                12 => 'Неудачная попытка вручения',
                                13 => 'Регистрация отправки',
                                14 => 'Таможенное оформление',
                                15 => 'Передача на временное хранение',
                                16 => 'Уничтожение',
                                17 => 'Оформление в собственность',
                                18 => 'Регистрация утраты',
                                19 => 'Таможенные платежи поступили',
                                20 => 'Регистрация',
                                21 => 'Доставка',
                                22 => 'Недоставка',
                                23 => 'Поступление на временное хранение',
                                24 => 'Продление срока выпуска таможней',
                                25 => 'Вскрытие',
                                26 => 'Отмена',
                                27 => 'Получена электронная регистрация',
                                28 => 'Присвоение идентификатора',
                                29 => 'Регистрация прохождения в ММПО',
                                30 => 'Отправка SRM',
                                31 => 'Обработка перевозчиком',
                                32 => 'Поступление АПО',
                                33 => 'Международная обработка',
                                34 => 'Электронное уведомление загружено',
                                35 => 'Отказ в курьерской доставке',
                                36 => 'Уточнение вида оплаты доставки',
                                37 => 'Предварительное оформление',
                                38 => 'Задержка для уточнений у отправителя',
                                39 => 'Предварительное таможенное декларирование',
                                40 => 'Таможенный контроль',
                                41 => 'Обработка таможенных платежей',
                                42 => 'Вторая неудачная попытка вручения',
                                43 => 'Вручение разрешено',
                                44 => 'Отказ в приеме',
                                45 => 'Отказ от отправки электронного уведомления получателем',
                                46 => 'Отмена присвоения идентификатора',
                                47 => 'Подтверждение возможности приема',
                                48 => 'Частичное вручение',
                                49 => 'Отказ в продлении срока хранения',
                                50 => 'Неудачная доставка в АПС',
                                51 => 'Неудачная доставка в ПВЗ',
                                52 => 'Отказ в резервировании ячейки АПС',
                                53 => 'Бронирование интервала доставки',
                                54 => 'Изменение (перебронирование) интервала доставки',
                                55 => 'Отказ от бронирования интервала доставки',
                                56 => 'Возврат в адресное ОПС',
                                58 => 'Изменение способа вручения ММО',
                                59 => 'Регистрация внешнего идентификатора',
                                60 => 'Неудачное изъятие из АПС',
                                61 => 'Передано ведомственному перевозчику',
                                62 => 'Возвращено клиенту',
                                1027 => 'Недостача',
                            ];

    /**
     * Проверяет признак конечного статуса
     * @return bool
     */
    public function isFinal($status_id, $substatus_id = 0)
    {
        if (!$substatus_id) $substatus_id = 0;

        $finalList = [
            2 => [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24],
            5 => [1, 2],
            15 => [0],
            16 => [0],
            17 => [0],
            18 => [0]
        ];

        return isset($finalList[$status_id]) && in_array($substatus_id, $finalList[$status_id]);
    }

    /**
     * Функция по коду статуса или статуса и подстатуса возвращает информацию по статусу
     * @param int $status_id код статуса
     * @param int $substatus_id код подстатуса (Если не передан, то вернет информацию только по статусу)
     * @param string $status_name название статуса (Если не передан, то возьмет название из справочника)
     * @return array
     */
    public function getInfo($status_id, $substatus_id = false, $status_name = false)
    {
        // Проверяем, что передан существующий код статуса
        if (empty($this->status_map[$status_id])) {
            throw new StatusValidationException('Неверный код статуса: '.$status_id, 404);
        }

        // Проверяем, что передан существующий код подстатуса
        if (isset($substatus_id) && empty($this->status_map[$status_id][$substatus_id])) {
            throw new StatusValidationException('Неверный код подстатуса: статус '.$status_id.', подстатус '.$substatus_id, 405);
        }

        if (empty($status_name))
            $status_name = !empty($this->status_name_list[$status_id]) ? $this->status_name_list[$status_id] : 'Не определено';

        $result['statusName'] = $status_name;
        $result['statusId'] = $status_id;
        $result['isFinal'] = $this->isFinal($status_id, $substatus_id);

        if (isset($substatus_id)) {
            $result['substatusId'] = $substatus_id;
            if(!empty($this->status_map[$status_id][$substatus_id])) {
                $result['substatusName'] = $this->status_map[$status_id][$substatus_id];
            } elseif (!empty($this->status_map[$status_id][0])) {
                $result['substatusName'] = $this->status_map[$status_id][0];
            } else {
                $result['substatusName'] = 'Не определено';
            }
        }

        return $result;
    }
}
