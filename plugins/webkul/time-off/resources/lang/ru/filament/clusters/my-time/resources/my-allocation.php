<?php

return [
    'title' => 'Мой Allocation',

    'model-label' => 'Мой Allocation',

    'navigation' => [
        'title' => 'Мой Allocation',
    ],

    'form' => [
        'fields' => [
            'name'                => 'Название',
            'name-placeholder'    => 'Отгул и отпуск Тип (От validity начать до validity конец/нет limit)',
            'time-off-type'       => 'Отгул и отпуск Тип',
            'allocation-type'     => 'Allocation Тип',
            'validity-period'     => 'Validity Период',
            'date-from'           => 'Дата От',
            'date-to'             => 'Дата До',
            'date-to-placeholder' => 'Нет Limit',
            'allocation'          => 'Allocation',
            'allocation-suffix'   => 'Номер Дни',
            'reason'              => 'Причина',
        ],
    ],

    'table' => [
        'columns' => [
            'time-off-type'   => 'Отгул и отпуск Тип',
            'amount'          => 'Сумма',
            'allocation-type' => 'Allocation Тип',
            'status'          => 'Статус',
        ],

        'groups' => [
            'time-off-type'   => 'Отгул и отпуск Тип',
            'employee-name'   => 'Сотрудник Название',
            'allocation-type' => 'Allocation Тип',
            'status'          => 'Статус',
            'start-date'      => 'Начать Дата',
        ],

        'actions' => [
            'approve' => [
                'title' => [
                    'validate' => 'Validate',
                    'approve'  => 'Утвердить',
                ],
                'notification' => [
                    'title' => 'Allocation утверждён утверждён',
                    'body'  => 'allocation утверждён есть утверждён successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Allocation удалено',
                    'body'  => 'allocation есть удалено successfully.',
                ],
            ],

            'refused' => [
                'title'        => 'Отказать',
                'notification' => [
                    'title' => 'Allocation отказано',
                    'body'  => 'allocation есть отказано successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Allocations удалено',
                    'body'  => 'allocations есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'allocation-details' => [
                'title'   => 'Allocation Подробности',
                'entries' => [
                    'name'                => 'Название',
                    'time-off-type'       => 'Отгул и отпуск Тип',
                    'allocation-type'     => 'Allocation Тип',
                ],
            ],

            'validity-period' => [
                'title'   => 'Validity Период',
                'entries' => [
                    'date-from' => 'Дата От',
                    'date-to'   => 'Дата До',
                    'reason'    => 'Причина',
                ],
            ],
            'allocation-status' => [
                'title'   => 'Allocation Статус',
                'entries' => [
                    'date-to-placeholder' => 'Нет Limit',
                    'allocation'          => 'Номер День(s)',
                    'allocation-value'    => ':days номер дни',
                    'state'               => 'Состояние',
                ],
            ],
        ],
    ],
];
