<?php

return [
    'navigation' => [
        'title' => 'Отправки',
    ],

    'global-search' => [
        'customer'  => 'Клиент',
        'state'     => 'Статус',
        'reference' => 'Ссылка клиента',
    ],

    'form' => [
        'tabs' => [
            'general'       => 'Общее',
            'route'         => 'Маршрут и точки',
            'cargo'         => 'Груз',
            'assignment'    => 'Назначение',
            'confidential'  => 'Конфиденциально',
        ],

        'fields' => [
            'number'                 => 'Номер отправки',
            'number-placeholder'     => 'Присваивается при сохранении отправки',
            'company'                => 'Компания',
            'customer'               => 'Клиент',
            'customer-reference'     => 'Ссылка клиента',
            'sale-order'             => 'Из заказа на продажу',
            'sale-order-helper'      => 'Копирует валюту и ссылку клиента из заказа на продажу этого клиента.',
            'service-type'           => 'Тип услуги',
            'transport-mode'         => 'Вид транспорта',
            'priority'               => 'Приоритет',
            'currency'               => 'Валюта',
            'origin'                 => 'Пункт отправления',
            'destination'            => 'Пункт назначения',
            'pickup-address'         => 'Адрес забора',
            'delivery-address'       => 'Адрес доставки',
            'planned-pickup-at'      => 'Плановый забор',
            'expected-delivery-at'   => 'Ожидаемая доставка',
            'stops'                  => 'Точки',
            'stop-type'              => 'Тип',
            'stop-address'           => 'Адрес',
            'stop-contact'           => 'Контакт',
            'stop-phone'             => 'Телефон',
            'stop-planned-arrival'   => 'Плановое прибытие',
            'stop-instructions'      => 'Инструкции',
            'declared-value'         => 'Объявленная стоимость',
            'is-fragile'             => 'Хрупкий',
            'is-hazardous'           => 'Опасный',
            'is-hazardous-helper'    => 'Только отметка. Документы на опасные грузы здесь не оформляются.',
            'cargo-lines'            => 'Груз',
            'line-description'       => 'Описание',
            'line-package-type'      => 'Тип упаковки',
            'line-quantity'          => 'Количество',
            'line-weight'            => 'Вес',
            'line-volume'            => 'Объём',
            'instructions'           => 'Особые указания',
            'dispatcher'             => 'Диспетчер',
            'carrier'                => 'Перевозчик',
            'carrier-helper'         => 'Для субподрядной перевозки. Затраты перевозчика учитываются как расходы.',
            'carrier-reference'      => 'Ссылка перевозчика',
            'waybill-no'                      => 'Номер накладной',
            'line-type'                       => 'Тип строки',
            'line-product'                    => 'Товар',
            'is-confidential'                 => 'Конфиденциальная отправка',
            'is-confidential-helper'          => 'Скрывает сведения о грузе в накладной и в форме доставки водителя.',
            'confidential-description'        => 'Описание груза для накладной',
            'confidential-description-helper' => 'Этот текст выводится в накладной вместо полного списка груза.',
            'consignor'                       => 'Отправлено от имени',
            'consignor-helper'                => 'Компания, чей груз перевозится (3PL / white-label).',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'Номер',
            'customer'             => 'Клиент',
            'state'                => 'Статус',
            'planned-pickup-at'    => 'Плановый забор',
            'expected-delivery-at' => 'Ожидаемая доставка',
            'service-type'         => 'Услуга',
            'packages'             => 'Места',
            'dispatcher'           => 'Диспетчер',
            'company'              => 'Компания',
        ],

        'filters' => [
            'state'         => 'Статус',
            'customer'      => 'Клиент',
            'service-type'  => 'Тип услуги',
            'pickup-from'   => 'Забор с',
            'pickup-until'  => 'Забор по',
        ],
    ],

    'infolist' => [
        'summary'            => 'Отправка',
        'route'              => 'Маршрут',
        'cargo'              => 'Груз',
        'assignment'         => 'Назначение',
        'actual-pickup-at'   => 'Фактический забор',
        'actual-delivery-at' => 'Фактическая доставка',
        'total-weight'       => 'Общий вес',
        'total-volume'       => 'Общий объём',
    ],

    'timeline' => [
        'title'   => 'Хронология',
        'columns' => [
            'occurred-at' => 'Когда',
            'type'        => 'Событие',
            'source'      => 'Источник',
            'user'        => 'Кем',
            'location'    => 'Местоположение',
            'notes'       => 'Примечания',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'Подтвердить',
            'heading'      => 'Подтвердить эту отправку?',
            'notification' => 'Отправка подтверждена',
        ],

        'hold' => [
            'label'        => 'Приостановить',
            'heading'      => 'Приостановить эту отправку?',
            'reason'       => 'Причина',
            'notification' => 'Отправка приостановлена',
        ],

        'release' => [
            'label'        => 'Возобновить',
            'heading'      => 'Возобновить эту отправку?',
            'description'  => 'Отправка возвращается в статус «Подтверждена» и снова может быть назначена на рейс.',
            'notification' => 'Отправка возобновлена',
        ],

        'cancel' => [
            'label'        => 'Отменить отправку',
            'heading'      => 'Отменить эту отправку?',
            'description'  => 'Отменённую отправку нельзя открыть снова. Её начисления сохраняются для справки.',
            'reason'       => 'Причина',
            'notification' => 'Отправка отменена',
        ],
    ],
];
