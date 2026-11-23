<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Печать',

            'actions' => [
                'without-content' => [
                    'label' => 'Печать Штрихкод',
                ],

                'with-content' => [
                    'label' => 'Печать Штрихкод С Content',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Упаковка Удалено',
                    'body'  => 'упаковка есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Упаковка could не быть удалено',
                    'body'  => 'упаковка нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
