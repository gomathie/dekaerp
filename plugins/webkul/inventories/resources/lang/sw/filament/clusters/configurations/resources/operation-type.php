<?php

return [
    'navigation' => [
        'title' => 'Aina za operesheni',
        'group' => 'Usimamizi wa ghala',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type'             => 'Aina ya operesheni',
                    'operator-type-placeholder' => 'mf. Mapokezi',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Inatumika kwa',
                'description' => 'Chagua mahali ambapo njia hii inaweza kuchaguliwa.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'operator-type'                      => 'Aina ya operesheni',
                    'sequence-prefix'                    => 'Kiambishi awali cha mfuatano',
                    'generate-shipping-labels'           => 'Tengeneza lebo za usafirishaji',
                    'warehouse'                          => 'Ghala',
                    'show-reception-report'              => 'Onyesha ripoti ya upokeaji wakati wa kuthibitisha',
                    'show-reception-report-hint-tooltip' => 'Ikichaguliwa, mfumo utaonyesha ripoti ya upokeaji kiotomatiki wakati wa kuthibitisha, ikiwa kuna mienendo ya kugawa.',
                    'company'                            => 'Kampuni',
                    'return-type'                        => 'Aina ya marejesho',
                    'create-backorder'                   => 'Unda oda iliyosalia',
                    'move-type'                          => 'Aina ya uhamisho',
                    'move-type-hint-tooltip'             => 'Isipowekwa na hati chanzo, hii itakuwa sera chaguomsingi ya uchukuaji kwa aina hii ya operesheni.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title'  => 'Makundi/Nambari za mfululizo',

                        'fields' => [
                            'create-new'                => 'Unda mapya',
                            'create-new-hint-tooltip'   => 'Ikichaguliwa, mfumo utachukulia kuwa unakusudia kuunda makundi/nambari mpya za mfululizo, na utaweza kuziandika kwenye kisanduku cha maandishi.',
                            'use-existing'              => 'Tumia yaliyopo',
                            'use-existing-hint-tooltip' => 'Ikichaguliwa, unaweza kuchagua makundi/nambari za mfululizo au kutoweka yoyote. Hii huruhusu bidhaa kuingizwa bila kundi au bila vikwazo vya kundi linalotumika.',
                        ],
                    ],

                    'locations' => [
                        'title'  => 'Maeneo',

                        'fields' => [
                            'source-location'                   => 'Eneo la chanzo',
                            'source-location-hint-tooltip'      => 'Hili ni eneo chaguomsingi la chanzo unapounda operesheni hii kwa mkono. Linaweza kubadilishwa baadaye, na njia zinaweza kuweka eneo chaguomsingi tofauti.',
                            'destination-location'              => 'Eneo la kufikia',
                            'destination-location-hint-tooltip' => 'Hili ni eneo chaguomsingi la kufikia kwa operesheni zinazoundwa kwa mkono. Linaweza kubadilishwa baadaye, na njia zinaweza kuweka eneo chaguomsingi tofauti.',
                        ],
                    ],

                    'packages' => [
                        'title'  => 'Vifurushi',

                        'fields' => [
                            'show-entire-package'              => 'Hamisha kifurushi kizima',
                            'show-entire-package-hint-tooltip' => 'Ikichaguliwa, unaweza kuhamisha vifurushi vizima.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title'  => 'Vifaa',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title'  => 'Chapisha wakati wa kuthibitisha',

                        'fields' => [
                            'delivery-slip'              => 'Hati ya uwasilishaji',
                            'delivery-slip-hint-tooltip' => 'Ikichaguliwa, mfumo utachapisha hati ya uwasilishaji kiotomatiki uchukuaji unapothibitishwa.',

                            'return-slip'              => 'Hati ya marejesho',
                            'return-slip-hint-tooltip' => 'Ikichaguliwa, mfumo utachapisha hati ya marejesho kiotomatiki uchukuaji unapothibitishwa.',

                            'product-labels'              => 'Lebo za bidhaa',
                            'product-labels-hint-tooltip' => 'Ikichaguliwa, mfumo utachapisha lebo za bidhaa kiotomatiki uchukuaji unapothibitishwa.',

                            'lots-labels'              => 'Lebo za kundi/mfululizo',
                            'lots-labels-hint-tooltip' => 'Ikichaguliwa, mfumo utachapisha lebo za makundi/nambari za mfululizo kiotomatiki uchukuaji unapothibitishwa.',

                            'reception-report'              => 'Ripoti ya upokeaji',
                            'reception-report-hint-tooltip' => 'Ikichaguliwa, mfumo utachapisha ripoti ya upokeaji kiotomatiki uchukuaji unapothibitishwa na una mienendo iliyogawiwa.',

                            'reception-report-labels'              => 'Lebo za ripoti ya upokeaji',
                            'reception-report-labels-hint-tooltip' => 'Ikichaguliwa, mfumo utachapisha lebo za ripoti ya upokeaji kiotomatiki uchukuaji unapothibitishwa.',

                            'package-content'              => 'Yaliyomo kwenye kifurushi',
                            'package-content-hint-tooltip' => 'Ikichaguliwa, mfumo utachapisha maelezo ya vifurushi na yaliyomo kiotomatiki uchukuaji unapothibitishwa.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title'  => 'Chapisha kwa «Weka kwenye kifurushi»',

                        'fields' => [
                            'package-label'              => 'Lebo ya kifurushi',
                            'package-label-hint-tooltip' => 'Ikichaguliwa, mfumo utachapisha lebo ya kifurushi kiotomatiki kitufe cha «Weka kwenye kifurushi» kinapotumika.',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'warehouse'  => 'Ghala',
            'company'    => 'Kampuni',
            'deleted-at' => 'Ilifutwa',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'groups' => [
            'type'       => 'Aina',
            'warehouse'  => 'Ghala',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'filters' => [
            'type'      => 'Aina',
            'warehouse' => 'Ghala',
            'company'   => 'Kampuni',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aina ya operesheni imerejeshwa',
                    'body'  => 'Aina ya operesheni imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aina ya operesheni imefutwa',
                    'body'  => 'Aina ya operesheni imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Aina ya operesheni imefutwa kabisa',
                        'body'  => 'Aina ya operesheni imefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Aina ya operesheni haikuweza kufutwa',
                        'body'  => 'Aina ya operesheni haiwezi kufutwa kwa sababu inatumika.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Aina za operesheni zimerejeshwa',
                    'body'  => 'Aina za operesheni zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Aina za operesheni zimefutwa',
                    'body'  => 'Aina za operesheni zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Aina za operesheni zimefutwa kabisa',
                        'body'  => 'Aina za operesheni zimefutwa kabisa kwa mafanikio.',
                    ],

                    'error' => [
                        'title' => 'Aina za operesheni hazikuweza kufutwa',
                        'body'  => 'Aina za operesheni haziwezi kufutwa kwa sababu zinatumika.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'Unda aina ya operesheni',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Taarifa za jumla',

                'entries' => [
                    'name' => 'Jina',
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

        'tabs' => [
            'general' => [
                'title' => 'Jumla',

                'entries' => [
                    'type'                       => 'Aina ya operesheni',
                    'sequence_code'              => 'Msimbo wa mfuatano',
                    'print_label'                => 'Chapisha lebo',
                    'warehouse'                  => 'Ghala',
                    'reservation_method'         => 'Njia ya kutenga bidhaa',
                    'auto_show_reception_report' => 'Onyesha ripoti ya upokeaji kiotomatiki',
                    'company'                    => 'Kampuni',
                    'return_operation_type'      => 'Aina ya operesheni ya marejesho',
                    'create_backorder'           => 'Unda oda iliyosalia',
                    'move_type'                  => 'Aina ya uhamisho',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'Makundi',

                        'entries' => [
                            'use_create_lots'   => 'Unda makundi',
                            'use_existing_lots' => 'Tumia makundi yaliyopo',
                        ],
                    ],

                    'locations' => [
                        'title' => 'Maeneo',

                        'entries' => [
                            'source_location'      => 'Eneo la chanzo',
                            'destination_location' => 'Eneo la kufikia',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Vifaa',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'Chapisha wakati wa kuthibitisha',

                        'entries' => [
                            'auto_print_delivery_slip'           => 'Chapisha hati ya uwasilishaji kiotomatiki',
                            'auto_print_return_slip'             => 'Chapisha hati ya marejesho kiotomatiki',
                            'auto_print_product_labels'          => 'Chapisha lebo za bidhaa kiotomatiki',
                            'auto_print_lot_labels'              => 'Chapisha lebo za makundi kiotomatiki',
                            'auto_print_reception_report'        => 'Chapisha ripoti ya upokeaji kiotomatiki',
                            'auto_print_reception_report_labels' => 'Chapisha lebo za ripoti ya upokeaji kiotomatiki',
                            'auto_print_packages'                => 'Chapisha vifurushi kiotomatiki',
                        ],
                    ],

                    'print_on_pack' => [
                        'title' => 'Chapisha wakati wa kufunga',

                        'entries' => [
                            'auto_print_package_label' => 'Chapisha lebo ya kifurushi kiotomatiki',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
