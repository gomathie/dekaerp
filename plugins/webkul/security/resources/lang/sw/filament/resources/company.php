<?php

return [
    'capacity-error' => 'Kampuni hii imefikia nafasi zake :limit za watumiaji (:used zinatumika, pamoja na mialiko inayosubiri). Ongeza kikomo cha nafasi ili kuongeza watumiaji zaidi.',
    'title'          => 'Kampuni',

    'navigation' => [
        'title' => 'Kampuni',
    ],

    'provisioning' => [
        'column' => 'Hali ya usanidi',
        'filter' => 'Hali ya usanidi',

        'statuses' => [
            'active'           => 'Hai',
            'invited'          => 'Amealikwa, bado hajaanza',
            'expired'          => 'Mwaliko umeisha muda',
            'no-administrator' => 'Hakuna msimamizi',
            'closed'           => 'Imefungwa',
        ],

        'resend' => [
            'label'         => 'Tuma mwaliko tena',
            'heading'       => 'Tuma tena mwaliko wa msimamizi?',
            'description'   => 'Mwaliko kwa :email utatumwa tena na utadumu kwa siku 7 zaidi.',
            'invited'       => 'Mwaliko umetumwa tena',
            'invite-failed' => 'Mwaliko haukuweza kutumwa tena. Huenda umekubaliwa au kubatilishwa katika muda huu.',
        ],
    ],

    'global-search' => [
        'email' => 'Barua pepe',
    ],

    'pages' => [
        'create' => [
            'administrator' => [
                'invited' => [
                    'title' => 'Msimamizi amealikwa',
                    'body'  => 'Mwaliko umetumwa kwa :email. Atachagua nenosiri lake mwenyewe kisha ataweza kuongeza watumiaji wa kampuni yake.',
                ],

                'skipped' => [
                    'title'         => 'Hakuna msimamizi aliyealikwa',
                    'missing-email' => 'Kampuni imeundwa, lakini haina anwani ya barua pepe, kwa hivyo hakuna aliyeweza kualikwa kuisimamia. Ongeza anwani na utume mwaliko kutoka ukurasa wa Watumiaji.',
                    'missing-role'  => 'Kampuni imeundwa, lakini jukumu la Company Admin halipo kwenye usakinishaji huu, kwa hivyo hakuna aliyeweza kualikwa.',

                    'seats-exhausted' => 'Kampuni imeundwa, lakini haina nafasi za watumiaji zilizobaki, kwa hivyo hakuna aliyeweza kualikwa kuisimamia. Ongeza kikomo cha nafasi na utume mwaliko kutoka ukurasa wa Watumiaji.',
                    'invite-failed'   => 'Kampuni imeundwa, lakini mwaliko haukuweza kutolewa. Huenda anwani tayari ni ya mtumiaji, au huenda bado huruhusiwi kugawa watumiaji kwa kampuni hii. Alika kutoka ukurasa wa Watumiaji badala yake.',
                    'mail-failed'     => 'Kampuni imeundwa na mwaliko umetolewa, lakini barua pepe haikuweza kutumwa. Mpe :email kiungo hiki cha kuingia kupitia njia unayoiamini — kinaisha baada ya siku 7, naye ataweka nenosiri lake mwenyewe: :link',
                ],
            ],
        ],

        'edit' => [
            'administrator' => [
                'skipped' => [
                    'title'           => 'Hakuna msimamizi aliyealikwa',
                    'disabled'        => 'Mialiko imezimwa kwenye mipangilio ya watumiaji, kwa hivyo :email hakualikwa. Weka nenosiri badala yake, au washa mialiko.',
                    'missing-role'    => 'Jukumu la Company Admin halipo kwenye usakinishaji huu, kwa hivyo hakuna aliyeweza kualikwa.',
                    'seats-exhausted' => 'Kampuni hii haina nafasi za watumiaji zilizobaki, kwa hivyo :email hakualikwa. Ongeza kikomo cha nafasi kwanza.',
                    'invite-failed'   => 'Mwaliko haukuweza kutolewa. Huenda anwani tayari ni ya mtumiaji, au huenda huruhusiwi kugawa watumiaji kwa kampuni hii. Ili kuweka upya nenosiri la msimamizi aliyepo, chagua “Weka nenosiri sasa” badala yake.',
                    'mail-failed'     => 'Mwaliko umetolewa, lakini barua pepe haikuweza kutumwa. Mpe :email kiungo hiki cha kuingia kupitia njia unayoiamini — kinaisha baada ya siku 7, naye ataweka nenosiri lake mwenyewe: :link',
                ],
            ],
        ],

        'administrator-account' => [
            'created' => [
                'title' => 'Akaunti ya msimamizi imeundwa',
                'body'  => ':email anaweza kuingia sasa kwa nenosiri uliloweka. Halikutumwa kwa barua pepe: mpe wewe mwenyewe.',
            ],

            'password-reset' => [
                'title' => 'Nenosiri la msimamizi limebadilishwa',
                'body'  => 'Nenosiri la :email limebadilishwa. Halikutumwa kwa barua pepe: mpe wewe mwenyewe.',
            ],

            'refused' => [
                'title'             => 'Msimamizi hakubadilishwa',
                'missing-email'     => 'Weka anwani ya barua pepe ya msimamizi pamoja na nenosiri.',
                'missing-role'      => 'Jukumu la Company Admin halipo kwenye usakinishaji huu, kwa hivyo hakuna akaunti iliyoundwa.',
                'not-member'        => ':email ni ya mtumiaji ambaye si mwanachama wa kampuni hii, kwa hivyo nenosiri lake halikubadilishwa hapa. Dhibiti mtumiaji huyo kutoka ukurasa wa Watumiaji.',
                'not-allowed'       => 'Huruhusiwi kudhibiti msimamizi wa kampuni hii.',
                'seats-exhausted'   => 'Kampuni hii haina nafasi za watumiaji zilizobaki, kwa hivyo hakuna akaunti iliyoundwa kwa :email. Ongeza kikomo cha nafasi kwanza.',
            ],
        ],
    ],
];
