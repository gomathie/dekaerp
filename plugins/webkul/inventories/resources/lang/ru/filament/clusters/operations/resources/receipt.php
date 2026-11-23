<?php

return [
    'navigation' => [
        'title' => 'Приходы',
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
                        'title' => 'Приход удалено',
                        'body'  => 'приход ras удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Приход could не быть удалено',
                        'body'  => 'приход нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Приходы удалено',
                        'body'  => 'приходы есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Приходы could не быть удалено',
                        'body'  => 'приходы нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],
];
