<?php

return [
    'navigation' => [
        'title' => 'Рейсы',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'Транспорт и экипаж',
            'schedule' => 'Расписание',
        ],

        'fields' => [
            'company'            => 'Компания',
            'number'             => 'Номер рейса',
            'number-placeholder' => 'Присваивается автоматически',
            'vehicle'            => 'Транспортное средство',
            'driver'             => 'Водитель',
            'dispatcher'         => 'Диспетчер',
            'planned-start-at'   => 'Плановое начало',
            'planned-end-at'     => 'Плановое окончание',
            'odometer-start'     => 'Одометр на старте',
            'odometer-end'       => 'Одометр на финише',
            'notes'              => 'Примечания',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Рейс',
            'state'            => 'Статус',
            'vehicle'          => 'Транспортное средство',
            'driver'           => 'Водитель',
            'shipments'        => 'Отправки',
            'planned-start-at' => 'Плановое начало',
            'company'          => 'Компания',
        ],

        'filters' => [
            'state'   => 'Статус',
            'vehicle' => 'Транспортное средство',
            'driver'  => 'Водитель',
        ],
    ],

    'infolist' => [
        'summary'         => 'Рейс',
        'schedule'        => 'Расписание',
        'load'            => 'Загрузка',
        'actual-start-at' => 'Фактическое начало',
        'actual-end-at'   => 'Фактическое окончание',
        'capacity'        => 'Вместимость',
        'capacity-ok'     => 'В пределах вместимости транспортного средства.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'Отправки',
            'columns' => [
                'number'   => 'Отправка',
                'state'    => 'Статус',
                'customer' => 'Клиент',
                'weight'   => 'Вес (кг)',
                'volume'   => 'Объём (м³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'Добавить отправку',
                    'field'        => 'Подтверждённая отправка',
                    'notification' => 'Отправка добавлена в этот рейс.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'Отправить в рейс',
            'heading'      => 'Отправить этот рейс?',
            'notification' => 'Рейс отправлен.',
        ],
        'startTrip' => [
            'label'        => 'Начать',
            'heading'      => 'Начать этот рейс?',
            'notification' => 'Рейс начат.',
        ],
        'completeTrip' => [
            'label'        => 'Завершить',
            'heading'      => 'Завершить этот рейс?',
            'notification' => 'Рейс завершён.',
        ],
    ],
];
