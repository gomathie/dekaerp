<?php

return [
    'group' => [
        'label' => 'Ufikiaji wa tovuti ya wateja',
    ],

    'grant' => [
        'label' => 'Toa ufikiaji wa tovuti ya wateja',

        'modal' => [
            'heading'     => 'Toa ufikiaji wa tovuti ya wateja',
            'description' => 'Barua pepe ya mwaliko itatumwa kwa :email ikiwa na kiungo cha kuweka nenosiri la tovuti ya wateja.',
        ],

        'notification' => [
            'email-missing' => [
                'title' => 'Anwani ya barua pepe inahitajika',
                'body'  => 'Ongeza anwani ya barua pepe kwa mhusika huyu kabla ya kutoa ufikiaji wa tovuti ya wateja.',
            ],

            'email-taken' => [
                'title' => 'Anwani ya barua pepe tayari inatumika',
                'body'  => 'Mhusika mwingine mwenye ufikiaji wa tovuti ya wateja tayari anatumia anwani hii ya barua pepe.',
            ],

            'granted' => [
                'title' => 'Ufikiaji wa tovuti ya wateja umetolewa',
                'body'  => 'Mwaliko wa kuweka nenosiri umetumwa kwa :email.',
            ],

            'email-failed' => [
                'title' => 'Ufikiaji umetolewa, lakini barua pepe ya mwaliko haikuweza kutumwa',
            ],
        ],
    ],

    'change-password' => [
        'label' => 'Badilisha nenosiri',

        'modal' => [
            'heading'     => 'Badilisha nenosiri la tovuti ya wateja',
            'description' => 'Weka nenosiri jipya la tovuti ya wateja kwa :email.',
        ],

        'form' => [
            'password' => [
                'label' => 'Nenosiri jipya',
            ],

            'password-confirmation' => [
                'label' => 'Thibitisha nenosiri jipya',
            ],
        ],

        'notification' => [
            'changed' => [
                'title' => 'Nenosiri la tovuti ya wateja limebadilishwa',
                'body'  => 'Mhusika sasa anaweza kuingia kwenye tovuti ya wateja kwa nenosiri jipya.',
            ],
        ],
    ],

    'password-reset' => [
        'label' => 'Tuma kiungo cha kuweka upya nenosiri',

        'modal' => [
            'heading'     => 'Tuma kiungo cha kuweka upya nenosiri',
            'description' => 'Kiungo cha kuweka upya nenosiri kitatumwa kwa :email.',
        ],

        'notification' => [
            'sent' => [
                'title' => 'Kiungo cha kuweka upya nenosiri kimetumwa',
                'body'  => 'Kiungo cha kuweka upya nenosiri kimetumwa kwa :email.',
            ],

            'failed' => [
                'title' => 'Kiungo cha kuweka upya nenosiri hakikuweza kutumwa',
            ],
        ],
    ],

    'revoke' => [
        'label' => 'Batilisha ufikiaji wa tovuti ya wateja',

        'modal' => [
            'heading'     => 'Batilisha ufikiaji wa tovuti ya wateja',
            'description' => 'Mhusika hataweza tena kuingia kwenye tovuti ya wateja. Viungo vyovyote vya kuweka upya nenosiri ambavyo havijatumika vitabatilishwa.',
        ],

        'notification' => [
            'revoked' => [
                'title' => 'Ufikiaji wa tovuti ya wateja umebatilishwa',
                'body'  => 'Mhusika hawezi tena kuingia kwenye tovuti ya wateja.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'portal-access' => 'Ufikiaji wa tovuti ya wateja',
        ],

        'filters' => [
            'portal-access' => 'Ufikiaji wa tovuti ya wateja',
        ],
    ],

    'infolist' => [
        'section' => [
            'title' => 'Tovuti ya wateja',
        ],

        'entries' => [
            'status' => [
                'label'   => 'Ufikiaji wa tovuti ya wateja',
                'granted' => 'Umetolewa',
                'none'    => 'Haujatolewa',
            ],

            'email-verified-at' => [
                'label'       => 'Barua pepe ilithibitishwa',
                'placeholder' => 'Kamwe',
            ],

            'last-login-at' => [
                'label'       => 'Kuingia kwa mwisho',
                'placeholder' => 'Kamwe',
            ],
        ],
    ],
];
