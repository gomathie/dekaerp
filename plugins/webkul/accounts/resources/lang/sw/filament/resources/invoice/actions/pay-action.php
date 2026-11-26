<?php

return [
    'title' => 'Rekodi malipo',

    'paid-now' => [
        'label'       => 'Imelipwa sasa',
        'helper'      => 'Kuhifadhi kunathibitisha ankara na kurekodi malipo kamili.',
        'invalid'     => 'Njia hii ya kulipa haiwezi kutumika kwa ankara hii.',
        'not-allowed' => 'Huna ruhusa ya kurekodi malipo.',
    ],

    'form' => [
        'more-options' => 'Chaguo zaidi',
        'fields'       => [
            'paid-how'               => 'Ililipwa vipi?',
            'new-way'                => 'Njia mpya ya kulipa (k.m. Wave, kadi)',
            'journal'                => 'Jarida',
            'amount'                 => 'Kiasi',
            'currency'               => 'Sarafu',
            'payment-method-line'    => 'Njia ya malipo',
            'payment-date'           => 'Tarehe ya malipo',
            'recipient-bank-account' => 'Akaunti ya benki ya mpokeaji',
            'communication'          => 'Kumbukumbu',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title'      => 'Malipo yameshindwa',
            'plain-body' => 'Malipo haya hayakuweza kuandaliwa. Timu yetu imearifiwa. Tafadhali jaribu tena baada ya muda mfupi, au wasiliana na huduma kwa wateja.',
        ],
    ],
];
