<?php

return [
    'navigation' => [
        'title' => 'Dropships',
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
                        'title' => 'Dropship imefutwa',
                        'body'  => 'dropship ras imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Dropship could si kuwa imefutwa',
                        'body'  => 'dropship haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Dropships imefutwa',
                        'body'  => 'dropships ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Dropships could si kuwa imefutwa',
                        'body'  => 'dropships haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],
];
