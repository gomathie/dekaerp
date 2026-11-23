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
            'route'         => 'Маршрут & остановки',
            'cargo'         => 'Cargo',
            'assignment'    => 'Assignment',
            'confidential'  => 'Confidential',
        ],

        'fields' => [
            'number'                 => 'Номер отправки',
            'number-placeholder'     => 'Назначен когда отправка saved',
            'company'                => 'Компания',
            'customer'               => 'Клиент',
            'customer-reference'     => 'Ссылка клиента',
            'sale-order'             => 'От заказ на продажу',
            'sale-order-helper'      => 'Copies валюта и Ссылка клиента от заказ на продажу этот клиент.',
            'service-type'           => 'Service тип',
            'transport-mode'         => 'Transport mode',
            'priority'               => 'Приоритет',
            'currency'               => 'Валюта',
            'origin'                 => 'Происхождение',
            'destination'            => 'Назначение',
            'pickup-address'         => 'Pickup адрес',
            'delivery-address'       => 'Доставка адрес',
            'planned-pickup-at'      => 'Запланировано pickup',
            'expected-delivery-at'   => 'Ожидается доставка',
            'stops'                  => 'Остановки',
            'stop-type'              => 'Тип',
            'stop-address'           => 'Адрес',
            'stop-contact'           => 'Контакт',
            'stop-phone'             => 'Телефон',
            'stop-planned-arrival'   => 'Запланировано arrival',
            'stop-instructions'      => 'Instructions',
            'declared-value'         => 'Declared значение',
            'is-fragile'             => 'Fragile',
            'is-hazardous'           => 'Hazardous',
            'is-hazardous-helper'    => 'Flag только. Dangerous-goods paperwork не handled здесь.',
            'cargo-lines'            => 'Cargo',
            'line-description'       => 'Описание',
            'line-package-type'      => 'Упаковка тип',
            'line-quantity'          => 'Количество',
            'line-weight'            => 'Вес',
            'line-volume'            => 'Объём',
            'instructions'           => 'Special instructions',
            'dispatcher'             => 'Dispatcher',
            'carrier'                => 'Carrier',
            'carrier-helper'         => 'Для subcontracted transport. Carrier затраты recorded как расходы.',
            'carrier-reference'      => 'Carrier ссылка',
            'waybill-no'                      => 'Номер накладной',
            'line-type'                       => 'Строка тип',
            'line-product'                    => 'Товар',
            'is-confidential'                 => 'Confidential отправка',
            'is-confidential-helper'          => 'Conceals cargo подробности вкл. накладная и driver\'s доставка форма.',
            'confidential-description'        => 'Cargo описание для накладная',
            'confidential-description-helper' => 'Этот text appears вкл. накладная в place полный cargo список.',
            'consignor'                       => 'Отправлено от имени',
            'consignor-helper'                => 'компания whose goods transported (3PL / white-подпись).',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'Номер',
            'customer'             => 'Клиент',
            'state'                => 'Статус',
            'planned-pickup-at'    => 'Запланировано pickup',
            'expected-delivery-at' => 'Ожидается доставка',
            'service-type'         => 'Service',
            'packages'             => 'Упаковки',
            'dispatcher'           => 'Dispatcher',
            'company'              => 'Компания',
        ],

        'filters' => [
            'state'         => 'Статус',
            'customer'      => 'Клиент',
            'service-type'  => 'Service тип',
            'pickup-from'   => 'Pickup от',
            'pickup-until'  => 'Pickup до',
        ],
    ],

    'infolist' => [
        'summary'            => 'Отправка',
        'route'              => 'Маршрут',
        'cargo'              => 'Cargo',
        'assignment'         => 'Assignment',
        'actual-pickup-at'   => 'Факт pickup',
        'actual-delivery-at' => 'Факт доставка',
        'total-weight'       => 'Итого вес',
        'total-volume'       => 'Итого объём',
    ],

    'timeline' => [
        'title'   => 'Timeline',
        'columns' => [
            'occurred-at' => 'Когда',
            'type'        => 'Event',
            'source'      => 'Источник',
            'user'        => 'От',
            'location'    => 'Место',
            'notes'       => 'Заметки',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'Подтвердить',
            'heading'      => 'Подтвердить этот отправка?',
            'notification' => 'Отправка подтверждён',
        ],

        'hold' => [
            'label'        => 'Put вкл. hold',
            'heading'      => 'Put этот отправка вкл. hold?',
            'reason'       => 'Причина',
            'notification' => 'Отправка put вкл. hold',
        ],

        'release' => [
            'label'        => 'Выпуск',
            'heading'      => 'Выпуск этот отправка?',
            'description'  => 'отправка goes назад до Подтверждён и можно быть назначен до рейс снова.',
            'notification' => 'Отправка released',
        ],

        'cancel' => [
            'label'        => 'Отмена отправка',
            'heading'      => 'Отмена этот отправка?',
            'description'  => 'отменён отправка нельзя быть reopened. Его charges stay для ссылка.',
            'reason'       => 'Причина',
            'notification' => 'Отправка отменён',
        ],
    ],
];
