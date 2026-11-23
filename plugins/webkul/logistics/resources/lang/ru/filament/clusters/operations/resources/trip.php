<?php

return [
    'navigation' => [
        'title' => 'Рейсы',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'Транспорт и crew',
            'schedule' => 'График',
        ],

        'fields' => [
            'company'            => 'Компания',
            'number'             => 'Рейс номер',
            'number-placeholder' => 'Назначен автоматически',
            'vehicle'            => 'Транспорт',
            'driver'             => 'Водитель',
            'dispatcher'         => 'Dispatcher',
            'planned-start-at'   => 'Запланировано начать',
            'planned-end-at'     => 'Запланировано конец',
            'odometer-start'     => 'Odometer начать',
            'odometer-end'       => 'Odometer конец',
            'notes'              => 'Заметки',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Рейс',
            'state'            => 'Статус',
            'vehicle'          => 'Транспорт',
            'driver'           => 'Водитель',
            'shipments'        => 'Отправки',
            'planned-start-at' => 'Запланировано начать',
            'company'          => 'Компания',
        ],

        'filters' => [
            'state'   => 'Статус',
            'vehicle' => 'Транспорт',
            'driver'  => 'Водитель',
        ],
    ],

    'infolist' => [
        'summary'         => 'Рейс',
        'schedule'        => 'График',
        'load'            => 'Load',
        'actual-start-at' => 'Факт начать',
        'actual-end-at'   => 'Факт конец',
        'capacity'        => 'Capacity',
        'capacity-ok'     => 'В пределах транспорт’s capacity.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'Отправки',
            'columns' => [
                'number'   => 'Отправка',
                'state'    => 'Статус',
                'customer' => 'Клиент',
                'weight'   => 'Вес (kg)',
                'volume'   => 'Объём (m³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'Добавить отправка',
                    'field'        => 'Подтверждён отправка',
                    'notification' => 'Отправка added до этот рейс.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'Dispatch',
            'heading'      => 'Dispatch этот рейс?',
            'notification' => 'Рейс dispatched.',
        ],
        'startTrip' => [
            'label'        => 'Начать',
            'heading'      => 'Начать этот рейс?',
            'notification' => 'Рейс started.',
        ],
        'completeTrip' => [
            'label'        => 'Завершить',
            'heading'      => 'Завершить этот рейс?',
            'notification' => 'Рейс завершено.',
        ],
    ],
];
