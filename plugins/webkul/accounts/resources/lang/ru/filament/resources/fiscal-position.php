<?php

return [
    'global-search' => [
        'zip-from' => 'Индекс с',
        'zip-to'   => 'Индекс по',
        'name'     => 'Название',
    ],

    'form' => [
        'fields' => [
            'name'                   => 'Название',
            'foreign-vat'            => 'Иностранный VAT',
            'country'                => 'Страна',
            'country-group'          => 'Группа стран',
            'zip-from'               => 'Индекс с',
            'zip-to'                 => 'Индекс по',
            'detect-automatically'   => 'Определять автоматически',
            'notes'                  => 'Примечания',
            'company'                => 'Компания',
        ],
        'tabs' => [
            'account-mapping' => [
                'table' => [
                    'columns' => [
                        'source-account'      => 'Исходный счёт',
                        'destination-account' => 'Целевой счёт',
                    ],
                ],

            ],
            'tax-mapping' => [
                'table' => [
                    'columns' => [
                        'tax-source'      => 'Исходный налог',
                        'tax-destination' => 'Целевой налог',
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
            'country-group'        => 'Группа стран',
            'created-by'           => 'Создал',
            'zip-from'             => 'Индекс с',
            'zip-to'               => 'Индекс по',
            'status'               => 'Статус',
            'detect-automatically' => 'Определять автоматически',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Условие оплаты удалено',
                    'body'  => 'Условие оплаты успешно удалено.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Налоговая позиция удалена',
                    'body'  => 'Налоговая позиция успешно удалена.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'Название',
            'foreign-vat'          => 'Иностранный VAT',
            'country'              => 'Страна',
            'country-group'        => 'Группа стран',
            'zip-from'             => 'Индекс с',
            'zip-to'               => 'Индекс по',
            'detect-automatically' => 'Определять автоматически',
            'notes'                => 'Примечания',
        ],
    ],
];
