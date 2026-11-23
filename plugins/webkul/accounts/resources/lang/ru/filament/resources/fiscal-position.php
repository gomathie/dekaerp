<?php

return [
    'global-search' => [
        'zip-from' => 'Zip От',
        'zip-to'   => 'Zip До',
        'name'     => 'Название',
    ],

    'form' => [
        'fields' => [
            'name'                   => 'Название',
            'foreign-vat'            => 'Foreign НДС',
            'country'                => 'Страна',
            'country-group'          => 'Страна Группа',
            'zip-from'               => 'Zip От',
            'zip-to'                 => 'Zip До',
            'detect-automatically'   => 'Detect Автоматически',
            'notes'                  => 'Заметки',
            'company'                => 'Компания',
        ],
        'tabs' => [
            'account-mapping' => [
                'table' => [
                    'columns' => [
                        'source-account'      => 'Источник Счёт',
                        'destination-account' => 'Назначение Счёт',
                    ],
                ],

            ],
            'tax-mapping' => [
                'table' => [
                    'columns' => [
                        'tax-source'      => 'Налог Источник',
                        'tax-destination' => 'Налог Назначение',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Название',
            'company'              => 'Компания',
            'country'              => 'Страна',
            'country-group'        => 'Страна Группа',
            'created-by'           => 'Создано От',
            'zip-from'             => 'Zip От',
            'zip-to'               => 'Zip До',
            'status'               => 'Статус',
            'detect-automatically' => 'Detect Автоматически',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Условие оплаты удалено',
                    'body'  => 'Условие оплаты есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Финансовый Должность удалено',
                    'body'  => 'финансовый Должность есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'Название',
            'foreign-vat'          => 'Foreign НДС',
            'country'              => 'Страна',
            'country-group'        => 'Страна Группа',
            'zip-from'             => 'Zip От',
            'zip-to'               => 'Zip До',
            'detect-automatically' => 'Detect Автоматически',
            'notes'                => 'Заметки',
        ],
    ],
];
