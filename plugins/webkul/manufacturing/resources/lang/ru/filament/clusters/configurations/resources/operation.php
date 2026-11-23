<?php

return [
    'navigation' => [
        'title' => 'Операции',
        'group' => 'Конфигурация',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'name'              => 'Operation',
                    'name-placeholder'  => 'eg. Cutting',
                    'bill-of-material'  => 'Счёт поставщика Material',
                    'work-center'       => 'Work Center',
                    'apply-on-variants' => 'Применить вкл. Variants',
                    'company'           => 'Компания',
                    'blocked-by'        => 'Blocked От',
                ],
            ],
            'settings' => [
                'title'  => 'Настройки',
                'fields' => [
                    'time-mode'                  => 'Duration Computation',
                    'time-mode-batch'            => 'Based Вкл.',
                    'time-mode-batch-prefix'     => 'последний',
                    'time-mode-batch-suffix'     => 'производственный заказs',
                    'manual-cycle-time'          => 'По умолчанию Duration',
                    'manual-cycle-time-suffix'   => 'минуты',
                ],
            ],
            'worksheet' => [
                'title'  => 'Work Sheet',
                'fields' => [
                    'worksheet'                => 'Worksheet',
                    'pdf'                      => 'PDF',
                    'google-slide'             => 'Google Slide',
                    'google-slide-placeholder' => 'Google Slide Link',
                    'description'              => 'Описание',
                    'description-placeholder'  => 'Описание operation...',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'              => 'Operation',
            'bill-of-material'  => 'Счёт поставщика Material',
            'work-center'       => 'Work Center',
            'time-mode'         => 'Duration Computation',
            'manual-cycle-time' => 'По умолчанию Duration',
            'worksheet-type'    => 'Worksheet',
            'deleted-at'        => 'Удалено',
            'created-at'        => 'Создано',
            'updated-at'        => 'Обновлено',
        ],
        'filters' => [
            'work-center'    => 'Work Center',
            'time-mode'      => 'Duration Computation',
            'worksheet-type' => 'Worksheet',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operation restored',
                    'body'  => 'operation есть restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Operation в архиве',
                    'body'  => 'operation есть в архиве successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operation удалено',
                        'body'  => 'operation есть permanently удалено.',
                    ],
                    'error' => [
                        'title' => 'Operation could не быть удалено',
                        'body'  => 'operation нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Операции restored',
                    'body'  => 'выбрано операции есть restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Операции в архиве',
                    'body'  => 'выбрано операции есть в архиве successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Операции удалено',
                        'body'  => 'выбрано операции есть permanently удалено.',
                    ],
                    'error' => [
                        'title' => 'Операции could не быть удалено',
                        'body'  => 'One или больше выбрано операции currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'name'              => 'Operation',
                    'bill-of-material'  => 'Счёт поставщика Material',
                    'work-center'       => 'Work Center',
                    'apply-on-variants' => 'Применить вкл. Variants',
                    'company'           => 'Компания',
                ],
            ],
            'settings' => [
                'title'   => 'Настройки',
                'entries' => [
                    'time-mode'                => 'Duration Computation',
                    'time-mode-batch'          => 'Based Вкл.',
                    'manual-cycle-time'        => 'По умолчанию Duration',
                    'manual-cycle-time-suffix' => 'минуты',
                ],
            ],
            'worksheet' => [
                'title'   => 'Work Sheet',
                'entries' => [
                    'worksheet'    => 'Worksheet',
                    'pdf'          => 'PDF',
                    'google-slide' => 'Google Slide',
                    'description'  => 'Описание',
                ],
            ],
            'record-information' => [
                'title'   => 'Запись Сведения',
                'entries' => [
                    'created-by'   => 'Создано От',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],
        ],
    ],
];
