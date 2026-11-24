<?php

return [
    'navigation' => [
        'title' => 'Oda za uzalishaji',
        'group' => 'Operesheni',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'product'                => 'Bidhaa',
                    'quantity'               => 'Idadi',
                    'uom'                    => 'Kipimo',
                    'bill-of-material'       => 'Orodha ya malighafi',
                    'scheduled-date'         => 'Tarehe iliyopangwa',
                    'scheduled-end'          => 'Mwisho uliopangwa',
                    'responsible'            => 'Msimamizi',
                    'to-produce'             => 'Ya kuzalisha',
                    'to-produce-placeholder' => 'Hakiki ya picha',
                    'uom-placeholder'        => 'Kipimo',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Vipengele',
                'add-action'   => 'Ongeza mstari',
                'process-note' => 'Vipengele vitatengenezwa kadiri mchakato wa uzalishaji unavyojengwa.',
                'columns'      => [
                    'component'          => 'Bidhaa',
                    'from'               => 'Kutoka',
                    'to-consume'         => 'Ya kutumia',
                    'to-consume-tooltip' => 'Idadi inayopatikana haitoshi',
                    'quantity'           => 'Idadi',
                    'uom'                => 'Kipimo',
                    'forecast'           => 'Utabiri',
                ],
            ],
            'work-orders' => [
                'title'        => 'Oda za kazi',
                'add-action'   => 'Ongeza mstari',
                'process-note' => 'Oda za kazi zitatengenezwa baada ya mchakato wa uzalishaji kusanidiwa.',
                'columns'      => [
                    'operation'          => 'Operesheni',
                    'work-center'        => 'Kituo cha kazi',
                    'product'            => 'Bidhaa',
                    'quantity-remaining' => 'Idadi iliyobaki',
                    'quantity-produced'  => 'Idadi iliyozalishwa',
                    'start'              => 'Mwanzo',
                    'end'                => 'Mwisho',
                    'expected-duration'  => 'Muda unaotarajiwa',
                    'real-duration'      => 'Muda halisi',
                    'status'             => 'Hali',
                    'lot-serial'         => 'Kundi/Namba ya mfululizo',
                ],
                'actions'      => [
                    'open-work-order' => [
                        'tooltip' => 'Fungua oda ya kazi',
                    ],

                    'done' => [
                        'label' => 'Imekamilika',
                    ],
                ],
            ],
            'by-products' => [
                'title'        => 'Bidhaa za ziada',
                'process-note' => 'Bidhaa za ziada zitatengenezwa kadiri mchakato wa uzalishaji unavyojengwa.',
                'columns'      => [
                    'product'    => 'Bidhaa',
                    'to'         => 'Kwenda',
                    'to-produce' => 'Ya kuzalisha',
                    'uom'        => 'Kipimo',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Mengineyo',
                'fields' => [
                    'operation-type'             => 'Aina ya operesheni',
                    'source'                     => 'Chanzo',
                    'finished-products-location' => 'Mahali pa bidhaa zilizokamilika',
                    'company'                    => 'Kampuni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'              => 'Rejea',
            'start'                  => 'Mwanzo',
            'end'                    => 'Mwisho',
            'deadline'               => 'Tarehe ya mwisho',
            'product'                => 'Bidhaa',
            'lot-serial-number'      => 'Namba ya kundi/mfululizo',
            'bill-of-material'       => 'Orodha ya malighafi',
            'source'                 => 'Chanzo',
            'responsible'            => 'Msimamizi',
            'mo-readiness'           => 'Utayari wa oda',
            'component-status'       => 'Hali ya vipengele',
            'quantity'               => 'Idadi',
            'uom'                    => 'Kipimo',
            'consumption-efficiency' => 'Ufanisi wa matumizi',
            'expected-duration'      => 'Muda unaotarajiwa',
            'real-duration'          => 'Muda halisi',
            'company'                => 'Kampuni',
            'state'                  => 'Hali',
        ],
        'groups' => [
            'state'            => 'Hali',
            'product'          => 'Bidhaa',
            'bill-of-material' => 'Orodha ya malighafi',
            'responsible'      => 'Msimamizi',
            'deadline'         => 'Tarehe ya mwisho',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla',
                'entries' => [
                    'product'                  => 'Bidhaa',
                    'scheduled-date'           => 'Tarehe iliyopangwa',
                    'responsible'              => 'Msimamizi',
                    'quantity'                 => 'Idadi',
                    'uom'                      => 'Kipimo',
                    'bill-of-material'         => 'Orodha ya malighafi',
                    'operation-type'           => 'Aina ya operesheni',
                    'consumption-efficiency'   => 'Ufanisi wa matumizi',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'        => 'Vipengele',
                'process-note' => 'Vipengele vitapatikana baada ya mchakato wa uzalishaji kusanidiwa.',
                'columns'      => [
                    'component' => 'Kipengele',
                    'quantity'  => 'Idadi',
                    'uom'       => 'Kipimo',
                ],
            ],
            'work-orders' => [
                'title'        => 'Oda za kazi',
                'process-note' => 'Oda za kazi zitapatikana baada ya mchakato wa uzalishaji kusanidiwa.',
                'columns'      => [
                    'operation'          => 'Operesheni',
                    'work-center'        => 'Kituo cha kazi',
                    'product'            => 'Bidhaa',
                    'quantity-remaining' => 'Idadi iliyobaki',
                    'expected-duration'  => 'Muda unaotarajiwa',
                    'real-duration'      => 'Muda halisi',
                    'lot-serial'         => 'Kundi/Namba ya mfululizo',
                    'start'              => 'Mwanzo',
                    'end'                => 'Mwisho',
                ],
            ],
            'by-products' => [
                'title'        => 'Bidhaa za ziada',
                'process-note' => 'Bidhaa za ziada zitapatikana baada ya mchakato wa uzalishaji kusanidiwa.',
                'columns'      => [
                    'product'    => 'Bidhaa',
                    'to'         => 'Kwenda',
                    'to-produce' => 'Ya kuzalisha',
                    'uom'        => 'Kipimo',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Mengineyo',
                'entries' => [
                    'operation-type'             => 'Aina ya operesheni',
                    'source'                     => 'Chanzo',
                    'finished-products-location' => 'Mahali pa bidhaa zilizokamilika',
                    'company'                    => 'Kampuni',
                ],
            ],
        ],
    ],

    'pages' => [
        'shared' => [
            'header-actions' => [
                'confirm' => [
                    'label'        => 'Thibitisha',
                    'notification' => [
                        'title' => 'Oda ya uzalishaji imethibitishwa',
                    ],
                ],

                'cancel' => [
                    'label'        => 'Ghairi',
                    'notification' => [
                        'title' => 'Oda ya uzalishaji imeghairiwa',
                    ],
                ],
            ],
        ],
    ],
];
