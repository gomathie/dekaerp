<?php

return [
    'navigation' => [
        'title' => 'Teams',
    ],

    'form' => [
        'name'    => 'Jina',
        'company' => 'Kampuni',
        'users'   => 'Timu Wanachama',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'company'    => 'Kampuni',
            'users'      => 'Timu Wanachama',
            'created-at' => 'Imeundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Timu imesasishwa',
                    'body'  => 'timu ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Timu restored',
                    'body'  => 'timu ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timu imefutwa',
                    'body'  => 'timu ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Timu force imefutwa',
                        'body'  => 'timu ina force imefutwa successfully.',
                    ],
                    'error' => [
                        'title' => 'Timu could si kuwa force imefutwa',
                        'body'  => 'timu ni inatumika na haiwezekani kuwa force imefutwa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Teams restored',
                    'body'  => 'teams ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Teams imefutwa',
                    'body'  => 'teams ina imefutwa successfully.',
                ],
            ],
        ],
    ],
];
