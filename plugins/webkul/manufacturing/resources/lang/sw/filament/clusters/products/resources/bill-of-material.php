<?php

return [
    'navigation' => [
        'title' => 'Orodha za malighafi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'reference'             => 'Rejea',
                    'reference-placeholder' => 'mf. BOM-001',
                    'product'               => 'Bidhaa',
                    'product-variant'       => 'Aina ya bidhaa',
                    'quantity'              => 'Idadi',
                    'uom'                   => 'Kipimo',
                    'operation-type'        => 'Aina ya operesheni',
                    'company'               => 'Kampuni',
                    'type'                  => 'Aina ya orodha',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Mengineyo',
                'fields' => [
                    'kit-information'                         => 'Taarifa za kifurushi',
                    'kit-information-content'                 => 'Orodha ya malighafi ya aina ya kifurushi hutumika kupanga vipengele kwa ajili ya uhamisho au mauzo, badala ya kuzalishwa kupitia oda ya uzalishaji.',
                    'manufacturing-lead-time'                 => 'Muda wa uzalishaji',
                    'days-to-prepare-manufacturing-order'     => 'Siku za kuandaa oda ya uzalishaji',
                    'days-suffix'                             => 'siku',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'Vipengele',
                'add-action' => 'Ongeza mstari',
                'columns'    => [
                    'component'              => 'Kipengele',
                    'apply-on-variants'      => 'Tumia kwenye aina za bidhaa',
                    'consumed-in-operation'  => 'Hutumika katika operesheni',
                    'highlight-consumption'  => 'Angazia matumizi',
                    'quantity'               => 'Idadi',
                    'uom'                    => 'Kipimo cha bidhaa',
                ],
                'validation' => [
                    'component-different-from-product' => 'Kipengele lazima kiwe tofauti na bidhaa inayozalishwa.',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'Jina',
                        'type'            => 'Aina',
                        'category'        => 'Kategoria',
                        'company'         => 'Kampuni',
                        'uom'             => 'Kipimo',
                        'uom-placeholder' => 'Kipimo',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'Operesheni',
                'add-action' => 'Ongeza mstari',
                'actions'    => [
                    'edit'                 => 'Hariri operesheni',
                    'copy-existing'        => 'Nakili operesheni zilizopo',
                    'copy-existing-fields' => [
                        'operation' => 'Operesheni',
                    ],
                ],
                'columns'    => [
                    'operation'        => 'Operesheni',
                    'work-center'      => 'Kituo cha kazi',
                    'time-mode'        => 'Ukokotoaji wa muda',
                    'time-mode-batch'  => 'Kwa oda za mwisho',
                    'company'          => 'Kampuni',
                    'apply-on-variants'=> 'Tumia kwenye aina za bidhaa',
                    'duration'         => 'Muda (dakika)',
                ],
            ],
            'by-products' => [
                'title'      => 'Bidhaa za ziada',
                'add-action' => 'Ongeza mstari',
                'columns'    => [
                    'product'   => 'Bidhaa ya ziada',
                    'quantity'  => 'Idadi',
                    'uom'       => 'Kipimo',
                    'operation' => 'Huzalishwa katika operesheni',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Mengineyo',
                'fields' => [
                    'ready-to-produce'       => 'Utayari wa uzalishaji',
                    'routing'                => 'Mpangilio wa njia',
                    'consumption'            => 'Matumizi yanayobadilika',
                    'operation-dependencies' => 'Utegemezi wa operesheni',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'Rejea',
            'product'    => 'Bidhaa',
            'quantity'   => 'Idadi',
            'uom'        => 'Kipimo',
            'type'       => 'Aina ya orodha',
            'company'    => 'Kampuni',
            'deleted-at' => 'Ilifutwa',
            'updated-at' => 'Ilisasishwa',
        ],
        'filters' => [
            'product' => 'Bidhaa',
            'type'    => 'Aina ya orodha',
            'company' => 'Kampuni',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Orodha ya malighafi imerejeshwa',
                    'body'  => 'Orodha ya malighafi imerejeshwa kikamilifu.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Orodha ya malighafi imewekwa kwenye kumbukumbu',
                    'body'  => 'Orodha ya malighafi imewekwa kwenye kumbukumbu kikamilifu.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Orodha ya malighafi imefutwa',
                        'body'  => 'Orodha ya malighafi imefutwa kabisa.',
                    ],
                    'error' => [
                        'title' => 'Orodha ya malighafi haikuweza kufutwa',
                        'body'  => 'Orodha ya malighafi haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Orodha za malighafi zimerejeshwa',
                    'body'  => 'Orodha za malighafi zilizochaguliwa zimerejeshwa kikamilifu.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Orodha za malighafi zimewekwa kwenye kumbukumbu',
                    'body'  => 'Orodha za malighafi zilizochaguliwa zimewekwa kwenye kumbukumbu kikamilifu.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Orodha za malighafi zimefutwa',
                        'body'  => 'Orodha za malighafi zilizochaguliwa zimefutwa kabisa.',
                    ],
                    'error' => [
                        'title' => 'Orodha za malighafi hazikuweza kufutwa',
                        'body'  => 'Orodha moja au zaidi kati ya zilizochaguliwa zinatumika kwa sasa.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'reference'      => 'Rejea',
                    'product'        => 'Bidhaa',
                    'product-variant'=> 'Aina ya bidhaa',
                    'quantity'       => 'Idadi',
                    'uom'            => 'Kipimo',
                    'operation-type' => 'Aina ya operesheni',
                    'company'        => 'Kampuni',
                    'type'           => 'Aina ya orodha',
                ],
            ],
            'record-information' => [
                'title'   => 'Taarifa za rekodi',
                'entries' => [
                    'created-by'   => 'Imeundwa na',
                    'created-at'   => 'Iliundwa',
                    'last-updated' => 'Ilisasishwa mwisho',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'   => 'Vipengele',
                'entries' => [
                    'component' => 'Kipengele',
                    'operation' => 'Operesheni',
                    'quantity'  => 'Idadi',
                    'uom'       => 'Kipimo cha bidhaa',
                ],
            ],
            'operations' => [
                'title'   => 'Operesheni',
                'entries' => [
                    'operation'   => 'Operesheni',
                    'work-center' => 'Kituo cha kazi',
                    'time-mode'   => 'Ukokotoaji wa muda',
                    'duration'    => 'Muda (dakika)',
                ],
            ],
            'by-products' => [
                'title'   => 'Bidhaa za ziada',
                'entries' => [
                    'product'   => 'Bidhaa ya ziada',
                    'quantity'  => 'Idadi',
                    'uom'       => 'Kipimo',
                    'operation' => 'Huzalishwa katika operesheni',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Mengineyo',
                'entries' => [
                    'kit-information'                         => 'Taarifa za kifurushi',
                    'kit-information-content'                 => 'Orodha ya malighafi ya aina ya kifurushi hutumika kupanga vipengele kwa ajili ya uhamisho au mauzo, badala ya kuzalishwa kupitia oda ya uzalishaji.',
                    'ready-to-produce'                        => 'Utayari wa uzalishaji',
                    'routing'                                 => 'Mpangilio wa njia',
                    'consumption'                             => 'Matumizi yanayobadilika',
                    'operation-dependencies'                  => 'Utegemezi wa operesheni',
                    'manufacturing-lead-time'                 => 'Muda wa uzalishaji',
                    'days-to-prepare-manufacturing-order'     => 'Siku za kuandaa oda ya uzalishaji',
                    'days-suffix'                             => 'siku',
                ],
            ],
        ],
    ],
];
