<?php

return [
    'notification' => [
        'title' => 'Scrap обновлено',
        'body'  => 'scrap есть обновлено successfully.',
    ],

    'header-actions' => [
        'validate' => [
            'label' => 'Validate',

            'notification' => [
                'warning' => [
                    'title' => 'Insufficient запас',
                    'body'  => 'scrap есть insufficient запас до validate.',
                ],

                'success' => [
                    'title' => 'Scrap marked как завершён',
                    'body'  => 'scrap есть marked как завершён successfully.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Scrap удалено',
                    'body'  => 'scrap есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Scraps could не быть удалено',
                    'body'  => 'scraps нельзя быть удалено потому что они currently в использовать.',
                ],
            ],
        ],
    ],
];
