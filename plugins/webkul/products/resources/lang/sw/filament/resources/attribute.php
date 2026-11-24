<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name' => 'Jina',
                    'type' => 'Aina',
                ],
            ],

            'options' => [
                'title'  => 'Chaguo',

                'fields' => [
                    'name'        => 'Jina',
                    'color'       => 'Rangi',
                    'extra-price' => 'Bei ya ziada',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Jina',
            'type'        => 'Aina',
            'deleted-at'  => 'Ilifutwa',
            'created-at'  => 'Iliundwa',
            'updated-at'  => 'Ilisasishwa',
        ],

        'groups' => [
            'type'       => 'Aina',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'filters' => [
            'type' => 'Aina',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sifa imerejeshwa',
                    'body'  => 'Sifa imerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sifa imefutwa',
                    'body'  => 'Sifa imefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Sifa imefutwa kabisa',
                        'body'  => 'Sifa imefutwa kabisa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Sifa haikuweza kufutwa',
                        'body'  => 'Sifa haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Sifa zimerejeshwa',
                    'body'  => 'Sifa zimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sifa zimefutwa',
                    'body'  => 'Sifa zimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Sifa zimefutwa kabisa',
                        'body'  => 'Sifa zimefutwa kabisa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Sifa hazikuweza kufutwa',
                        'body'  => 'Sifa haziwezi kufutwa kwa sababu zinatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name' => 'Jina',
                    'type' => 'Aina',
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'creator'    => 'Imeundwa na',
                    'created_at' => 'Iliundwa',
                    'updated_at' => 'Ilisasishwa mwisho',
                ],
            ],
        ],
    ],
];
