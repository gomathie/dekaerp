<?php

return [
    'notification' => [
        'title' => 'Доставка обновлено',
        'body'  => 'доставка есть обновлено successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Доставка удалено',
                    'body'  => 'доставка есть удалено successfully.',
                ],

                'error' => [
                    'title' => 'Доставка could не быть удалено',
                    'body'  => 'доставка нельзя быть удалено потому что это currently в использовать.',
                ],
            ],
        ],
    ],
];
