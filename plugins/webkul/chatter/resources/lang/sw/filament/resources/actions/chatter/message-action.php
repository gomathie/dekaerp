<?php

return [
    'setup' => [
        'title'        => 'Tuma ujumbe',
        'submit-title' => 'Tuma',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Ficha mada',
                'add-subject'             => 'Ongeza mada',
                'subject'                 => 'Mada',
                'write-message-here'      => 'Andika ujumbe wako hapa',
                'attachments-helper-text' => 'Ukubwa wa juu wa faili: 10MB. Aina zinazoruhusiwa: Picha, PDF, Word, Excel, Maandishi',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Ujumbe umetumwa',
                    'body'  => 'Ujumbe wako umetumwa kikamilifu.',
                ],

                'error' => [
                    'title' => 'Hitilafu ya kutuma ujumbe',
                    'body'  => 'Imeshindwa kutuma ujumbe wako',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
