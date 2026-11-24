<?php

return [
    'navigation' => [
        'title' => 'Maghala',
        'group' => 'Usimamizi wa ghala',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'name'                    => 'Jina',
                    'name-placeholder'        => 'mf. Ghala kuu',
                    'code'                    => 'Jina fupi',
                    'code-placeholder'        => 'mf. GK',
                    'code-hint-tooltip'       => 'Jina fupi hutumika kama kitambulisho cha ghala.',
                    'company'                 => 'Kampuni',
                    'multi-warehouse-warning' => 'Kuunda ghala jipya kutawasha kiotomatiki mpangilio wa «Maeneo ya uhifadhi».',
                    'address'                 => 'Anwani',
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',

                'fields' => [
                    'shipment-management'              => 'Usimamizi wa usafirishaji',
                    'incoming-shipments'               => 'Usafirishaji unaoingia',
                    'incoming-shipments-hint-tooltip'  => 'Njia chaguomsingi ya kuingia',
                    'outgoing-shipments'               => 'Usafirishaji unaotoka',
                    'outgoing-shipments-hint-tooltip'  => 'Njia chaguomsingi ya kutoka',
                    'manufacture'                      => 'Uzalishaji',
                    'manufacture-hint-tooltip'         => 'Njia chaguomsingi ya uzalishaji',
                    'resupply-management'              => 'Usimamizi wa kujaza upya',
                    'resupply-management-hint-tooltip' => 'Njia zitaundwa kiotomatiki ili kujaza upya ghala hili kutoka maghala yaliyochaguliwa.',
                    'resupply-from'                    => 'Jaza upya kutoka',
                ],
            ],

            'additional' => [
                'title'  => 'Taarifa za ziada',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'code'       => 'Jina fupi',
            'company'    => 'Kampuni',
            'address'    => 'Anwani',
            'deleted-at' => 'Ilifutwa',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'groups' => [
            'address'       => 'Anwani',
            'company'       => 'Kampuni',
            'created-at'    => 'Iliundwa',
            'updated-at'    => 'Ilisasishwa',
        ],

        'filters' => [
            'company' => 'Kampuni',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Ghala limerejeshwa',
                    'body'  => 'Ghala limerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ghala limefutwa',
                    'body'  => 'Ghala limefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ghala limefutwa kabisa',
                        'body'  => 'Ghala limefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Ghala halikuweza kufutwa',
                        'body'  => 'Ghala haliwezi kufutwa kwa sababu linatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Maghala yamerejeshwa',
                    'body'  => 'Maghala yamerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Maghala yamefutwa',
                    'body'  => 'Maghala yamefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Maghala yamefutwa kabisa',
                        'body'  => 'Maghala yamefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Maghala hayakuweza kufutwa',
                        'body'  => 'Maghala hayawezi kufutwa kwa sababu yanatumika.',
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
                    'name'    => 'Jina la ghala',
                    'code'    => 'Msimbo wa ghala',
                    'company' => 'Kampuni',
                    'address' => 'Anwani',
                ],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'entries' => [
                    'shipment-management' => 'Usimamizi wa usafirishaji',
                    'incoming-shipments'  => 'Usafirishaji unaoingia',
                    'outgoing-shipments'  => 'Usafirishaji unaotoka',
                    'resupply-management' => 'Usimamizi wa kujaza upya',
                    'resupply-from'       => 'Jaza upya kutoka',
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
