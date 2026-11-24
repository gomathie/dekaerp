<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'company'            => 'Kampuni',
                'country'            => 'Nchi',
                'name'               => 'Jina',
                'preceding-subtotal' => 'Jumla ndogo iliyotangulia',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'company'            => 'Kampuni',
            'country'            => 'Nchi',
            'created-by'         => 'Imeundwa na',
            'name'               => 'Jina',
            'preceding-subtotal' => 'Jumla ndogo iliyotangulia',
            'created-at'         => 'Iliundwa',
            'updated-at'         => 'Ilisasishwa',
        ],

        'groups' => [
            'name'       => 'Jina',
            'company'    => 'Kampuni',
            'country'    => 'Nchi',
            'created-by' => 'Imeundwa na',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kikundi cha kodi kimefutwa',
                        'body'  => 'Kikundi cha kodi kimefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Kikundi cha kodi hakikuweza kufutwa',
                        'body'  => 'Kikundi cha kodi hakiwezi kufutwa kwa sababu kinatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Vikundi vya kodi vimefutwa',
                        'body'  => 'Vikundi vya kodi vimefutwa kikamilifu.',
                    ],

                    'error' => [
                        'title' => 'Vikundi vya kodi havikuweza kufutwa',
                        'body'  => 'Vikundi vya kodi haviwezi kufutwa kwa sababu vinatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'company'            => 'Kampuni',
                'country'            => 'Nchi',
                'name'               => 'Jina',
                'preceding-subtotal' => 'Jumla ndogo iliyotangulia',
            ],
        ],
    ],
];
