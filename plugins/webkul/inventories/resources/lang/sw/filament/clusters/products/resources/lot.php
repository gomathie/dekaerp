<?php

return [
    'navigation' => [
        'title' => 'Makundi / Nambari za mfululizo',
        'group' => 'Bidhaa ghalani',
    ],

    'global-search' => [
        'ref'     => 'Marejeleo',
        'product' => 'Bidhaa',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name'                   => 'Jina',
                    'name-placeholder'       => 'mf. LOT/0001/20121',
                    'product'                => 'Bidhaa',
                    'product-hint-tooltip'   => 'Bidhaa inayohusiana na kundi/nambari hii ya mfululizo. Haiwezi kubadilishwa ikiwa tayari imehamishwa.',
                    'company'                => 'Kampuni',
                    'reference'              => 'Marejeleo',
                    'reference-hint-tooltip' => 'Nambari ya marejeleo ya ndani, ikiwa ni tofauti na nambari ya kundi/mfululizo ya mtengenezaji.',
                    'description'            => 'Maelezo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'         => 'Jina',
            'product'      => 'Bidhaa',
            'on-hand-qty'  => 'Idadi iliyopo',
            'reference'    => 'Marejeleo ya ndani',
            'created-at'   => 'Iliundwa',
            'updated-at'   => 'Ilisasishwa',
        ],

        'groups' => [
            'product'        => 'Bidhaa',
            'location'       => 'Eneo',
            'created-at'     => 'Iliundwa',
        ],

        'filters' => [
            'product'  => 'Bidhaa',
            'location' => 'Eneo',
            'creator'  => 'Muundaji',
            'company'  => 'Kampuni',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kundi limefutwa',
                        'body'  => 'Kundi limefutwa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Kundi halikuweza kufutwa',
                        'body'  => 'Kundi haliwezi kufutwa kwa sababu linatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Chapisha msimbopau',
            ],

            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Makundi yamefutwa',
                        'body'  => 'Makundi yamefutwa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Makundi hayakuweza kufutwa',
                        'body'  => 'Makundi hayawezi kufutwa kwa sababu yanatumika.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Maelezo ya kundi/nambari ya mfululizo',

                'entries' => [
                    'name'        => 'Jina la kundi',
                    'product'     => 'Bidhaa',
                    'reference'   => 'Marejeleo',
                    'description' => 'Maelezo',
                    'on-hand-qty' => 'Idadi iliyopo',
                    'company'     => 'Kampuni',
                    'created-at'  => 'Iliundwa',
                    'updated-at'  => 'Ilisasishwa mwisho',
                ],
            ],

            'record-information' => [
                'title' => 'Taarifa za rekodi',

                'entries' => [
                    'created-by'   => 'Imeundwa na',
                    'created-at'   => 'Iliundwa',
                    'last-updated' => 'Ilisasishwa mwisho',
                ],
            ],
        ],
    ],
];
