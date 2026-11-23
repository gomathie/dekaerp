<?php

return [
    'title'      => 'Likizo Aina',
    'navigation' => [
        'title' => 'Likizo Aina',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla Taarifa',
                'fields' => [
                    'name'                => 'Kichwa',
                    'approval'            => 'Approval',
                    'requires-allocation' => 'Requires Allocation',
                    'employee-requests'   => 'Mfanyakazi Requests',
                    'display-option'      => 'Display Option',
                ],
            ],
            'display-option' => [
                'title'  => 'Display Option',
                'fields' => [
                    'color' => 'Rangi',
                ],
            ],
            'configuration' => [
                'title' => 'Usanidi',

                'fields' => [
                    'notified-time-off-officers'          => 'Notified Likizo Officers',
                    'take-time-off-in'                    => 'Take Likizo Katika',
                    'public-holiday-included'             => 'Hadharani Holiday Imejumuishwa',
                    'allow-to-attach-supporting-document' => 'Ruhusu hadi Attach Supporting Hati',
                    'show-on-dashboard'                   => 'Onyesha washa Dashibodi',
                    'allow-negative-cap'                  => 'Ruhusu Negative Cap',
                    'kind-off-time'                       => 'Kind ya Time',
                    'max-negative-cap'                    => 'Max Negative Cap',
                    'kind-of-time'                        => 'Kind ya Likizo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                      => 'Jina',
            'company-name'              => 'Kampuni',
            'color'                     => 'Rangi',
            'notified-time-officers'    => 'Notified Likizoicers',
            'time-off-approval'         => 'Likizo Approval',
            'requires-allocation'       => 'Requires Allocation',
            'allocation-approval'       => 'Allocation Approval',
            'employee-request'          => 'Mfanyakazi Request',
        ],

        'filters' => [
            'name'                => 'Jina',
            'company-name'        => 'Kampuni',
            'time-off-approval'   => 'Likizo Approval',
            'requires-allocation' => 'Requires Allocation',
            'time-type'           => 'Time Aina',
            'request-unit'        => 'Request Uniti',
            'created-by'          => 'Imeundwa Na',
            'employee-request'    => 'Mfanyakazi Request',
            'created-at'          => 'Imeundwa',
            'updated-at'          => 'Imesasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Likizo Aina imefutwa',
                    'body'  => 'Likizo aina ina imefutwa successfully.',
                ],
            ],
            'restore' => [
                'notification' => [
                    'title' => 'Likizo Aina restored',
                    'body'  => 'Likizo aina ina restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Likizo Aina restored',
                    'body'  => 'Likizo Aina ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Likizo Aina imefutwa',
                    'body'  => 'Likizo Aina ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Likizo Aina force imefutwa',
                        'body'  => 'Likizo Aina ina force imefutwa successfully.',
                    ],
                    'error' => [
                        'title' => 'Likizo Aina could si kuwa imefutwa',
                        'body'  => 'Likizo Aina haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla Taarifa',
                'entries' => [
                    'name'                => 'Kichwa',
                    'approval'            => 'Approval',
                    'requires-allocation' => 'Requires Allocation',
                    'employee-requests'   => 'Mfanyakazi Requests',
                    'display-option'      => 'Display Option',
                ],
            ],
            'display-option' => [
                'title'   => 'Display Option',
                'entries' => [
                    'color' => 'Rangi',
                ],
            ],
            'configuration' => [
                'title' => 'Usanidi',

                'entries' => [
                    'notified-time-off-officers'          => 'Notified Likizo Officers',
                    'take-time-off-in'                    => 'Take Likizo Katika',
                    'public-holiday-included'             => 'Hadharani Holiday Imejumuishwa',
                    'allow-to-attach-supporting-document' => 'Ruhusu hadi Attach Supporting Hati',
                    'show-on-dashboard'                   => 'Onyesha washa Dashibodi',
                    'kind-off-time'                       => 'Kind ya Time',
                    'max-negative-cap'                    => 'Max Negative Cap',
                    'kind-of-time'                        => 'Kind ya Likizo',
                ],
            ],
        ],
    ],
];
