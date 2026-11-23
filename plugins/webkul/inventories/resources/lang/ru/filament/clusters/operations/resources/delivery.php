<?php

return [
    'navigation' => [
        'title' => 'Deliveries',
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
                        'title' => 'Доставка удалено',
                        'body'  => 'доставка ras удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Доставка could не быть удалено',
                        'body'  => 'доставка нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Deliveries удалено',
                        'body'  => 'deliveries есть удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Deliveries could не быть удалено',
                        'body'  => 'deliveries нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],
];
