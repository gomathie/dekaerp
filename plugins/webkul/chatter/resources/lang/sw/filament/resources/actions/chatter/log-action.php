<?php

return [
    'setup' => [
        'title'        => 'Log Maelezo',
        'submit-title' => 'Log',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Ficha Subject',
                'add-subject'             => 'Ongeza Subject',
                'subject'                 => 'Subject',
                'write-message-here'      => 'Kuandika yako ujumbe hapa',
                'attachments-helper-text' => 'Max faili ukubwa: 10MB. Inaruhusiwa types: Images, PDF, Word, Excel, Text',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Log Maelezo added',
                    'body'  => 'Yako log maelezo added successfully.',
                ],

                'error' => [
                    'title' => 'Log ongeza hitilafu',
                    'body'  => 'Imeshindwa hadi ongeza yako log maelezo',
                ],
            ],
        ],
    ],
];
