<?php

return [
    'navigation' => [
        'title' => 'Kategoria za vipimo',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Jumla',

                'fields' => [
                    'name' => 'Jina',
                ],
            ],

            'uoms' => [
                'title' => 'Vipimo',

                'fields' => [
                    'uoms'     => 'Vipimo',
                    'type'     => 'Aina',
                    'name'     => 'Kipimo',
                    'ratio'    => 'Uwiano',
                    'rounding' => 'Usahihi wa ukadiriaji',
                ],

                'validations' => [
                    'missing-reference'          => 'Kategoria hii inapaswa kuwa na kipimo cha rejea.',
                    'multiple-references'        => 'Kategoria hii inapaswa kuwa na kipimo kimoja tu cha rejea.',
                    'ratio-greater-than-zero'    => 'Uwiano wa ubadilishaji wa kipimo hauwezi kuwa sifuri.',
                    'rounding-greater-than-zero' => 'Usahihi wa ukadiriaji lazima uwe zaidi ya sifuri.',
                ],

                'actions' => [
                    'add' => 'Ongeza kipimo',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'uoms'       => 'Vipimo',
            'created-at' => 'Iliundwa',
            'updated-at' => 'Ilisasishwa',
        ],

        'groups' => [
            'created-at' => 'Iliundwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kategoria ya vipimo imesasishwa',
                    'body'  => 'Kategoria ya vipimo imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kategoria ya vipimo imefutwa',
                    'body'  => 'Kategoria ya vipimo imefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kategoria za vipimo zimefutwa',
                    'body'  => 'Kategoria za vipimo zimefutwa kikamilifu.',
                ],
            ],
        ],
    ],
];
