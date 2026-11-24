<?php

return [
    'navigation' => [
        'title' => 'Kanuni',
        'group' => 'Usimamizi wa ghala',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name'                        => 'Jina',
                    'action'                      => 'Kitendo',
                    'operation-type'              => 'Aina ya operesheni',
                    'source-location'             => 'Eneo la chanzo',
                    'destination-location'        => 'Eneo la kufikia',
                    'supply-method'               => 'Njia ya ugavi',
                    'supply-method-hint-tooltip'  => 'Chukua kutoka ghalani: Bidhaa huchukuliwa moja kwa moja kutoka akiba iliyopo katika eneo la chanzo.<br/>Anzisha kanuni nyingine: Mfumo hupuuza akiba iliyopo na hutafuta kanuni ya kujaza upya eneo la chanzo.<br/>Chukua kutoka ghalani, kama hakuna, anzisha kanuni nyingine: Bidhaa huchukuliwa kwanza kutoka akiba iliyopo. Kama hakuna, mfumo hutumia kanuni ya kuleta bidhaa katika eneo la chanzo.',
                    'automatic-move'              => 'Uhamisho wa kiotomatiki',
                    'automatic-move-hint-tooltip' => 'Operesheni ya mkono: Huunda mwendo tofauti wa bidhaa baada ya ule wa sasa.<br/>Kiotomatiki bila hatua ya ziada: Hubadilisha eneo moja kwa moja katika mwendo wa awali bila kuongeza hatua.',

                    'action-information' => [
                        'pull'        => 'Bidhaa zinapohitajika katika <b>:sourceLocation</b>, :operation huundwa kutoka <b>:destinationLocation</b> ili kukidhi mahitaji.',
                        'push'        => 'Bidhaa zinapofika <b>:sourceLocation</b>,</br><b>:operation</b> huundwa ili kuzihamisha hadi <b>:destinationLocation</b>.',
                        'buy'         => 'Bidhaa zinapohitajika katika <b>:destinationLocation</b>, ombi la nukuu ya bei huundwa ili kukidhi hitaji.',
                        'manufacture' => 'Bidhaa zinapohitajika katika <b>:destinationLocation</b>, oda ya uzalishaji huundwa ili kukidhi hitaji.',
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',

                'fields' => [
                    'partner-address'              => 'Anwani ya mhusika',
                    'partner-address-hint-tooltip' => 'Anwani ambapo bidhaa zinapaswa kuwasilishwa. Si lazima.',
                    'lead-time'                    => 'Muda wa maandalizi (siku)',
                    'lead-time-hint-tooltip'       => 'Tarehe inayotarajiwa ya uhamisho itakokotolewa kwa kutumia muda huu.',
                ],

                'fieldsets' => [
                    'applicability' => [
                        'title'  => 'Matumizi',

                        'fields' => [
                            'route'   => 'Njia',
                            'company' => 'Kampuni',
                        ],
                    ],

                    'propagation' => [
                        'title'  => 'Usambazaji',

                        'fields' => [
                            'propagation-procurement-group'              => 'Usambazaji wa kikundi cha ununuzi',
                            'propagation-procurement-group-hint-tooltip' => 'Ikichaguliwa, kughairi mwendo ulioundwa na kanuni hii pia kutaghairi mwendo unaofuata.',
                            'cancel-next-move'                           => 'Ghairi mwendo unaofuata',
                            'warehouse-to-propagate'                     => 'Ghala la kusambaza',
                            'warehouse-to-propagate-hint-tooltip'        => 'Ghala linalowekwa kwa mwendo au ununuzi ulioundwa, ambalo linaweza kutofautiana na ghala ambalo kanuni hii inatumika (mf. kwa kanuni za kujaza upya kutoka ghala jingine).',
                        ],
                    ],
                ],

            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Jina',
            'action'               => 'Kitendo',
            'source-location'      => 'Eneo la chanzo',
            'destination-location' => 'Eneo la kufikia',
            'route'                => 'Njia',
            'deleted-at'           => 'Ilifutwa',
            'created-at'           => 'Iliundwa',
            'updated-at'           => 'Ilisasishwa',
        ],

        'groups' => [
            'action'               => 'Kitendo',
            'source-location'      => 'Eneo la chanzo',
            'destination-location' => 'Eneo la kufikia',
            'route'                => 'Njia',
            'created-at'           => 'Iliundwa',
            'updated-at'           => 'Ilisasishwa',
        ],

        'filters' => [
            'action'               => 'Kitendo',
            'source-location'      => 'Eneo la chanzo',
            'destination-location' => 'Eneo la kufikia',
            'route'                => 'Njia',
            'company'              => 'Kampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kanuni imesasishwa',
                    'body'  => 'Kanuni imesasishwa kwa mafanikio.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kanuni imerejeshwa',
                    'body'  => 'Kanuni imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kanuni imefutwa',
                    'body'  => 'Kanuni imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kanuni imefutwa kabisa',
                        'body'  => 'Kanuni imefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Kanuni haikuweza kufutwa',
                        'body'  => 'Kanuni haiwezi kufutwa kwa sababu inatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Kanuni zimerejeshwa',
                    'body'  => 'Kanuni zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kanuni zimefutwa',
                    'body'  => 'Kanuni zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Kanuni zimefutwa kabisa',
                        'body'  => 'Kanuni zimefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Kanuni hazikuweza kufutwa',
                        'body'  => 'Kanuni haziwezi kufutwa kwa sababu zinatumika.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Maelezo ya kanuni',

                'description' => [
                    'pull' => 'Bidhaa zinapohitajika katika <b>:sourceLocation</b>, <b>:operation</b> huundwa kutoka <b>:destinationLocation</b> ili kukidhi mahitaji.',
                    'push' => 'Bidhaa zinapofika katika <b>:sourceLocation</b>, <b>:operation</b> huundwa ili kuzihamisha hadi <b>:destinationLocation</b>.',
                ],

                'entries' => [
                    'name'                 => 'Jina la kanuni',
                    'action'               => 'Kitendo',
                    'operation-type'       => 'Aina ya operesheni',
                    'source-location'      => 'Eneo la chanzo',
                    'destination-location' => 'Eneo la kufikia',
                    'route'                => 'Njia',
                    'company'              => 'Kampuni',
                    'partner-address'      => 'Anwani ya mhusika',
                    'lead-time'            => 'Muda wa maandalizi',
                    'action-information'   => 'Taarifa za kitendo',
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
