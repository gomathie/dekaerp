<?php

return [
    'tabs' => [
        'all'      => 'Watumiaji wote',
        'archived' => 'Watumiaji waliowekwa kumbukumbu',
    ],

    'header-actions' => [
        'invitation' => 'Mwaliko unaosubiri',
        'resend'     => [
            'title'         => 'Tuma mwaliko tena',
            'invited'       => 'Mwaliko umetumwa tena',
            'invite-failed' => 'Mwaliko haukuweza kutumwa tena',
            'mail-failed'   => 'Barua pepe imeshindwa',
            'recovery'      => 'Mwaliko bado ni halali. Shiriki kiungo hiki kupitia njia unayoiamini: :link',
        ],
        'revoke' => [
            'title' => 'Batilisha mwaliko',
            'done'  => 'Mwaliko umebatilishwa',
        ],
        'invite' => [
            'title' => 'Alika mtumiaji',
            'modal' => [
                'submit-action-label' => 'Alika mtumiaji',
            ],
            'form' => [
                'email' => 'Barua pepe',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Mtumiaji amealikwa',
                    'body'  => 'Mtumiaji amealikwa kwa mafanikio',
                ],
                'error' => [
                    'title' => 'Mwaliko wa mtumiaji umeshindwa',
                    'body'  => 'Mfumo umekumbana na hitilafu isiyotarajiwa wakati wa kutuma mwaliko wa mtumiaji.',
                ],

                'default-company-error' => [
                    'title' => 'Kampuni chaguomsingi haijawekwa',
                    'body'  => 'Tafadhali weka kampuni chaguomsingi kwenye mipangilio kabla ya kumwalika mtumiaji.',
                ],
            ],
        ],

        'create' => [
            'label' => 'Mtumiaji mpya',
        ],
    ],
];
