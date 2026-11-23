<?php

return [
    'navigation' => [
        'title' => 'Operation Types',
        'group' => 'Ghala Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'fields' => [
                    'operator-type'             => 'Operation Aina',
                    'operator-type-placeholder' => 'eg. Receptions',
                ],
            ],

            'applicable-on' => [
                'title'       => 'Applicable Washa',
                'description' => 'Chagua places wapi hii njia unaweza kuwa imechaguliwa.',

                'fields' => [
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'operator-type'                      => 'Operation Aina',
                    'sequence-prefix'                    => 'Mfuatano Kiambishi',
                    'generate-shipping-labels'           => 'Generate Shipping Labels',
                    'warehouse'                          => 'Ghala',
                    'show-reception-report'              => 'Onyesha Reception Ripoti saa Validation',
                    'show-reception-report-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki display reception ripoti upon validation, provided ni moves hadi allocate.',
                    'company'                            => 'Kampuni',
                    'return-type'                        => 'Return Aina',
                    'create-backorder'                   => 'Unda Backorder',
                    'move-type'                          => 'Hamisha Aina',
                    'move-type-hint-tooltip'             => 'Unless defined na chanzo hati, hii ita serve kama chaguo-msingi picking policy kwa hii operation aina.',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title'  => 'Makundi/Nambari ya mfululizo Numbers',

                        'fields' => [
                            'create-new'                => 'Unda Mpya',
                            'create-new-hint-tooltip'   => 'Ikiwa imechaguliwa, mfumo ita assume wewe intend hadi unda mpya Makundi/Nambari ya mfululizo Numbers, allowing wewe hadi ingiza wao katika text sehemu.',
                            'use-existing'              => 'Tumia Existing',
                            'use-existing-hint-tooltip' => 'Ikiwa imechaguliwa, wewe unaweza chagua Makundi/Nambari ya mfululizo Numbers au opt si hadi kabidhi yoyote. Hii allows hisa hadi kuwa imeundwa bila kundi au bila restrictions washa kundi inatumika.',
                        ],
                    ],

                    'locations' => [
                        'title'  => 'Maeneo',

                        'fields' => [
                            'source-location'                   => 'Chanzo Mahali',
                            'source-location-hint-tooltip'      => 'Hii serves kama chaguo-msingi chanzo mahali wakati kwa mkono creating hii operation. However, hii unaweza kuwa imebadilishwa later, na njia mei kabidhi different chaguo-msingi mahali.',
                            'destination-location'              => 'Unakoenda Mahali',
                            'destination-location-hint-tooltip' => 'Hii ni chaguo-msingi unakoenda mahali kwa kwa mkono imeundwa shughuli. However, hii unaweza kuwa modified later, na njia mei kabidhi different chaguo-msingi mahali.',
                        ],
                    ],

                    'packages' => [
                        'title'  => 'Vifurushi',

                        'fields' => [
                            'show-entire-package'              => 'Hamisha Entire Kifurushi',
                            'show-entire-package-hint-tooltip' => 'Ikiwa imechaguliwa, wewe unaweza hamisha entire vifurushi.',
                        ],
                    ],
                ],
            ],

            'hardware' => [
                'title'  => 'Hardware',

                'fieldsets' => [
                    'print-on-validation' => [
                        'title'  => 'Chapisha washa Validation',

                        'fields' => [
                            'delivery-slip'              => 'Usafirishaji Slip',
                            'delivery-slip-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki chapisha usafirishaji slip wakati picking ni validated.',

                            'return-slip'              => 'Return Slip',
                            'return-slip-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki chapisha return slip wakati picking ni validated.',

                            'product-labels'              => 'Bidhaa Labels',
                            'product-labels-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki chapisha bidhaa labels wakati picking ni validated.',

                            'lots-labels'              => 'Kundi/SN Labels',
                            'lots-labels-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki chapisha kundi/nambari ya mfululizo nambari labels wakati picking ni validated.',

                            'reception-report'              => 'Reception Ripoti',
                            'reception-report-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki chapisha reception ripoti wakati picking ni validated na contains amekabidhiwa moves.',

                            'reception-report-labels'              => 'Reception Ripoti Labels',
                            'reception-report-labels-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki chapisha reception ripoti labels wakati picking ni validated.',

                            'package-content'              => 'Kifurushi Content',
                            'package-content-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki chapisha kifurushi maelezo na yao contents wakati picking ni validated.',
                        ],
                    ],

                    'print-on-pack' => [
                        'title'  => 'Chapisha washa "Put katika Pack"',

                        'fields' => [
                            'package-label'              => 'Kifurushi Lebo',
                            'package-label-hint-tooltip' => 'Ikiwa imechaguliwa, mfumo ita kiotomatiki chapisha kifurushi lebo wakati "Put katika Pack" button ni inatumika.',
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
            'deleted-at' => 'Imefutwa',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'type'       => 'Aina',
            'warehouse'  => 'Ghala',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'filters' => [
            'type'      => 'Aina',
            'warehouse' => 'Ghala',
            'company'   => 'Kampuni',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operation Aina restored',
                    'body'  => 'operation aina ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Operation Aina imefutwa',
                    'body'  => 'operation aina ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Operation Aina force imefutwa',
                        'body'  => 'operation aina ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Operation Aina could si kuwa imefutwa',
                        'body'  => 'operation aina haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Operation Types restored',
                    'body'  => 'operation types ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Operation Types imefutwa',
                    'body'  => 'operation types ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Shughuli Types force imefutwa',
                        'body'  => 'operation types ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Shughuli Types could si kuwa imefutwa',
                        'body'  => 'operation types haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'empty-actions' => [
            'create' => [
                'label' => 'Unda Operation Aina',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'name' => 'Jina',
                ],
            ],

            'record-information' => [
                'title' => 'Rekodi Taarifa',

                'entries' => [
                    'created-by'   => 'Imeundwa Na',
                    'created-at'   => 'Imeundwa',
                    'last-updated' => 'Mwisho Imesasishwa',
                ],
            ],
        ],

        'tabs' => [
            'general' => [
                'title' => 'Jumla',

                'entries' => [
                    'type'                       => 'Operation Aina',
                    'sequence_code'              => 'Mfuatano Msimbo',
                    'print_label'                => 'Chapisha Lebo',
                    'warehouse'                  => 'Ghala',
                    'reservation_method'         => 'Reservation Method',
                    'auto_show_reception_report' => 'Auto Onyesha Reception Ripoti',
                    'company'                    => 'Kampuni',
                    'return_operation_type'      => 'Return Operation Aina',
                    'create_backorder'           => 'Unda Backorder',
                    'move_type'                  => 'Hamisha Aina',
                ],

                'fieldsets' => [
                    'lots' => [
                        'title' => 'Makundi',

                        'entries' => [
                            'use_create_lots'   => 'Tumia Unda Makundi',
                            'use_existing_lots' => 'Tumia Existing Makundi',
                        ],
                    ],

                    'locations' => [
                        'title' => 'Maeneo',

                        'entries' => [
                            'source_location'      => 'Chanzo Mahali',
                            'destination_location' => 'Unakoenda Mahali',
                        ],
                    ],
                ],
            ],
            'hardware' => [
                'title' => 'Hardware',

                'fieldsets' => [
                    'print_on_validation' => [
                        'title' => 'Chapisha washa Validation',

                        'entries' => [
                            'auto_print_delivery_slip'           => 'Auto Chapisha Usafirishaji Slip',
                            'auto_print_return_slip'             => 'Auto Chapisha Return Slip',
                            'auto_print_product_labels'          => 'Auto Chapisha Bidhaa Labels',
                            'auto_print_lot_labels'              => 'Auto Chapisha Kundi Labels',
                            'auto_print_reception_report'        => 'Auto Chapisha Reception Ripoti',
                            'auto_print_reception_report_labels' => 'Auto Chapisha Reception Ripoti Labels',
                            'auto_print_packages'                => 'Auto Chapisha Vifurushi',
                        ],
                    ],

                    'print_on_pack' => [
                        'title' => 'Chapisha washa Pack',

                        'entries' => [
                            'auto_print_package_label' => 'Auto Chapisha Kifurushi Lebo',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
