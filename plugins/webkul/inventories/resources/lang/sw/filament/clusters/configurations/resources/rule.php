<?php

return [
    'navigation' => [
        'title' => 'Rules',
        'group' => 'Ghala Management',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Jumla',

                'fields' => [
                    'name'                        => 'Jina',
                    'action'                      => 'Kitendo',
                    'operation-type'              => 'Operation Aina',
                    'source-location'             => 'Chanzo Mahali',
                    'destination-location'        => 'Unakoenda Mahali',
                    'supply-method'               => 'Supply Method',
                    'supply-method-hint-tooltip'  => 'Take Kutoka Hisa: Bidhaa ni sourced directly kutoka inapatikana hisa katika chanzo mahali.<br/>Trigger Another Rule: mfumo ignores inapatikana hisa na searches kwa hisa rule hadi replenish chanzo mahali.<br/>Take Kutoka Hisa, ikiwa Haipatikani, Trigger Another Rule: Bidhaa ni kwanza taken kutoka inapatikana hisa. Ikiwa hakuna ni inapatikana, mfumo inatumika hisa rule hadi bring bidhaa ndani chanzo mahali.',
                    'automatic-move'              => 'Kiotomatiki Hamisha',
                    'automatic-move-hint-tooltip' => 'Kwa mkono Operation: Creates separate hisa hamisha baada ya sasa one.<br/>Kiotomatiki Hakuna Step Added: Directly replaces mahali katika original hamisha bila adding extra step.',

                    'action-information' => [
                        'pull'        => 'Wakati bidhaa ni inahitajika katika <b>:sourceLocation</b>, :operation ni generated kutoka <b>:destinationLocation</b> hadi meet mahitaji.',
                        'push'        => 'Wakati bidhaa reach <b>:sourceLocation</b>,</br><b>:operation</b> ni generated hadi uhamisho wao hadi <b>:destinationLocation</b>.',
                        'buy'         => 'Wakati bidhaa ni needed katika <b>:destinationLocation</b>, request kwa nukuu ni imeundwa hadi fulfill need.',
                        'manufacture' => 'Wakati bidhaa ni needed katika <b>:destinationLocation</b>, uzalishaji agizo ni imeundwa hadi fulfill need.',
                    ],
                ],
            ],

            'settings' => [
                'title'  => 'Mipangilio',

                'fields' => [
                    'partner-address'              => 'Mshirika Anwani',
                    'partner-address-hint-tooltip' => 'Anwani wapi goods unapaswa kuwa delivered. Si lazima.',
                    'lead-time'                    => 'Lead Time (Siku)',
                    'lead-time-hint-tooltip'       => 'inatarajiwa uhamisho tarehe ita kuwa calculated kwa kutumia hii lead time.',
                ],

                'fieldsets' => [
                    'applicability' => [
                        'title'  => 'Applicability',

                        'fields' => [
                            'route'   => 'Njia',
                            'company' => 'Kampuni',
                        ],
                    ],

                    'propagation' => [
                        'title'  => 'Propagation',

                        'fields' => [
                            'propagation-procurement-group'              => 'Propagation ya Procurement Kundi',
                            'propagation-procurement-group-hint-tooltip' => 'Ikiwa imechaguliwa, canceling hamisha imeundwa na hii rule ita pia ghairi subsequent hamisha.',
                            'cancel-next-move'                           => 'Ghairi Ifuatayo Hamisha',
                            'warehouse-to-propagate'                     => 'Ghala hadi Propagate',
                            'warehouse-to-propagate-hint-tooltip'        => 'ghala amekabidhiwa hadi imeundwa hamisha au procurement, ambayo mei differ kutoka ghala hii rule inatumika hadi (e.g., kwa resupply rules kutoka another ghala).',
                        ],
                    ],
                ],

            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Jina',
            'action'               => 'Kitendo',
            'source-location'      => 'Chanzo Mahali',
            'destination-location' => 'Unakoenda Mahali',
            'route'                => 'Njia',
            'deleted-at'           => 'Imefutwa',
            'created-at'           => 'Imeundwa',
            'updated-at'           => 'Imesasishwa',
        ],

        'groups' => [
            'action'               => 'Kitendo',
            'source-location'      => 'Chanzo Mahali',
            'destination-location' => 'Unakoenda Mahali',
            'route'                => 'Njia',
            'created-at'           => 'Imeundwa',
            'updated-at'           => 'Imesasishwa',
        ],

        'filters' => [
            'action'               => 'Kitendo',
            'source-location'      => 'Chanzo Mahali',
            'destination-location' => 'Unakoenda Mahali',
            'route'                => 'Njia',
            'company'              => 'Kampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Rule imesasishwa',
                    'body'  => 'rule ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Rule restored',
                    'body'  => 'rule ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rule imefutwa',
                    'body'  => 'rule ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rule force imefutwa',
                        'body'  => 'rule ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Rule could si kuwa imefutwa',
                        'body'  => 'rule haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Rules restored',
                    'body'  => 'rules ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Rules imefutwa',
                    'body'  => 'rules ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Rules force imefutwa',
                        'body'  => 'rules ina force imefutwa successfully.',
                    ],

                    'error' => [
                        'title' => 'Rules could si kuwa imefutwa',
                        'body'  => 'rules haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Rule Maelezo',

                'description' => [
                    'pull' => 'Wakati bidhaa ni inahitajika katika <b>:sourceLocation</b>, <b>:operation</b> ni generated kutoka <b>:destinationLocation</b> hadi meet mahitaji.',
                    'push' => 'Wakati bidhaa reach katika <b>:sourceLocation</b>, <b>:operation</b> ni generated hadi uhamisho wao hadi <b>:destinationLocation</b>.',
                ],

                'entries' => [
                    'name'                 => 'Rule Jina',
                    'action'               => 'Kitendo',
                    'operation-type'       => 'Operation Aina',
                    'source-location'      => 'Chanzo Mahali',
                    'destination-location' => 'Unakoenda Mahali',
                    'route'                => 'Njia',
                    'company'              => 'Kampuni',
                    'partner-address'      => 'Mshirika Anwani',
                    'lead-time'            => 'Lead Time',
                    'action-information'   => 'Kitendo Taarifa',
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
