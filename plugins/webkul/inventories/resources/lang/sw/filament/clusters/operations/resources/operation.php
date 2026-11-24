<?php

return [
    'navigation' => [
        'title' => 'Bidhaa',
        'group' => 'Bidhaa ghalani',
    ],

    'global-search' => [
        'partner' => 'Mhusika',
        'origin'  => 'Chanzo',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'receive-from'         => 'Pokea kutoka',
                    'contact'              => 'Mhusika',
                    'delivery-address'     => 'Anwani ya uwasilishaji',
                    'operation-type'       => 'Aina ya operesheni',
                    'source-location'      => 'Eneo la chanzo',
                    'destination-location' => 'Eneo la kufikia',
                ],
            ],

            'additional-fields' => [
                'title' => 'Taarifa za ziada',
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Operesheni',

                'columns' => [
                    'product'                    => 'Bidhaa',
                    'final-location'             => 'Eneo la mwisho',
                    'description'                => 'Maelezo',
                    'scheduled-at'               => 'Imepangwa',
                    'deadline'                   => 'Muda wa mwisho',
                    'packaging'                  => 'Ufungashaji',
                    'demand'                     => 'Mahitaji',
                    'quantity'                   => 'Idadi',
                    'insufficient-stock-tooltip' => 'Idadi iliyopo haitoshi',
                    'unit'                       => 'Kipimo',
                    'picked'                     => 'Imechukuliwa',
                ],

                'actions' => [
                    'open-product' => [
                        'tooltip' => 'Fungua bidhaa',
                    ],
                ],

                'fields' => [
                    'product'        => 'Bidhaa',
                    'final-location' => 'Eneo la mwisho',
                    'description'    => 'Maelezo',
                    'scheduled-at'   => 'Imepangwa',
                    'deadline'       => 'Muda wa mwisho',
                    'packaging'      => 'Ufungashaji',
                    'demand'         => 'Mahitaji',
                    'quantity'       => 'Idadi',
                    'unit'           => 'Kipimo',
                    'picked'         => 'Imechukuliwa',

                    'lines' => [
                        'modal-heading'             => 'Simamia mienendo ya bidhaa',
                        'modal-submit-action-label' => 'Hifadhi',
                        'add-line'                  => 'Ongeza mstari',

                        'actions' => [
                            'generate' => 'Tengeneza nambari za mfululizo/makundi',
                            'import'   => 'Ingiza nambari za mfululizo/makundi',
                        ],

                        'fields' => [
                            'lot'                => 'Kundi/Nambari ya mfululizo',
                            'pick-from'          => 'Chukua kutoka',
                            'location'           => 'Hifadhi katika',
                            'package'            => 'Kifurushi cha kufikia',
                            'quantity'           => 'Idadi',
                            'uom'                => 'Kipimo',
                            'first-lot'          => 'Nambari ya kundi la kwanza',
                            'quantity-per-lot'   => 'Idadi kwa kila kundi',
                            'quantity-received'  => 'Idadi iliyopokelewa',
                            'keep-current-lines' => 'Baki na mistari ya sasa',
                            'serials'            => 'Makundi/Nambari za mfululizo',
                            'serials-helper'     => 'Kundi/nambari moja ya mfululizo kwa kila mstari.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Ziada',

                'fields' => [
                    'responsible'                  => 'Msimamizi',
                    'shipping-policy'              => 'Sera ya usafirishaji',
                    'shipping-policy-hint-tooltip' => 'Huamua kama bidhaa ziwasilishwe kwa sehemu au zote kwa pamoja.',
                    'scheduled-at'                 => 'Imepangwa',
                    'scheduled-at-hint-tooltip'    => 'Muda uliopangwa wa kushughulikia sehemu ya kwanza ya usafirishaji. Ukiweka thamani hapa kwa mkono, itakuwa tarehe inayotarajiwa kwa mienendo yote ya bidhaa.',
                    'source-document'              => 'Hati chanzo',
                    'source-document-hint-tooltip' => 'Marejeleo ya hati',
                ],
            ],

            'note' => [
                'title' => 'Kidokezo',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'        => 'Kipendwa',
            'reference'       => 'Marejeleo',
            'from'            => 'Kutoka',
            'to'              => 'Kwenda',
            'contact'         => 'Mhusika',
            'responsible'     => 'Msimamizi',
            'scheduled-at'    => 'Imepangwa',
            'deadline'        => 'Muda wa mwisho',
            'closed-at'       => 'Ilifungwa',
            'source-document' => 'Hati chanzo',
            'operation-type'  => 'Aina ya operesheni',
            'company'         => 'Kampuni',
            'state'           => 'Hali',
            'deleted-at'      => 'Ilifutwa',
            'created-at'      => 'Iliundwa',
            'updated-at'      => 'Ilisasishwa',
        ],

        'groups' => [
            'state'           => 'Hali',
            'source-document' => 'Hati chanzo',
            'operation-type'  => 'Aina ya operesheni',
            'scheduled-at'    => 'Imepangwa',
            'created-at'      => 'Iliundwa',
        ],

        'filters' => [
            'operation-type'       => 'Aina ya operesheni',
            'name'                 => 'Jina',
            'state'                => 'Hali',
            'partner'              => 'Mhusika',
            'responsible'          => 'Msimamizi',
            'owner'                => 'Mmiliki',
            'source-location'      => 'Eneo la chanzo',
            'destination-location' => 'Eneo la kufikia',
            'deadline'             => 'Muda wa mwisho',
            'scheduled-at'         => 'Imepangwa',
            'closed-at'            => 'Ilifungwa',
            'created-at'           => 'Iliundwa',
            'updated-at'           => 'Ilisasishwa',
            'company'              => 'Kampuni',
            'creator'              => 'Muundaji',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'contact'              => 'Mhusika',
                    'operation-type'       => 'Aina ya operesheni',
                    'source-location'      => 'Eneo la chanzo',
                    'destination-location' => 'Eneo la kufikia',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Operesheni',
                'entries' => [
                    'product'        => 'Bidhaa',
                    'final-location' => 'Eneo la mwisho',
                    'description'    => 'Maelezo',
                    'scheduled-at'   => 'Imepangwa',
                    'deadline'       => 'Muda wa mwisho',
                    'packaging'      => 'Ufungashaji',
                    'demand'         => 'Mahitaji',
                    'quantity'       => 'Idadi',
                    'unit'           => 'Kipimo',
                    'picked'         => 'Imechukuliwa',
                ],
            ],
            'additional' => [
                'title'   => 'Taarifa za ziada',
                'entries' => [
                    'responsible'     => 'Msimamizi',
                    'shipping-policy' => 'Sera ya usafirishaji',
                    'scheduled-at'    => 'Imepangwa',
                    'source-document' => 'Hati chanzo',
                ],
            ],
            'note' => [
                'title' => 'Kidokezo',
            ],
        ],
    ],

    'tabs' => [
        'todo'        => 'Za kufanya',
        'my'          => 'Uhamisho wangu',
        'starred'     => 'Zenye nyota',
        'draft'       => 'Rasimu',
        'waiting'     => 'Inasubiri',
        'ready'       => 'Tayari',
        'late'        => 'Zimechelewa',
        'done'        => 'Imekamilika',
        'canceled'    => 'Imeghairiwa',
        'back-orders' => 'Oda zilizosalia',
    ],

    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'Onyo la usahihi wa kipimo',
            'body'  => 'Unatumia kipimo kidogo kuliko kile kinachotumika kuhifadhi bidhaa hii. Hii inaweza kusababisha matatizo ya ukadiriaji kwenye idadi zilizotengwa. Fikiria kutumia kipimo kidogo zaidi kwa thamani ya bidhaa ghalani, au punguza usahihi wa ukadiriaji wa kipimo chako cha msingi.',
        ],
    ],
];
