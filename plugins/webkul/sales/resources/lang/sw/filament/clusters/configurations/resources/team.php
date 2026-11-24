<?php

return [
    'title' => 'Timu za mauzo',

    'navigation' => [
        'title' => 'Timu za mauzo',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'Timu ya mauzo',
                'status'   => 'Hali',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'Maelezo ya timu',
                        'fields' => [
                            'team-leader'            => 'Kiongozi wa timu',
                            'company'                => 'Kampuni',
                            'invoiced-target'        => 'Lengo la ankara',
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
            'id'              => 'ID',
            'company'         => 'Kampuni',
            'team-leader'     => 'Kiongozi wa timu',
            'name'            => 'Jina',
            'status'          => 'Hali',
            'invoiced-target' => 'Lengo la ankara',
            'color'           => 'Rangi',
            'created-by'      => 'Iliundwa na',
            'created-at'      => 'Iliundwa',
            'updated-at'      => 'Ilisasishwa',
        ],

        'filters' => [
            'name'        => 'Jina',
            'team-leader' => 'Kiongozi wa timu',
            'company'     => 'Kampuni',
            'created-by'  => 'Iliundwa na',
            'updated-at'  => 'Ilisasishwa',
            'created-at'  => 'Iliundwa',
        ],

        'groups' => [
            'name'        => 'Jina',
            'company'     => ' Kampuni',
            'team-leader' => 'Kiongozi wa timu',
            'created-at'  => 'Iliundwa',
            'updated-at'  => 'Ilisasishwa',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Timu ya mauzo imerejeshwa',
                    'body'  => 'Timu ya mauzo imerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timu ya mauzo imefutwa',
                    'body'  => 'Timu ya mauzo imefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Timu ya mauzo imefutwa kabisa',
                    'body'  => 'Timu ya mauzo imefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Timu za mauzo zimerejeshwa',
                    'body'  => 'Timu za mauzo zimerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Timu za mauzo zimefutwa',
                    'body'  => 'Timu za mauzo zimefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Timu za mauzo zimefutwa kabisa',
                    'body'  => 'Timu za mauzo zimefutwa kabisa kwa mafanikio.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Timu za mauzo zimeundwa',
                    'body'  => 'Timu za mauzo zimeundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'Timu ya mauzo',
                'status'   => 'Hali',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'Maelezo ya timu',
                        'entries' => [
                            'team-leader'            => 'Kiongozi wa timu',
                            'company'                => 'Kampuni',
                            'invoiced-target'        => 'Lengo la ankara',
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
