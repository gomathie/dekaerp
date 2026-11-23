<?php

return [
    'navigation' => [
        'title' => 'Bidhaa',
        'group' => 'Ghala',
    ],

    'global-search' => [
        'partner' => 'Mshirika',
        'origin'  => 'Asili',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'receive-from'         => 'Receive Kutoka',
                    'contact'              => 'Mwasiliani',
                    'delivery-address'     => 'Usafirishaji Anwani',
                    'operation-type'       => 'Operation Aina',
                    'source-location'      => 'Chanzo Mahali',
                    'destination-location' => 'Unakoenda Mahali',
                ],
            ],

            'additional-fields' => [
                'title' => 'Additional Taarifa',
            ],
        ],

        'tabs' => [
            'operations' => [
                'title' => 'Shughuli',

                'columns' => [
                    'product'                    => 'Bidhaa',
                    'final-location'             => 'Final Mahali',
                    'description'                => 'Maelezo',
                    'scheduled-at'               => 'Scheduled Saa',
                    'deadline'                   => 'Deadline',
                    'packaging'                  => 'Kifungashio',
                    'demand'                     => 'Mahitaji',
                    'quantity'                   => 'Kiasi',
                    'insufficient-stock-tooltip' => 'Insufficient kiasi inapatikana',
                    'unit'                       => 'Uniti',
                    'picked'                     => 'Picked',
                ],

                'actions' => [
                    'open-product' => [
                        'tooltip' => 'Wazi bidhaa',
                    ],
                ],

                'fields' => [
                    'product'        => 'Bidhaa',
                    'final-location' => 'Final Mahali',
                    'description'    => 'Maelezo',
                    'scheduled-at'   => 'Scheduled Saa',
                    'deadline'       => 'Deadline',
                    'packaging'      => 'Kifungashio',
                    'demand'         => 'Mahitaji',
                    'quantity'       => 'Kiasi',
                    'unit'           => 'Uniti',
                    'picked'         => 'Picked',

                    'lines' => [
                        'modal-heading'             => 'Manage Hisa Moves',
                        'modal-submit-action-label' => 'Hifadhi',
                        'add-line'                  => 'Ongeza Mstari',

                        'actions' => [
                            'generate' => 'Generate Serials/Makundi',
                            'import'   => 'Ingiza Serials/Makundi',
                        ],

                        'fields' => [
                            'lot'                => 'Kundi/Nambari ya mfululizo Nambari',
                            'pick-from'          => 'Pick Kutoka',
                            'location'           => 'Store Hadi',
                            'package'            => 'Unakoenda Kifurushi',
                            'quantity'           => 'Kiasi',
                            'uom'                => 'Kipimo',
                            'first-lot'          => 'Kwanza Kundi Nambari',
                            'quantity-per-lot'   => 'Kiasi kwa Kundi',
                            'quantity-received'  => 'Kiasi Imepokelewa',
                            'keep-current-lines' => 'Weka sasa mistari',
                            'serials'            => 'Kundi/Nambari ya mfululizo Numbers',
                            'serials-helper'     => 'One kundi/nambari ya mfululizo nambari kwa mstari.',
                        ],
                    ],
                ],
            ],

            'additional' => [
                'title' => 'Additional',

                'fields' => [
                    'responsible'                  => 'Responsible',
                    'shipping-policy'              => 'Shipping Policy',
                    'shipping-policy-hint-tooltip' => 'Hii defines whether goods unapaswa kuwa delivered partially au zote saa mara moja.',
                    'scheduled-at'                 => 'Scheduled Saa',
                    'scheduled-at-hint-tooltip'    => 'scheduled time kwa processing kwanza part ya shehena. Kwa mkono mpangilio thamani hapa ita tumia hii kama inatarajiwa tarehe kwa zote hisa moves.',
                    'source-document'              => 'Chanzo Hati',
                    'source-document-hint-tooltip' => 'Kumbukumbu ya hati',
                ],
            ],

            'note' => [
                'title' => 'Maelezo',

                'fields' => [

                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'favorite'        => 'Favorite',
            'reference'       => 'Kumbukumbu',
            'from'            => 'Kutoka',
            'to'              => 'Hadi',
            'contact'         => 'Mwasiliani',
            'responsible'     => 'Responsible',
            'scheduled-at'    => 'Scheduled Saa',
            'deadline'        => 'Deadline',
            'closed-at'       => 'Imefungwa Saa',
            'source-document' => 'Chanzo Hati',
            'operation-type'  => 'Operation Aina',
            'company'         => 'Kampuni',
            'state'           => 'Hali',
            'deleted-at'      => 'Imefutwa',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'groups' => [
            'state'           => 'Hali',
            'source-document' => 'Chanzo Hati',
            'operation-type'  => 'Operation Aina',
            'scheduled-at'    => 'Ratiba Saa',
            'created-at'      => 'Imeundwa',
        ],

        'filters' => [
            'operation-type'       => 'Operation Aina',
            'name'                 => 'Jina',
            'state'                => 'Hali',
            'partner'              => 'Mshirika',
            'responsible'          => 'Responsible',
            'owner'                => 'Mmiliki',
            'source-location'      => 'Chanzo Mahali',
            'destination-location' => 'Unakoenda Mahali',
            'deadline'             => 'Deadline',
            'scheduled-at'         => 'Scheduled Saa',
            'closed-at'            => 'Imefungwa Saa',
            'created-at'           => 'Imeundwa',
            'updated-at'           => 'Imesasishwa',
            'company'              => 'Kampuni',
            'creator'              => 'Creator',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Jumla Taarifa',
                'entries' => [
                    'contact'              => 'Mwasiliani',
                    'operation-type'       => 'Operation Aina',
                    'source-location'      => 'Chanzo Mahali',
                    'destination-location' => 'Unakoenda Mahali',
                ],
            ],
        ],

        'tabs' => [
            'operations' => [
                'title'   => 'Shughuli',
                'entries' => [
                    'product'        => 'Bidhaa',
                    'final-location' => 'Final Mahali',
                    'description'    => 'Maelezo',
                    'scheduled-at'   => 'Scheduled Saa',
                    'deadline'       => 'Deadline',
                    'packaging'      => 'Kifungashio',
                    'demand'         => 'Mahitaji',
                    'quantity'       => 'Kiasi',
                    'unit'           => 'Uniti',
                    'picked'         => 'Picked',
                ],
            ],
            'additional' => [
                'title'   => 'Additional Taarifa',
                'entries' => [
                    'responsible'     => 'Responsible',
                    'shipping-policy' => 'Shipping Policy',
                    'scheduled-at'    => 'Scheduled Saa',
                    'source-document' => 'Chanzo Hati',
                ],
            ],
            'note' => [
                'title' => 'Maelezo',
            ],
        ],
    ],

    'tabs' => [
        'todo'        => 'Hadi',
        'my'          => 'Yangu Uhamisho',
        'starred'     => 'Starred',
        'draft'       => 'Rasimu',
        'waiting'     => 'Waiting',
        'ready'       => 'Ready',
        'late'        => 'Late',
        'done'        => 'Imekamilika',
        'canceled'    => 'Imeghairiwa',
        'back-orders' => 'Rudi Maagizo',
    ],

    'notifications' => [
        'uom-precision-warning' => [
            'title' => 'Kipimo Precision Onyo',
            'body'  => 'Wewe ni kwa kutumia kipimo smaller kuliko one inatumika hadi hisa hii bidhaa. Hii unaweza lead hadi rounding issues washa imehifadhiwa quantities. Consider kwa kutumia smallest uniti ya measure kwa hisa valuation, au reduce rounding precision ya yako base uniti.',
        ],
    ],
];
