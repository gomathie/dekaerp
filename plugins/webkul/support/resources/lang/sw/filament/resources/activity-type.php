<?php

return [
    'title' => 'Idara',

    'navigation' => [
        'title' => 'Idara',
        'group' => 'Wafanyakazi',
    ],

    'form' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Jumla Taarifa',

                'fields' => [
                    'name'                => 'Shughuli Aina',
                    'name-tooltip'        => 'Ingiza official shughuli aina jina',
                    'action'              => 'Kitendo',
                    'default-user'        => 'Chaguo-msingi Mtumiaji',
                    'summary'             => 'Muhtasari',
                    'note'                => 'Maelezo',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Taarifa',

                'fields' => [
                    'delay-count'            => 'Delay Idadi',
                    'delay-unit'             => 'Delay Uniti',
                    'delay-form'             => 'Delay Fomu',
                    'delay-form-helper-text' => 'Chanzo ya delay calculation',
                ],
            ],

            'advanced-information' => [
                'title' => 'Advanced Taarifa',

                'fields' => [
                    'icon'                => 'Ikon',
                    'decoration-type'     => 'Decoration Aina',
                    'chaining-type'       => 'Chaining Aina',
                    'suggest'             => 'Suggest',
                    'trigger'             => 'Trigger',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Hali & Usanidi',

                'fields' => [
                    'status'               => 'Hali',
                    'keep-done-activities' => 'Weka Imekamilika Shughuli',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Shughuli Aina',
            'summary'    => 'Muhtasari',
            'planned-in' => 'Imepangwa Katika',
            'type'       => 'Aina',
            'action'     => 'Kitendo',
            'status'     => 'Hali',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'groups' => [
            'name'             => 'Jina',
            'action-category'  => 'Kitendo Kategoria',
            'status'           => 'Hali',
            'delay-count'      => 'Delay Idadi',
            'delay-unit'       => 'Delay Uniti',
            'delay-source'     => 'Delay Chanzo',
            'associated-model' => 'Associated Model',
            'chaining-type'    => 'Chaining Aina',
            'decoration-type'  => 'Decoration Aina',
            'default-user'     => 'Chaguo-msingi Mtumiaji',
            'creation-date'    => 'Creation Tarehe',
            'last-update'      => 'Mwisho Sasisha',
        ],

        'filters' => [
            'action'    => 'Kitendo',
            'status'    => 'Hali',
            'has-delay' => 'Ina Delay',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Shughuli aina restored',
                    'body'  => 'shughuli aina ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Shughuli aina imefutwa',
                    'body'  => 'shughuli aina ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Shughuli aina force imefutwa',
                        'body'  => 'Shughuli aina ina force imefutwa successfully.',
                    ],
                    'error' => [
                        'title' => 'Shughuli aina could si kuwa imefutwa',
                        'body'  => 'Shughuli aina haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Shughuli types restored',
                    'body'  => 'shughuli types ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Shughuli types imefutwa',
                    'body'  => 'shughuli types ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Shughuli types force imefutwa',
                    'body'  => 'shughuli types ina force imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'activity-type-details' => [
                'title' => 'Jumla Taarifa',

                'entries' => [
                    'name'                => 'Shughuli Aina',
                    'name-tooltip'        => 'Ingiza official shughuli aina jina',
                    'action'              => 'Kitendo',
                    'default-user'        => 'Chaguo-msingi Mtumiaji',
                    'plugin'              => 'Programu-jalizi',
                    'summary'             => 'Muhtasari',
                    'note'                => 'Maelezo',
                ],
            ],

            'delay-information' => [
                'title' => 'Delay Taarifa',

                'entries' => [
                    'delay-count'            => 'Delay Idadi',
                    'delay-unit'             => 'Delay Uniti',
                    'delay-form'             => 'Delay Fomu',
                    'delay-form-helper-text' => 'Chanzo ya delay calculation',
                ],
            ],

            'advanced-information' => [
                'title' => 'Advanced Taarifa',

                'entries' => [
                    'icon'                => 'Ikon',
                    'decoration-type'     => 'Decoration Aina',
                    'chaining-type'       => 'Chaining Aina',
                    'suggest'             => 'Suggest',
                    'trigger'             => 'Trigger',
                ],
            ],

            'status-and-configuration-information' => [
                'title' => 'Hali & Usanidi',

                'entries' => [
                    'status'               => 'Hali',
                    'keep-done-activities' => 'Weka Imekamilika Shughuli',
                ],
            ],
        ],
    ],
];
