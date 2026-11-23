<?php

return [
    'navigation' => [
        'title' => 'Ndani Uhamisho',
        'group' => 'Uhamisho',
    ],

    'global-search' => [
        'origin' => 'Asili',
    ],

    'table' => [
        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ndani Uhamisho imefutwa',
                        'body'  => 'ndani uhamisho ras imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Ndani Uhamisho could si kuwa imefutwa',
                        'body'  => 'ndani uhamisho haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ndani Uhamisho imefutwa',
                        'body'  => 'ndani uhamisho ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Ndani Uhamisho could si kuwa imefutwa',
                        'body'  => 'ndani uhamisho haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],
];
