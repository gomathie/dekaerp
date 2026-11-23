<?php

return [
    'create-employee' => 'Unda Mfanyakazi',
    'goto-employee'   => 'Go hadi Mfanyakazi',

    'notification' => [
        'title' => 'Mwombaji imesasishwa',
        'body'  => 'mwombaji ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Mwombaji imefutwa',
                'body'  => 'mwombaji ina imefutwa successfully.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Mwombaji imefutwa',
                'body'  => 'mwombaji ina force imefutwa successfully.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Mwombaji restored',
                'body'  => 'mwombaji ina restored successfully.',
            ],
        ],

        'refuse' => [
            'title'        => 'Kataa Sababu',
            'form'         => [
                'fields' => [
                    'notify'   => 'Notify',
                    'email-to' => 'Barua pepe Hadi',
                ],
            ],
            'notification' => [
                'title' => 'Mwombaji imekataliwa',
                'body'  => 'mwombaji ina imekataliwa successfully.',
            ],
        ],

        'reopen' => [
            'title'        => 'Reopen Mwombaji',
            'notification' => [
                'title' => 'Mwombaji reopened',
                'body'  => 'mwombaji ina reopened successfully.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Mwombaji hali imesasishwa',
                'body'  => 'mwombaji hali ina imesasishwa successfully.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Yako Kazi Application: :application',
        ],

        'application-confirm' => [
            'subject' => 'Yako Kazi Application: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Wewe ina amekabidhiwa hadi Mwombaji :applicant.',
        ],
    ],
];
