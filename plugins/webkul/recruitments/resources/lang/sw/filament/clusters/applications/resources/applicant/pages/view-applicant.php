<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Mwombaji amefutwa',
                'body'  => 'Mwombaji amefutwa kwa mafanikio.',
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
            'title'        => 'Rejesha mwombaji aliyekataliwa',
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
    ],
];
