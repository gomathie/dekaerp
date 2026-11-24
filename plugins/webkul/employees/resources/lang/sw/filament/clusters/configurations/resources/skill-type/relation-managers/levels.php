<?php

return [
    'form' => [
        'name'          => 'Jina',
        'level'         => 'Kiwango',
        'default-level' => 'Kiwango chaguo-msingi',
    ],

    'table' => [
        'columns' => [
            'name'          => 'Jina',
            'level'         => 'Kiwango',
            'default-level' => 'Kiwango chaguo-msingi',
            'created-at'    => 'Imeundwa',
            'updated-at'    => 'Imesasishwa',
        ],

        'groups' => [
            'created-at' => 'Imeundwa',
        ],

        'filters' => [
            'deleted-records' => 'Rekodi zilizofutwa',
        ],

        'actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Kiwango cha ujuzi kimeundwa',
                    'body'  => 'Kiwango cha ujuzi kimeundwa kikamilifu.',
                ],
            ],

            'edit' => [
                'notification' => [
                    'title' => 'Kiwango cha ujuzi kimesasishwa',
                    'body'  => 'Kiwango cha ujuzi kimesasishwa kikamilifu.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Kiwango cha ujuzi kimerejeshwa',
                    'body'  => 'Kiwango cha ujuzi kimerejeshwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kiwango cha ujuzi kimefutwa',
                    'body'  => 'Kiwango cha ujuzi kimefutwa kikamilifu.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Viwango vya ujuzi vimefutwa',
                    'body'  => 'Viwango vya ujuzi vimefutwa kikamilifu.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Viwango vya ujuzi vimefutwa kabisa',
                    'body'  => 'Viwango vya ujuzi vimefutwa kabisa.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Viwango vya ujuzi vimerejeshwa',
                    'body'  => 'Viwango vya ujuzi vimerejeshwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'          => 'Jina',
            'level'         => 'Kiwango',
            'default-level' => 'Kiwango chaguo-msingi',
        ],
    ],
];
