<?php

return [
    'heading' => [
        'title' => 'Обзор отпусков',
    ],

    'modal-actions' => [
        'edit' => [
            'title'        => 'Изменить',
            'notification' => [
                'title' => 'Отпуск обновлён',
                'body'  => 'Заявка на отпуск успешно обновлена.',
            ],
        ],

        'delete' => [
            'title' => 'Удалить',
        ],
    ],

    'view-action' => [
        'title'       => 'Просмотр',
        'description' => 'Просмотр заявки на отпуск',
    ],

    'header-actions' => [
        'create' => [
            'title'       => 'Новый отпуск',
            'description' => 'Создать заявку на отпуск',

            'notification' => [
                'title' => 'Отпуск создан',
                'body'  => 'Заявка на отпуск успешно создана.',
            ],

            'employee-not-found' => [
                'notification' => [
                    'title' => 'Сотрудник не найден',
                    'body'  => 'Перед созданием заявки на отпуск добавьте сотрудника в свой профиль.',
                ],
            ],
        ],
    ],

    'form' => [
        'fields' => [
            'time-off-type'     => 'Тип отпуска',
            'request-date-from' => 'Запрошено с',
            'request-date-to'   => 'Запрошено по',
            'period'            => 'Период',
            'half-day'          => 'Полдня',
            'requested-days'    => 'Запрошено (дни/часы)',
            'description'       => 'Описание',
        ],
    ],

    'infolist' => [
        'entries' => [
            'time-off-type'           => 'Тип отпуска',
            'request-date-from'       => 'Запрошено с',
            'request-date-to'         => 'Запрошено по',
            'description'             => 'Описание',
            'description-placeholder' => 'Описание не указано',
            'duration'                => 'Продолжительность',
            'status'                  => 'Статус',
        ],
    ],
];
