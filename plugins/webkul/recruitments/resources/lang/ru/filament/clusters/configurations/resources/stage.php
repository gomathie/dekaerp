<?php

return [
    'title' => 'Этапы',

    'navigation' => [
        'title' => 'Этапы',
        'group' => 'Должность Positions',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Общее Сведения',

                'fields' => [
                    'stage-name'   => 'Этап Название',
                    'sort'         => 'Нумерация Заказ',
                    'requirements' => 'Requirements',
                ],
            ],

            'tooltips' => [
                'title'       => 'Tooltips',
                'description' => 'Define свой подпись для application статус.',

                'fields' => [
                    'gray-label'          => 'Gray Подпись',
                    'gray-label-tooltip'  => 'подпись для gray статус.',
                    'red-label'           => 'Red Подпись',
                    'red-label-tooltip'   => 'подпись для red статус.',
                    'green-label'         => 'Green Подпись',
                    'green-label-tooltip' => 'подпись для green статус.',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Сведения',

                'fields' => [
                    'job-positions' => 'Должность Positions',
                    'folded'        => 'Folded',
                    'hired-stage'   => 'Hired Этап',
                    'default-stage' => 'По умолчанию Этап',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'Ид.',
            'name'               => 'Этап Название',
            'hired-stage'        => 'Hired Этап',
            'default-stage'      => 'По умолчанию Этап',
            'folded'             => 'Folded',
            'job-positions'      => 'Должность Positions',
            'created-by'         => 'Создано От',
            'created-at'         => 'Создано',
            'updated-at'         => 'Обновлено',
        ],

        'filters' => [
            'name'         => 'Этап Название',
            'job-position' => 'Должность Должность',
            'folded'       => 'Folded',
            'gray-label'   => 'Gray Подпись',
            'red-label'    => 'Red Подпись',
            'green-label'  => 'Green Подпись',
            'created-by'   => 'Создано От',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'groups' => [
            'job-position' => 'Должность Должность',
            'stage-name'   => 'Этап Название',
            'folded'       => 'Folded',
            'gray-label'   => 'Gray Подпись',
            'red-label'    => 'Red Подпись',
            'green-label'  => 'Green Подпись',
            'created-by'   => 'Создано От',
            'created-at'   => 'Создано',
            'updated-at'   => 'Обновлено',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Этапы удалено',
                        'body'  => 'Этапы есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Этапы could не быть удалено',
                        'body'  => 'Этапы нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Этапы удалено',
                    'body'  => 'Этапы есть удалено successfully.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Новое Этап',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'stage-name'   => 'Этап Название',
                    'sort'         => 'Нумерация Заказ',
                    'requirements' => 'Requirements',
                ],
            ],

            'tooltips' => [
                'title'       => 'Tooltips',
                'description' => 'Define свой подпись для application статус.',

                'entries' => [
                    'gray-label'          => 'Gray Подпись',
                    'gray-label-tooltip'  => 'подпись для gray статус.',
                    'red-label'           => 'Red Подпись',
                    'red-label-tooltip'   => 'подпись для red статус.',
                    'green-label'         => 'Green Подпись',
                    'green-label-tooltip' => 'подпись для green статус.',
                ],
            ],

            'additional-information' => [
                'title' => 'Additional Сведения',

                'entries' => [
                    'job-positions'      => 'Должность Должность',
                    'folded'             => 'Folded',
                    'hired-stage'        => 'Hired Этап',
                    'default-stage'      => 'По умолчанию Этап',
                ],
            ],
        ],
    ],

];
