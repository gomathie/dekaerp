<?php

return [
    'title' => 'Mauzo Teams',

    'navigation' => [
        'title' => 'Mauzo Teams',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'Mauzo Timu',
                'status'   => 'Hali',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'Timu Maelezo',
                        'fields' => [
                            'team-leader'            => 'Timu Leader',
                            'company'                => 'Kampuni',
                            'invoiced-target'        => 'Invoiced Lengwa',
                            'invoiced-target-suffix' => '/ Mwezi',
                            'color'                  => 'Rangi',
                            'members'                => 'Wanachama',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'Kitambulisho',
            'company'         => 'Kampuni',
            'team-leader'     => 'Timu Leader',
            'name'            => 'Jina',
            'status'          => 'Hali',
            'invoiced-target' => 'Invoiced Lengwa',
            'color'           => 'Rangi',
            'created-by'      => 'Imeundwa Na',
            'created-at'      => 'Imeundwa',
            'updated-at'      => 'Imesasishwa',
        ],

        'filters' => [
            'name'        => 'Jina',
            'team-leader' => 'Timu Leader',
            'company'     => 'Kampuni',
            'created-by'  => 'Imeundwa Na',
            'updated-at'  => 'Imesasishwa',
            'created-at'  => 'Imeundwa',
        ],

        'groups' => [
            'name'        => 'Jina',
            'company'     => ' Kampuni',
            'team-leader' => 'Timu Leader',
            'created-at'  => 'Imeundwa',
            'updated-at'  => 'Imesasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mauzo Timu restored',
                    'body'  => 'mauzo Timu ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mauzo Timu imefutwa',
                    'body'  => 'mauzo Timu ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mauzo Timu force imefutwa',
                    'body'  => 'mauzo Timu ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Mauzo Teams restored',
                    'body'  => 'mauzo Teams ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mauzo Teams imefutwa',
                    'body'  => 'mauzo Teams ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Mauzo Teams force imefutwa',
                    'body'  => 'mauzo Teams ina force imefutwa successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Mauzo Teams imeundwa',
                    'body'  => 'mauzo Teams ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'Mauzo Timu',
                'status'   => 'Hali',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'Timu Maelezo',
                        'entries' => [
                            'team-leader'            => 'Timu Leader',
                            'company'                => 'Kampuni',
                            'invoiced-target'        => 'Invoiced Lengwa',
                            'invoiced-target-suffix' => '/ Mwezi',
                            'color'                  => 'Rangi',
                            'members'                => 'Wanachama',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
