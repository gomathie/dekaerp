<?php

return [
    'navigation' => [
        'title' => 'Deliveries',
        'group' => 'Uhamisho',
    ],

    'global-search' => [
        'partner' => 'Mshirika',
        'origin'  => 'Asili',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Usafirishaji imefutwa',
                        'body'  => 'usafirishaji ras imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Usafirishaji could si kuwa imefutwa',
                        'body'  => 'usafirishaji haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Deliveries imefutwa',
                        'body'  => 'deliveries ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Deliveries could si kuwa imefutwa',
                        'body'  => 'deliveries haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],
];
