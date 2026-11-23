<?php

return [
    'navigation' => [
        'label' => 'API Tokens',
    ],

    'group'      => 'Jumla',
    'breadcrumb' => 'API Tokens',
    'title'      => 'API Tokens',
    'subheading' => 'Tokens client integrations tumia hadi authenticate dhidi ya API. token carries majukumu na inaruhusiwa makampuni ya mtumiaji hii belongs hadi.',

    'abilities' => [
        'read'  => 'Kusoma tu',
        'write' => 'Kuandika',
        'all'   => 'Kamili access',
    ],

    'table' => [
        'columns' => [
            'name'      => 'Lebo',
            'user'      => 'Acts kama',
            'abilities' => 'Scopes',
            'last-used' => 'Mwisho inatumika',
            'expires'   => 'Expires',
            'created'   => 'Issued',
        ],

        'never-used' => 'Kamwe inatumika',
    ],

    'actions' => [
        'issue' => [
            'label'  => 'Issue token',
            'submit' => 'Issue token',

            'fields' => [
                'user'             => 'Acts kama mtumiaji',
                'user-helper'      => 'token unaweza exactly nini hii mtumiaji unaweza. Tumia dedicated mtumiaji kwa client, kamwe msimamizi.',
                'name'             => 'Lebo',
                'name-helper'      => 'Jinsi wewe ita recognise hii token later, e.g. acme-ghala-sync.',
                'abilities'        => 'Scopes',
                'abilities-helper' => 'Kusoma tu haiwezekani POST, PATCH au Futa. Kamili access ignores scope checks entirely.',
            ],
        ],

        'revoke' => [
            'label'       => 'Revoke',
            'heading'     => 'Revoke hii token?',
            'description' => 'integration kwa kutumia hii ita kituo working immediately. Hii haiwezekani kuwa undone.',
            'bulk'        => 'Revoke imechaguliwa',
        ],
    ],

    'notifications' => [
        'issued' => [
            'title' => 'Token issued',
            'body'  => 'Nakili hii sasa - hii haiwezekani kuwa shown tena.',
        ],

        'revoked'      => 'Token revoked',
        'missing-user' => 'Hiyo mtumiaji hakuna longer ipo.',
    ],

    'plaintext' => [
        'title'       => 'Nakili hii token sasa',
        'description' => 'Hii ni stored tu kama hash, hivyo hii ni one time hii unaweza kuwa displayed. Send hii hadi client zaidi ya channel wewe trust.',
    ],

    'empty' => [
        'heading'     => 'Hakuna API tokens bado',
        'description' => 'Issue one hadi ruhusu client integration authenticate dhidi ya API.',
    ],
];
