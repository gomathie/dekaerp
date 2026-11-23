<?php

return [
    'title' => 'Pay',

    'paid-now' => [
        'label'   => 'Imelipwa sasa',
        'helper'  => 'Saving confirms ankara na rekodi kamili malipo.',
        'invalid' => 'Hii way hadi pay haiwezekani kuwa inatumika kwa hii ankara.',
    ],

    'form' => [
        'more-options' => 'Zaidi options',
        'fields'       => [
            'paid-how'               => 'Jinsi ilikuwa hii imelipwa?',
            'new-way'                => 'Mpya way hadi pay (e.g. Wave, Card)',
            'journal'                => 'Jarida',
            'amount'                 => 'Kiasi',
            'currency'               => 'Sarafu',
            'payment-method-line'    => 'Malipo Method Mstari',
            'payment-date'           => 'Malipo Tarehe',
            'recipient-bank-account' => 'Mpokeaji Akaunti ya benki',
            'communication'          => 'Memo',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title'      => 'Malipo Imeshindwa',
            'plain-body' => 'Hii malipo could si kuwa prepared. Yetu timu ina notified. Tafadhali jaribu tena katika moment, au mwasiliani support.',
        ],
    ],
];
