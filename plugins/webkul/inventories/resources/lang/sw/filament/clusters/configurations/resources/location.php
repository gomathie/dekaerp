<?php

return [
    'navigation' => [
        'title' => 'Maeneo',
        'group' => 'Usimamizi wa ghala',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'location'                     => 'Eneo',
                    'location-placeholder'         => 'mf. Akiba ya ziada',
                    'parent-location'              => 'Eneo kuu',
                    'parent-location-hint-tooltip' => 'Eneo kuu linalojumuisha eneo hili. Kwa mfano, «Eneo la kusafirisha» ni sehemu ya eneo kuu «Lango 1».',
                    'external-notes'               => 'Vidokezo vya nje',
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',

                'fields' => [
                    'location-type'                 => 'Aina ya eneo',
                    'company'                       => 'Kampuni',
                    'storage-category'              => 'Kategoria ya uhifadhi',
                    'is-scrap'                      => 'Ni eneo la chakavu?',
                    'is-scrap-hint-tooltip'         => 'Chagua kisanduku hiki ili eneo hili lihifadhi bidhaa chakavu au zilizoharibika.',
                    'is-dock'                       => 'Ni eneo la upakiaji?',
                    'is-dock-hint-tooltip'          => 'Chagua kisanduku hiki ili eneo hili lihifadhi bidhaa zilizo tayari kusafirishwa.',
                    'is-replenish'                  => 'Ni eneo la kujaza upya?',
                    'is-replenish-hint-tooltip'     => 'Washa ili kupata idadi zote zinazohitajika kujaza upya eneo hili.',
                    'logistics'                     => 'Lojistiki',
                    'removal-strategy'              => 'Mkakati wa kutoa',
                    'removal-strategy-hint-tooltip' => 'Huweka njia chaguomsingi ya kuamua rafu, kundi na eneo halisi la kuchukua bidhaa. Njia hii inaweza kuwekwa kwenye kategoria ya bidhaa; isipowekwa hapa, njia ya eneo kuu hutumika.',
                    'cyclic-counting'               => 'Hesabu ya mzunguko',
                    'inventory-frequency'           => 'Marudio ya hesabu ya bidhaa',
                    'last-inventory'                => 'Hesabu ya mwisho',
                    'last-inventory-hint-tooltip'   => 'Tarehe ya hesabu ya mwisho ya bidhaa katika eneo hili.',
                    'next-expected'                 => 'Inayofuata',
                    'next-expected-hint-tooltip'    => 'Tarehe ya hesabu inayofuata iliyopangwa kwa ratiba ya mzunguko.',
                ],
            ],

            'additional' => [
                'title'  => 'Taarifa za ziada',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'location'         => 'Eneo',
            'type'             => 'Aina',
            'storage-category' => 'Kategoria ya uhifadhi',
            'company'          => 'Kampuni',
            'deleted-at'       => 'Ilifutwa',
            'created-at'       => 'Iliundwa',
            'updated-at'       => 'Ilisasishwa',
        ],

        'groups' => [
            'warehouse'       => 'Ghala',
            'type'            => 'Aina',
            'created-at'      => 'Iliundwa',
            'updated-at'      => 'Ilisasishwa',
        ],

        'filters' => [
            'location' => 'Eneo',
            'type'     => 'Aina',
            'company'  => 'Kampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Eneo limesasishwa',
                    'body'  => 'Eneo limesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Eneo limerejeshwa',
                    'body'  => 'Eneo limerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Eneo limefutwa',
                    'body'  => 'Eneo limefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Eneo limefutwa kabisa',
                        'body'  => 'Eneo limefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Eneo halikuweza kufutwa',
                        'body'  => 'Eneo haliwezi kufutwa kwa sababu linatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'print' => [
                'label' => 'Chapisha msimbopau',
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Maeneo yamerejeshwa',
                    'body'  => 'Maeneo yamerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Maeneo yamefutwa',
                    'body'  => 'Maeneo yamefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Maeneo yamefutwa kabisa',
                        'body'  => 'Maeneo yamefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Maeneo hayakuweza kufutwa',
                        'body'  => 'Maeneo hayawezi kufutwa kwa sababu yanatumika.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'entries' => [
                    'location'                     => 'Eneo',
                    'location-placeholder'         => 'mf. Akiba ya ziada',
                    'parent-location'              => 'Eneo kuu',
                    'parent-location-hint-tooltip' => 'Eneo kuu linalojumuisha eneo hili. Kwa mfano, «Eneo la kusafirisha» ni sehemu ya eneo kuu «Lango 1».',
                    'external-notes'               => 'Vidokezo vya nje',
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',

                'entries' => [
                    'location-type'                 => 'Aina ya eneo',
                    'company'                       => 'Kampuni',
                    'storage-category'              => 'Kategoria ya uhifadhi',
                    'is-scrap'                      => 'Ni eneo la chakavu?',
                    'is-scrap-hint-tooltip'         => 'Chagua kisanduku hiki ili eneo hili lihifadhi bidhaa chakavu au zilizoharibika.',
                    'is-dock'                       => 'Ni eneo la upakiaji?',
                    'is-dock-hint-tooltip'          => 'Chagua kisanduku hiki ili eneo hili lihifadhi bidhaa zilizo tayari kusafirishwa.',
                    'is-replenish'                  => 'Ni eneo la kujaza upya?',
                    'is-replenish-hint-tooltip'     => 'Washa ili kupata idadi zote zinazohitajika kujaza upya eneo hili.',
                    'logistics'                     => 'Lojistiki',
                    'removal-strategy'              => 'Mkakati wa kutoa',
                    'removal-strategy-hint-tooltip' => 'Huweka njia chaguomsingi ya kuamua rafu, kundi na eneo halisi la kuchukua bidhaa. Njia hii inaweza kuwekwa kwenye kategoria ya bidhaa; isipowekwa hapa, njia ya eneo kuu hutumika.',
                    'cyclic-counting'               => 'Hesabu ya mzunguko',
                    'inventory-frequency'           => 'Marudio ya hesabu ya bidhaa',
                    'last-inventory'                => 'Hesabu ya mwisho',
                    'last-inventory-hint-tooltip'   => 'Tarehe ya hesabu ya mwisho ya bidhaa katika eneo hili.',
                    'next-expected'                 => 'Inayofuata',
                    'next-expected-hint-tooltip'    => 'Tarehe ya hesabu inayofuata iliyopangwa kwa ratiba ya mzunguko.',
                ],
            ],

            'additional' => [
                'title'  => 'Taarifa za ziada',
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
