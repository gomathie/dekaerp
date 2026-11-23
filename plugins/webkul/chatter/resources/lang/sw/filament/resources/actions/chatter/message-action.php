<?php

return [
    'setup' => [
        'title'        => 'Send Ujumbe',
        'submit-title' => 'Send',

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
                    'title' => 'Ujumbe imetumwa',
                    'body'  => 'Yako ujumbe ina send successfully.',
                ],

                'error' => [
                    'title' => 'Ujumbe imetumwa hitilafu',
                    'body'  => 'Imeshindwa hadi send yako ujumbe',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
