<?php

return [
    'title' => 'Отказать Причина',

    'navigation' => [
        'title' => 'Отказать Reasons',
        'group' => 'Applications',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Название',
            'template'         => [
                'title'                    => 'Шаблон',
                'applicant-refuse'         => 'Кандидат Отказать',
                'applicant-not-interested' => 'Кандидат Не Interested',
            ],
            'name-placeholder' => 'Введите название отказать причина',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'Ид.',
            'name'       => 'Название',
            'template'   => 'Шаблон',
            'created-by' => 'Создано От',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'name'       => 'Название',
            'employee'   => 'Сотрудник',
            'created-by' => 'Создано От',
            'updated-at' => 'Обновлено',
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Отказать причина обновлено',
                    'body'  => 'отказать причина есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отказать причина удалено',
                    'body'  => 'отказать причина есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Отказать reasons удалено',
                    'body'  => 'отказать reasons есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Отказать причина создано',
                    'body'  => 'отказать причина есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'Название',
        'template'   => 'Шаблон',
    ],
];
