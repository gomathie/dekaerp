<?php

return [
    'navigation' => [
        'title' => 'Work Centers',
        'group' => 'Usanidi',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'name'                     => 'Jina',
                    'name-placeholder'         => 'eg. Assembly Mstari 1',
                    'code'                     => 'Msimbo',
                    'code-placeholder'         => 'eg. AL1',
                    'working-state'            => 'Working Hali',
                    'color'                    => 'Rangi',
                    'tags'                     => 'Lebo',
                    'alternative-work-centers' => 'Alternative Work Centers',
                    'company'                  => 'Kampuni',
                    'calendar'                 => 'Working Saa',
                ],
            ],

            'information' => [
                'title'     => 'Jumla Taarifa',
                'fieldsets' => [
                    'production-information' => 'Uzalishaji Taarifa',
                    'costing-information'    => 'Costing Taarifa',
                ],
                'fields' => [
                    'default-capacity' => 'Chaguo-msingi Capacity',
                    'time-efficiency'  => 'Time Efficiency',
                    'oee-target'       => 'OEE Lengwa',
                    'costs-per-hour'   => 'Gharama kwa Saa',
                    'cost-suffix'      => 'kwa saa',
                    'setup-time'       => 'Setup Time',
                    'cleanup-time'     => 'Cleanup Time',
                    'time-suffix'      => 'dakika',
                ],
            ],

            'description' => [
                'title'  => 'Maelezo',
                'fields' => [
                    'note'             => 'Maelezo',
                    'note-placeholder' => 'Maelezo ya work center...',
                ],
            ],

            'specific-capacity' => [
                'title'  => 'Specific Capacity',
                'fields' => [
                    'records' => 'Specific Capacity',
                ],
                'columns' => [
                    'product'      => 'Bidhaa',
                    'product-uom'  => 'UOM',
                    'capacity'     => 'Capacity',
                    'setup-time'   => 'Setup Time',
                    'cleanup-time' => 'Cleanup Time',
                ],
                'actions' => [
                    'add' => 'Ongeza mstari',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'             => 'Jina',
            'code'             => 'Msimbo',
            'company'          => 'Kampuni',
            'calendar'         => 'Working Saa',
            'working-state'    => 'Working Hali',
            'default-capacity' => 'Capacity',
            'time-efficiency'  => 'Efficiency',
            'costs-per-hour'   => 'Gharama kwa Saa',
            'deleted-at'       => 'Imefutwa',
            'created-at'       => 'Imeundwa',
            'updated-at'       => 'Imesasishwa',
        ],

        'groups' => [
            'company' => 'Kampuni',
        ],

        'filters' => [
            'company'       => 'Kampuni',
            'working-state' => 'Working Hali',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Work center restored',
                    'body'  => 'work center ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Work center imehifadhiwa',
                    'body'  => 'work center ina imehifadhiwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Work center imefutwa',
                        'body'  => 'work center ina permanently imefutwa.',
                    ],

                    'error' => [
                        'title' => 'Work center could si kuwa imefutwa',
                        'body'  => 'work center haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Work centers restored',
                    'body'  => 'imechaguliwa work centers ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Work centers imehifadhiwa',
                    'body'  => 'imechaguliwa work centers ina imehifadhiwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Work centers imefutwa',
                        'body'  => 'imechaguliwa work centers ina permanently imefutwa.',
                    ],

                    'error' => [
                        'title' => 'Work centers could si kuwa imefutwa',
                        'body'  => 'One au zaidi imechaguliwa work centers ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'name'                     => 'Work Center Jina',
                    'code'                     => 'Msimbo',
                    'working-state'            => 'Working Hali',
                    'tags'                     => 'Lebo',
                    'alternative-work-centers' => 'Alternative Work Centers',
                    'company'                  => 'Kampuni',
                    'calendar'                 => 'Working Saa',
                ],
            ],

            'information' => [
                'title'     => 'Jumla Taarifa',
                'fieldsets' => [
                    'production-information' => 'Uzalishaji Taarifa',
                    'costing-information'    => 'Costing Taarifa',
                ],

                'entries' => [
                    'default-capacity' => 'Chaguo-msingi Capacity',
                    'time-efficiency'  => 'Time Efficiency',
                    'oee-target'       => 'OEE Lengwa',
                    'costs-per-hour'   => 'Gharama kwa Saa',
                    'cost-suffix'      => 'kwa work center',
                    'setup-time'       => 'Setup Time',
                    'cleanup-time'     => 'Cleanup Time',
                    'time-suffix'      => 'dakika',
                ],
            ],

            'description' => [
                'title'   => 'Maelezo',
                'entries' => [
                    'note' => 'Maelezo',
                ],
            ],

            'specific-capacity' => [
                'title'   => 'Specific Capacities',
                'columns' => [
                    'product'      => 'Bidhaa',
                    'product-uom'  => 'UOM',
                    'capacity'     => 'Capacity',
                    'setup-time'   => 'Setup Time',
                    'cleanup-time' => 'Cleanup Time',
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
    ],
];
