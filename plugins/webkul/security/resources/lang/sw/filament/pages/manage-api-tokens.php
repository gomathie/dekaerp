<?php

return [
    'navigation' => [
        'label' => 'Tokeni za API',
    ],

    'group'      => 'Jumla',
    'breadcrumb' => 'Tokeni za API',
    'title'      => 'Tokeni za API',
    'subheading' => 'Tokeni ambazo miunganisho ya wateja hutumia kujithibitisha kwenye API. Tokeni hubeba majukumu na kampuni zinazoruhusiwa za mtumiaji anayeimiliki.',

    'abilities' => [
        'read'  => 'Kusoma tu',
        'write' => 'Kuandika',
        'all'   => 'Ufikiaji kamili',
    ],

    'table' => [
        'columns' => [
            'name'      => 'Lebo',
            'user'      => 'Inatenda kama',
            'abilities' => 'Wigo',
            'last-used' => 'Ilitumika mwisho',
            'expires'   => 'Inaisha',
            'created'   => 'Ilitolewa',
        ],

        'never-used' => 'Haijawahi kutumika',
    ],

    'actions' => [
        'issue' => [
            'label'  => 'Toa tokeni',
            'submit' => 'Toa tokeni',

            'fields' => [
                'user'             => 'Inatenda kama mtumiaji',
                'user-helper'      => 'Tokeni inaweza kufanya kile tu mtumiaji huyu anachoweza kufanya. Tumia mtumiaji maalum kwa kila mteja, kamwe si msimamizi.',
                'name'             => 'Lebo',
                'name-helper'      => 'Jinsi utakavyoitambua tokeni hii baadaye, k.m. acme-warehouse-sync.',
                'abilities'        => 'Wigo',
                'abilities-helper' => 'Kusoma tu hakuruhusu POST, PATCH wala DELETE. Ufikiaji kamili hupuuza kabisa ukaguzi wa wigo.',
            ],
        ],

        'revoke' => [
            'label'       => 'Batilisha',
            'heading'     => 'Batilisha tokeni hii?',
            'description' => 'Muunganisho unaoitumia utaacha kufanya kazi mara moja. Hili haliwezi kutenduliwa.',
            'bulk'        => 'Batilisha zilizochaguliwa',
        ],
    ],

    'notifications' => [
        'issued' => [
            'title' => 'Tokeni imetolewa',
            'body'  => 'Inakili sasa - haitaonyeshwa tena.',
        ],

        'revoked'      => 'Tokeni imebatilishwa',
        'missing-user' => 'Mtumiaji huyo hayupo tena.',
    ],

    'plaintext' => [
        'title'       => 'Nakili tokeni hii sasa',
        'description' => 'Huhifadhiwa kama hash pekee, kwa hivyo hii ndiyo mara pekee inayoweza kuonyeshwa. Mtumie mteja kupitia njia unayoiamini.',
    ],

    'empty' => [
        'heading'     => 'Bado hakuna tokeni za API',
        'description' => 'Toa moja ili muunganisho wa mteja ujithibitishe kwenye API.',
    ],
];
