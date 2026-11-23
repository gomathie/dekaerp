<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Налог Источник',
            'tax-destination' => 'Налог Назначение',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Налог Источник',
            'tax-destination' => 'Налог Назначение',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Оплата Due Term обновлено',
                    'body'  => 'оплата due term есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Оплата Due Term удалено',
                    'body'  => 'оплата due term есть удалено successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Оплата Due Term создано',
                    'body'  => 'оплата due term есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Налог Источник',
            'tax-destination' => 'Налог Назначение',
        ],
    ],
];
