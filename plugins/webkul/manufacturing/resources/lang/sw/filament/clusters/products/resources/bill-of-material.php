<?php

return [
    'navigation' => [
        'title' => 'Bili ya Materials',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'reference'             => 'Kumbukumbu',
                    'reference-placeholder' => 'eg. BOM-001',
                    'product'               => 'Bidhaa',
                    'product-variant'       => 'Bidhaa Variant',
                    'quantity'              => 'Kiasi',
                    'uom'                   => 'UOM',
                    'operation-type'        => 'Operation Aina',
                    'company'               => 'Kampuni',
                    'type'                  => 'BoM Aina',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'kit-information'                         => 'Kit Taarifa',
                    'kit-information-content'                 => 'kit BoM ni inatumika hadi kundi components kwa uhamisho au mauzo, instead ya produced kupitia uzalishaji agizo.',
                    'manufacturing-lead-time'                 => 'Uzalishaji Lead Time',
                    'days-to-prepare-manufacturing-order'     => 'Siku hadi prepare Uzalishaji Agizo',
                    'days-suffix'                             => 'siku',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'Components',
                'add-action' => 'Ongeza mstari',
                'columns'    => [
                    'component'              => 'Component',
                    'apply-on-variants'      => 'Tumia washa Variants',
                    'consumed-in-operation'  => 'Consumed katika Operation',
                    'highlight-consumption'  => 'Highlight Consumption',
                    'quantity'               => 'Kiasi',
                    'uom'                    => 'Bidhaa Kipimo',
                ],
                'validation' => [
                    'component-different-from-product' => 'component lazima kuwa different kutoka bidhaa manufactured.',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'Jina',
                        'type'            => 'Aina',
                        'category'        => 'Kategoria',
                        'company'         => 'Kampuni',
                        'uom'             => 'UOM',
                        'uom-placeholder' => 'UOM',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'Shughuli',
                'add-action' => 'Ongeza mstari',
                'actions'    => [
                    'edit'                 => 'Hariri Operation',
                    'copy-existing'        => 'Nakili Existing Shughuli',
                    'copy-existing-fields' => [
                        'operation' => 'Operation',
                    ],
                ],
                'columns'    => [
                    'operation'        => 'Operation',
                    'work-center'      => 'Work Center',
                    'time-mode'        => 'Duration Computation',
                    'time-mode-batch'  => 'Computed washa mwisho',
                    'company'          => 'Kampuni',
                    'apply-on-variants'=> 'Tumia washa Variants',
                    'duration'         => 'Duration (dakika)',
                ],
            ],
            'by-products' => [
                'title'      => 'Na-bidhaa',
                'add-action' => 'Ongeza mstari',
                'columns'    => [
                    'product'   => 'Na-bidhaa',
                    'quantity'  => 'Kiasi',
                    'uom'       => 'Kipimo',
                    'operation' => 'Produced katika Operation',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'ready-to-produce'       => 'Uzalishaji Readiness',
                    'routing'                => 'Routing',
                    'consumption'            => 'Flexible Consumption',
                    'operation-dependencies' => 'Operation Dependencies',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'Kumbukumbu',
            'product'    => 'Bidhaa',
            'quantity'   => 'Kiasi',
            'uom'        => 'UOM',
            'type'       => 'BOM Aina',
            'company'    => 'Kampuni',
            'deleted-at' => 'Imefutwa',
            'updated-at' => 'Imesasishwa',
        ],
        'filters' => [
            'product' => 'Bidhaa',
            'type'    => 'BOM Aina',
            'company' => 'Kampuni',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bili ya material restored',
                    'body'  => 'bili ya material ina restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Bili ya material imehifadhiwa',
                    'body'  => 'bili ya material ina imehifadhiwa successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bili ya material imefutwa',
                        'body'  => 'bili ya material ina permanently imefutwa.',
                    ],
                    'error' => [
                        'title' => 'Bili ya material could si kuwa imefutwa',
                        'body'  => 'bili ya material haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Bili ya material restored',
                    'body'  => 'imechaguliwa bili ya material ina restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Bili ya material imehifadhiwa',
                    'body'  => 'imechaguliwa bili ya material ina imehifadhiwa successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Bili ya material imefutwa',
                        'body'  => 'imechaguliwa bili ya material ina permanently imefutwa.',
                    ],
                    'error' => [
                        'title' => 'Bili ya material could si kuwa imefutwa',
                        'body'  => 'One au zaidi imechaguliwa bili ya material ni currently katika tumia.',
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
                    'reference'      => 'Kumbukumbu',
                    'product'        => 'Bidhaa',
                    'product-variant'=> 'Bidhaa Variant',
                    'quantity'       => 'Kiasi',
                    'uom'            => 'UOM',
                    'operation-type' => 'Operation Aina',
                    'company'        => 'Kampuni',
                    'type'           => 'BoM Aina',
                ],
            ],
            'record-information' => [
                'title'   => 'Rekodi Taarifa',
                'entries' => [
                    'created-by'   => 'Imeundwa Na',
                    'created-at'   => 'Imeundwa',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'   => 'Components',
                'entries' => [
                    'component' => 'Component',
                    'operation' => 'Operation',
                    'quantity'  => 'Kiasi',
                    'uom'       => 'Bidhaa Kipimo',
                ],
            ],
            'operations' => [
                'title'   => 'Shughuli',
                'entries' => [
                    'operation'   => 'Operation',
                    'work-center' => 'Work Center',
                    'time-mode'   => 'Duration Computation',
                    'duration'    => 'Duration (dakika)',
                ],
            ],
            'by-products' => [
                'title'   => 'Na-bidhaa',
                'entries' => [
                    'product'   => 'Na-bidhaa',
                    'quantity'  => 'Kiasi',
                    'uom'       => 'Kipimo',
                    'operation' => 'Produced katika Operation',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Miscellaneous',
                'entries' => [
                    'kit-information'                         => 'Kit Taarifa',
                    'kit-information-content'                 => 'kit BoM ni inatumika hadi kundi components kwa uhamisho au mauzo, instead ya produced kupitia uzalishaji agizo.',
                    'ready-to-produce'                        => 'Uzalishaji Readiness',
                    'routing'                                 => 'Routing',
                    'consumption'                             => 'Flexible Consumption',
                    'operation-dependencies'                  => 'Operation Dependencies',
                    'manufacturing-lead-time'                 => 'Uzalishaji Lead Time',
                    'days-to-prepare-manufacturing-order'     => 'Siku hadi prepare Uzalishaji Agizo',
                    'days-suffix'                             => 'siku',
                ],
            ],
        ],
    ],
];
