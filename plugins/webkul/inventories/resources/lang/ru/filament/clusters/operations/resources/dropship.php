<?php

return [
    'navigation' => [
        'title' => 'Dropships',
        'group' => 'Перемещения',
    ],

    'global-search' => [
        'partner' => 'Контрагент',
        'origin'  => 'Происхождение',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Dropship удалено',
                        'body'  => 'dropship ras удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Dropship could не быть удалено',
                        'body'  => 'dropship нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Dropships удалено',
                        'body'  => 'dropships есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Dropships could не быть удалено',
                        'body'  => 'dropships нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],
];
