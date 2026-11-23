<?php

return [
    'tabs' => [
        'all'      => 'Zote Watumiaji',
        'archived' => 'Imehifadhiwa Watumiaji',
    ],

    'header-actions' => [
        'invitation' => 'Inasubiri mwaliko',
        'resend'     => [
            'title'         => 'Resend mwaliko',
            'invited'       => 'Mwaliko resent',
            'invite-failed' => 'Mwaliko could si kuwa resent',
            'mail-failed'   => 'Barua pepe imeshindwa',
            'recovery'      => 'mwaliko ni bado inatumika. Share hii link zaidi ya trusted channel: :link',
        ],
        'revoke' => [
            'title' => 'Revoke mwaliko',
            'done'  => 'Mwaliko revoked',
        ],
        'invite' => [
            'title' => 'Alika Mtumiaji',
            'modal' => [
                'submit-action-label' => 'Alika Mtumiaji',
            ],
            'form' => [
                'email' => 'Barua pepe',
            ],
            'notification' => [
                'success' => [
                    'title' => 'Mtumiaji invited',
                    'body'  => 'Mtumiaji ina invited successfully',
                ],
                'error' => [
                    'title' => 'Mtumiaji Mwaliko Imeshindwa',
                    'body'  => 'mfumo encountered unexpected hitilafu wakati trying hadi send mtumiaji mwaliko.',
                ],

                'default-company-error' => [
                    'title' => 'Chaguo-msingi Kampuni Si Weka',
                    'body'  => 'Tafadhali weka chaguo-msingi kampuni kutoka mipangilio, kabla ya inviting mtumiaji.',
                ],
            ],
        ],

        'create' => [
            'label' => 'Mpya Mtumiaji',
        ],
    ],
];
