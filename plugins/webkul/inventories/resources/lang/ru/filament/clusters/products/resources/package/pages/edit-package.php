<?php

return [
    'notification' => [
        'title' => 'Упаковка обновлено',
        'body'  => 'упаковка есть обновлено successfully.',
    ],

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
                    'title' => 'Упаковка удалено',
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
