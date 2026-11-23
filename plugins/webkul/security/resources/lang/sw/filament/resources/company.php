<?php

return [
    'capacity-error' => 'Hii kampuni ina reached yake :limit mtumiaji seats (:used katika tumia, pamoja na inasubiri invitations). Raise seat limit hadi ongeza zaidi watumiaji.',
    'title'          => 'Makampuni',

    'navigation' => [
        'title' => 'Makampuni',
    ],

    'provisioning' => [
        'column' => 'Setup hali',
        'filter' => 'Setup hali',

        'statuses' => [
            'active'           => 'Inatumika',
            'invited'          => 'Invited, si bado inatumika',
            'expired'          => 'Mwaliko expired',
            'no-administrator' => 'Hakuna msimamizi',
            'closed'           => 'Imefungwa',
        ],

        'resend' => [
            'label'         => 'Resend mwaliko',
            'heading'       => 'Resend msimamizi mwaliko?',
            'description'   => 'mwaliko hadi :email ni imetumwa tena na stays halali kwa 7 zaidi siku.',
            'invited'       => 'Mwaliko imetumwa tena',
            'invite-failed' => 'mwaliko could si kuwa imetumwa tena. Hii mei ina accepted au revoked katika meantime.',
        ],
    ],

    'global-search' => [
        'email' => 'Barua pepe',
    ],

    'pages' => [
        'create' => [
            'administrator' => [
                'invited' => [
                    'title' => 'Msimamizi invited',
                    'body'  => 'mwaliko ilikuwa imetumwa hadi :email. Wao chagua yao yake nenosiri na unaweza kisha ongeza yao kampuni’s watumiaji.',
                ],

                'skipped' => [
                    'title'         => 'Hakuna msimamizi ilikuwa invited',
                    'missing-email' => 'kampuni ilikuwa imeundwa, lakini hii ina hakuna barua pepe anwani, hivyo nobody could kuwa invited hadi administer hii. Ongeza one na send mwaliko kutoka Watumiaji ukurasa.',
                    'missing-role'  => 'kampuni ilikuwa imeundwa, lakini Kampuni Msimamizi jukumu si exist katika hii installation, hivyo nobody could kuwa invited.',

                    'seats-exhausted' => 'kampuni ilikuwa imeundwa, lakini hii ina hakuna free mtumiaji seats iliyobaki, hivyo nobody could kuwa invited hadi administer hii. Raise seat limit na send mwaliko kutoka Watumiaji ukurasa.',
                    'invite-failed'   => 'kampuni ilikuwa imeundwa, lakini mwaliko could si kuwa issued. anwani mei tayari belong hadi mtumiaji, au kampuni mei si bado kuwa one wewe ni inaruhusiwa hadi kabidhi. Alika kutoka Watumiaji ukurasa instead.',
                    'mail-failed'     => 'kampuni ilikuwa imeundwa na mwaliko ilikuwa issued, lakini barua pepe could si kuwa imetumwa. Give hii sign-katika link hadi :email zaidi ya channel wewe trust — hii expires katika 7 siku, na wao weka yao yake nenosiri: :link',
                ],
            ],
        ],

        'edit' => [
            'administrator' => [
                'skipped' => [
                    'title'           => 'Hakuna msimamizi ilikuwa invited',
                    'disabled'        => 'Invitations ni switched zima katika mtumiaji mipangilio, hivyo :email ilikuwa si invited. Weka nenosiri instead, au switch invitations washa.',
                    'missing-role'    => 'Kampuni Msimamizi jukumu si exist katika hii installation, hivyo nobody could kuwa invited.',
                    'seats-exhausted' => 'Hii kampuni ina hakuna free mtumiaji seats iliyobaki, hivyo :email ilikuwa si invited. Raise seat limit kwanza.',
                    'invite-failed'   => 'mwaliko could si kuwa issued. anwani mei tayari belong hadi mtumiaji, au kampuni mei si kuwa one wewe ni inaruhusiwa hadi kabidhi. Hadi weka upya existing msimamizi’s nenosiri, chagua “Weka nenosiri sasa” instead.',
                    'mail-failed'     => 'mwaliko ilikuwa issued, lakini barua pepe could si kuwa imetumwa. Give hii sign-katika link hadi :email zaidi ya channel wewe trust — hii expires katika 7 siku, na wao weka yao yake nenosiri: :link',
                ],
            ],
        ],

        'administrator-account' => [
            'created' => [
                'title' => 'Msimamizi akaunti imeundwa',
                'body'  => ':email unaweza ingia sasa na nenosiri wewe weka. Hii ilikuwa si emailed: give hii hadi wao yourself.',
            ],

            'password-reset' => [
                'title' => 'Msimamizi nenosiri imebadilishwa',
                'body'  => 'nenosiri kwa :email ilikuwa imebadilishwa. Hii ilikuwa si emailed: give hii hadi wao yourself.',
            ],

            'refused' => [
                'title'             => 'msimamizi ilikuwa si imebadilishwa',
                'missing-email'     => 'Ingiza msimamizi’s barua pepe anwani together na nenosiri.',
                'missing-role'      => 'Kampuni Msimamizi jukumu si exist katika hii installation, hivyo hakuna akaunti ilikuwa imeundwa.',
                'not-member'        => ':email belongs hadi mtumiaji nani ni si mwanachama ya hii kampuni, hivyo yao nenosiri ilikuwa si imebadilishwa hapa. Manage hiyo mtumiaji kutoka Watumiaji ukurasa.',
                'not-allowed'       => 'Wewe ni si inaruhusiwa hadi manage hii kampuni’s msimamizi.',
                'seats-exhausted'   => 'Hii kampuni ina hakuna free mtumiaji seats iliyobaki, hivyo hakuna akaunti ilikuwa imeundwa kwa :email. Raise seat limit kwanza.',
            ],
        ],
    ],
];
