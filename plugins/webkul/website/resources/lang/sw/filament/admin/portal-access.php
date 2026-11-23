<?php

return [
    'group' => [
        'label' => 'Lango Access',
    ],

    'grant' => [
        'label' => 'Grant Lango Access',

        'modal' => [
            'heading'     => 'Grant Lango Access',
            'description' => 'mwaliko barua pepe ita kuwa imetumwa hadi :email na link hadi weka nenosiri kwa mteja lango.',
        ],

        'notification' => [
            'email-missing' => [
                'title' => 'Barua pepe anwani inahitajika',
                'body'  => 'Ongeza barua pepe anwani hadi hii mwasiliani kabla ya granting lango access.',
            ],

            'email-taken' => [
                'title' => 'Barua pepe anwani tayari katika tumia',
                'body'  => 'Another mwasiliani na lango access ni tayari kwa kutumia hii barua pepe anwani.',
            ],

            'granted' => [
                'title' => 'Lango access granted',
                'body'  => 'mwaliko hadi weka nenosiri ina imetumwa hadi :email.',
            ],

            'email-failed' => [
                'title' => 'Lango access granted, lakini mwaliko barua pepe could si kuwa imetumwa',
            ],
        ],
    ],

    'change-password' => [
        'label' => 'Badilisha Nenosiri',

        'modal' => [
            'heading'     => 'Badilisha Lango Nenosiri',
            'description' => 'Weka mpya mteja lango nenosiri kwa :email.',
        ],

        'form' => [
            'password' => [
                'label' => 'Mpya Nenosiri',
            ],

            'password-confirmation' => [
                'label' => 'Thibitisha Mpya Nenosiri',
            ],
        ],

        'notification' => [
            'changed' => [
                'title' => 'Lango nenosiri imebadilishwa',
                'body'  => 'mwasiliani unaweza sasa ingia hadi mteja lango na mpya nenosiri.',
            ],
        ],
    ],

    'password-reset' => [
        'label' => 'Send Nenosiri Weka upya',

        'modal' => [
            'heading'     => 'Send Nenosiri Weka upya',
            'description' => 'nenosiri weka upya link ita kuwa imetumwa hadi :email.',
        ],

        'notification' => [
            'sent' => [
                'title' => 'Nenosiri weka upya link imetumwa',
                'body'  => 'nenosiri weka upya link ina imetumwa hadi :email.',
            ],

            'failed' => [
                'title' => 'Nenosiri weka upya link could si kuwa imetumwa',
            ],
        ],
    ],

    'revoke' => [
        'label' => 'Revoke Lango Access',

        'modal' => [
            'heading'     => 'Revoke Lango Access',
            'description' => 'mwasiliani ita hakuna longer kuwa able hadi ingia hadi mteja lango. Yoyote inasubiri nenosiri weka upya links ita kuwa invalidated.',
        ],

        'notification' => [
            'revoked' => [
                'title' => 'Lango access revoked',
                'body'  => 'mwasiliani unaweza hakuna longer ingia hadi mteja lango.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'portal-access' => 'Lango Access',
        ],

        'filters' => [
            'portal-access' => 'Lango Access',
        ],
    ],

    'infolist' => [
        'section' => [
            'title' => 'Mteja Lango',
        ],

        'entries' => [
            'status' => [
                'label'   => 'Lango Access',
                'granted' => 'Granted',
                'none'    => 'Si Granted',
            ],

            'email-verified-at' => [
                'label'       => 'Barua pepe Verified Saa',
                'placeholder' => 'Kamwe',
            ],

            'last-login-at' => [
                'label'       => 'Mwisho Lango Ingia',
                'placeholder' => 'Kamwe',
            ],
        ],
    ],
];
