<?php

return [
    'title' => 'Watumiaji na logins',

    'table' => [
        'columns' => [
            'name'                 => 'Jina',
            'login'                => 'Ingia (barua pepe)',
            'company-admin'        => 'Kampuni Msimamizi',
            'active'               => 'Inatumika',
            'must-change-password' => 'Lazima badilisha nenosiri',
        ],

        'empty-state' => [
            'heading'     => 'Hakuna watumiaji bado',
            'description' => 'Alika au unda msimamizi katika Msimamizi sehemu juu ya.',
        ],

        'actions' => [
            'copy-invitation-link' => [
                'label'      => 'Nakili mwaliko link',
                'invitation' => 'Inasubiri mwaliko',
                'link'       => 'Mwaliko link',
            ],
            'show-password' => [
                'label'             => 'Onyesha nenosiri',
                'modal-heading'     => 'Onyesha nenosiri kwa :email',
                'modal-description' => 'Ingiza yako yake nenosiri hadi endelea. Hii angalia ni recorded katika audit log.',
                'submit'            => 'Onyesha nenosiri',
                'shown'             => 'Nenosiri kwa :email',

                'form' => [
                    'password' => 'Yako nenosiri',
                ],
            ],
            'set-password' => [
                'label'             => 'Weka nenosiri',
                'modal-heading'     => 'Weka nenosiri kwa :email',
                'modal-description' => 'Existing passwords haiwezekani kuwa shown, kwa sababu wao ni stored encrypted one way. Weka mpya one na give hii hadi mtumiaji yourself: hii ni si emailed.',

                'form' => [
                    'password'             => 'Mpya nenosiri',
                    'must-change-password' => 'Lazima badilisha hii nenosiri saa ifuatayo sign-katika',
                ],
            ],
        ],
    ],
];
