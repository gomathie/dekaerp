<?php

return [
    'navigation' => [
        'title' => 'Maghala',
        'group' => 'Ghala Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',
                'fields' => [
                    'name'                    => 'Jina',
                    'name-placeholder'        => 'eg. Central Ghala',
                    'code'                    => 'Short Jina',
                    'code-placeholder'        => 'eg. CW',
                    'code-hint-tooltip'       => 'short jina serves kama identifier kwa ghala.',
                    'company'                 => 'Kampuni',
                    'multi-warehouse-warning' => 'Creating mpya ghala ita kiotomatiki activate Storage Maeneo mpangilio.',
                    'address'                 => 'Anwani',
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',

                'fields' => [
                    'shipment-management'              => 'Shehena Management',
                    'incoming-shipments'               => 'Incoming Shehena',
                    'incoming-shipments-hint-tooltip'  => 'Chaguo-msingi incoming njia hadi follow',
                    'outgoing-shipments'               => 'Outgoing Shehena',
                    'outgoing-shipments-hint-tooltip'  => 'Chaguo-msingi outgoing njia hadi follow',
                    'manufacture'                      => 'Uzalishaji',
                    'manufacture-hint-tooltip'         => 'Chaguo-msingi uzalishaji njia hadi follow',
                    'resupply-management'              => 'Resupply Management',
                    'resupply-management-hint-tooltip' => 'Njia ita kuwa kiotomatiki generated hadi resupply hii ghala kutoka imechaguliwa maghala.',
                    'resupply-from'                    => 'Resupply Kutoka',
                ],
            ],

            'additional' => [
                'title'  => 'Additional Taarifa',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'code'       => 'Short Jina',
            'company'    => 'Kampuni',
            'address'    => 'Anwani',
            'deleted-at' => 'imefutwa',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'address'       => 'Anwani',
            'company'       => 'Kampuni',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'filters' => [
            'company' => 'Kampuni',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Ghala restored',
                    'body'  => 'ghala ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Ghala imefutwa',
                    'body'  => 'ghala ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Ghala force imefutwa',
                        'body'  => 'ghala ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Ghala could si kuwa imefutwa',
                        'body'  => 'ghala haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Maghala restored',
                    'body'  => 'maghala ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Maghala imefutwa',
                    'body'  => 'maghala ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Maghala force imefutwa',
                        'body'  => 'maghala ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Maghala could si kuwa imefutwa',
                        'body'  => 'maghala haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
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
                    'name'    => 'Ghala Jina',
                    'code'    => 'Ghala Msimbo',
                    'company' => 'Kampuni',
                    'address' => 'Anwani',
                ],
            ],

            'settings' => [
                'title' => 'Mipangilio',

                'entries' => [
                    'shipment-management' => 'Shehena Management',
                    'incoming-shipments'  => 'Incoming Shehena',
                    'outgoing-shipments'  => 'Outgoing Shehena',
                    'resupply-management' => 'Resupply Management',
                    'resupply-from'       => 'Resupply Kutoka',
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
