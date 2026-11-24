<?php

return [
    'title' => 'Мои распределения',

    'model-label' => 'Мои распределения',

    'navigation' => [
        'title' => 'Мои распределения',
    ],

    'form' => [
        'fields' => [
            'name'                => 'Название',
            'name-placeholder'    => 'Тип отпуска (от начала до конца срока действия / без ограничений)',
            'time-off-type'       => 'Тип отпуска',
            'allocation-type'     => 'Тип распределения',
            'validity-period'     => 'Срок действия',
            'date-from'           => 'Дата с',
            'date-to'             => 'Дата по',
            'date-to-placeholder' => 'Без ограничений',
            'allocation'          => 'Распределение',
            'allocation-suffix'   => 'Количество дней',
            'reason'              => 'Причина',
        ],
    ],

    'table' => [
        'columns' => [
            'time-off-type'   => 'Тип отпуска',
            'amount'          => 'Количество',
            'allocation-type' => 'Тип распределения',
            'status'          => 'Статус',
        ],

        'groups' => [
            'time-off-type'   => 'Тип отпуска',
            'employee-name'   => 'Имя сотрудника',
            'allocation-type' => 'Тип распределения',
            'status'          => 'Статус',
            'start-date'      => 'Дата начала',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Подтвердить',
                    'approve'  => 'Утвердить',
                ],
                'notification' => [
                    'title' => 'Распределение утверждено',
                    'body'  => 'Распределение успешно утверждено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Распределение удалено',
                    'body'  => 'Распределение успешно удалено.',
                ],
            ],

            'refused' => [
                'title'        => 'Отклонить',
                'notification' => [
                    'title' => 'Распределение отклонено',
                    'body'  => 'Распределение успешно отклонено.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Распределения удалены',
                    'body'  => 'Распределения успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'Сведения о распределении',
                'entries' => [
                    'name'                => 'Название',
                    'time-off-type'       => 'Тип отпуска',
                    'allocation-type'     => 'Тип распределения',
                ],
            ],

            'validity-period' => [
                'title'   => 'Срок действия',
                'entries' => [
                    'date-from' => 'Дата с',
                    'date-to'   => 'Дата по',
                    'reason'    => 'Причина',
                ],
            ],
            'allocation-status' => [
                'title'   => 'Статус распределения',
                'entries' => [
                    'date-to-placeholder' => 'Без ограничений',
                    'allocation'          => 'Количество дней',
                    'allocation-value'    => 'Дней: :days',
                    'state'               => 'Статус',
                ],
            ],
        ],
    ],
];
