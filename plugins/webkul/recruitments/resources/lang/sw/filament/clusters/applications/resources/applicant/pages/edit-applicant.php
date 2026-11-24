<?php

return [
    'create-employee' => 'Unda mfanyakazi',
    'goto-employee'   => 'Nenda kwa mfanyakazi',

    'notification' => [
        'title' => 'Mwombaji amesasishwa',
        'body'  => 'Mwombaji amesasishwa kwa mafanikio.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Mwombaji amefutwa',
                'body'  => 'Mwombaji amefutwa kwa mafanikio.',
            ],
        ],
        'force-delete' => [
            'notification' => [
                'title' => 'Mwombaji amefutwa',
                'body'  => 'Mwombaji amefutwa kabisa.',
            ],
        ],

        'restore' => [
            'notification' => [
                'title' => 'Mwombaji amerejeshwa',
                'body'  => 'Mwombaji amerejeshwa kwa mafanikio.',
            ],
        ],

        'refuse' => [
            'title'        => 'Sababu ya kukataa',
            'form'         => [
                'fields' => [
                    'notify'   => 'Arifu',
                    'email-to' => 'Tuma kwa',
                ],
            ],
            'notification' => [
                'title' => 'Mwombaji amekataliwa',
                'body'  => 'Mwombaji amekataliwa kwa mafanikio.',
            ],
        ],

        'reopen' => [
            'title'        => 'Fungua upya mwombaji',
            'notification' => [
                'title' => 'Mwombaji amefunguliwa upya',
                'body'  => 'Mwombaji amefunguliwa upya kwa mafanikio.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Hali ya mwombaji imesasishwa',
                'body'  => 'Hali ya mwombaji imesasishwa kwa mafanikio.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Ombi lako la kazi: :application',
        ],

        'application-confirm' => [
            'subject' => 'Ombi lako la kazi: :job_position',
        ],
        'interviewer-assigned' => [
            'subject' => 'Umepewa mwombaji :applicant.',
        ],
    ],
];
