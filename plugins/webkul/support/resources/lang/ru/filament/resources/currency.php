<?php

return [
    'title' => 'Валюты',

    'navigation' => [
        'title' => 'Валюты',
    ],

    'form' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Валюта Сведения',

                'fields' => [
                    'name'         => 'Валюта Название',
                    'name-tooltip' => 'Введите official валюта название',
                    'symbol'       => 'Валюта Symbol',
                    'full-name'    => 'Полный Название',
                    'iso-numeric'  => 'ISO Numeric Код',
                ],
            ],

            'format-information' => [
                'title' => 'Формат Конфигурация',

                'fields' => [
                    'decimal-places'        => 'Decimal Places',
                    'rounding'              => 'Rounding Precision',
                    'rounding-helper-text'  => 'Задать rounding precision для валюта calculations',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Статус & Конфигурация',

                'fields' => [
                    'status' => 'Статус',
                ],
            ],

            'rates' => [
                'title'       => 'Валюта Ставки',
                'description' => 'Manage historic обменный курсs для этот валюта relative до base валюта (USD).',

                'fields' => [
                    'name'              => 'Дата',
                    'unit-per-currency' => 'Единица За :currency',
                    'currency-per-unit' => ':currency За Единица',
                ],

                'add-rate'   => 'Добавить Ставка',
                'item-label' => 'Ставка',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'           => 'Валюта Название',
            'symbol'         => 'Symbol',
            'full-name'      => 'Полный Название',
            'iso-numeric'    => 'ISO Код',
            'decimal-places' => 'Decimal Places',
            'rounding'       => 'Rounding',
            'status'         => 'Статус',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',
        ],

        'groups' => [
            'name'           => 'Название',
            'status'         => 'Статус',
            'decimal-places' => 'Decimal Places',
            'creation-date'  => 'Creation Дата',
            'last-update'    => 'Последний Обновить',
        ],

        'filters' => [
            'status' => 'Статус',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title'   => 'Валюта удалено',
                    'body'    => 'валюта есть удалено successfully.',

                    'success' => [
                        'title' => 'Валюта удалено',
                        'body'  => 'валюта есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Валюта could не быть удалено',
                        'body'  => 'валюта нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],

            'deactivate' => [
                'notification' => [
                    'title' => 'Валюта нельзя быть deactivated',
                    'body'  => 'Этот валюта в использовать от one или больше компании и нельзя быть deactivated.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Валюты удалено',
                    'body'  => 'валюты есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'currency-details' => [
                'title' => 'Валюта Сведения',

                'entries' => [
                    'name'         => 'Валюта Название',
                    'symbol'       => 'Валюта Symbol',
                    'full-name'    => 'Полный Название',
                    'iso-numeric'  => 'ISO Numeric Код',
                ],
            ],

            'format-information' => [
                'title' => 'Формат Конфигурация',

                'entries' => [
                    'decimal-places' => 'Decimal Places',
                    'rounding'       => 'Rounding Precision',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Статус & Конфигурация',

                'entries' => [
                    'status' => 'Статус',
                ],
            ],

            'rates' => [
                'title'       => 'Валюта Ставки',

                'entries' => [
                    'name'              => 'Дата',
                    'unit-per-currency' => 'Единица За :currency',
                    'currency-per-unit' => ':currency За Единица',
                ],
            ],
        ],
    ],
];
