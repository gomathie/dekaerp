<?php

return [
    'title' => 'Chapisha na tuma',

    'modal' => [
        'title' => 'Hakiki ankara',

        'form' => [
            'partners'    => 'Mteja',
            'subject'     => 'Kichwa',
            'description' => 'Maelezo',
            'files'       => 'Kiambatisho',
        ],

        'action' => [
            'submit' => [
                'title' => 'Tuma',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Ankara imetumwa',
                'body'  => 'Ankara imetumwa kikamilifu.',
            ],
        ],
    ],

    'message' => [
        'subject'   => 'Ankara ya :partner (Kumb. :number)',
        'greeting'  => 'Mpendwa :name,',
        'body'      => 'Ankara yako :number kutoka :company ya :amount sasa inapatikana. Tafadhali panga malipo mapema iwezekanavyo.',
        'bank'      => 'Unapofanya malipo, tafadhali taja kumbukumbu :number kwa akaunti :account.',
        'questions' => 'Ukiwa na swali lolote, usisite kuwasiliana nasi.',
        'regards'   => 'Wako mwaminifu,',
    ],
];
