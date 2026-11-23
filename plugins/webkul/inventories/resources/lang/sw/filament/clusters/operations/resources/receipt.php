<?php

return [
    'navigation' => [
        'title' => 'Stakabadhi',
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
                        'title' => 'Stakabadhi imefutwa',
                        'body'  => 'stakabadhi ras imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Stakabadhi could si kuwa imefutwa',
                        'body'  => 'stakabadhi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Stakabadhi imefutwa',
                        'body'  => 'stakabadhi ina imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Stakabadhi could si kuwa imefutwa',
                        'body'  => 'stakabadhi haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],
];
