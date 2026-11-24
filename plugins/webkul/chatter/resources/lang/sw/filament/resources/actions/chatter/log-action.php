<?php

return [
    'setup' => [
        'title'        => 'Andika kidokezo',
        'submit-title' => 'Andika',

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
                    'title' => 'Kidokezo kimeongezwa',
                    'body'  => 'Kidokezo chako kimeongezwa kikamilifu.',
                ],

                'error' => [
                    'title' => 'Hitilafu ya kuongeza kidokezo',
                    'body'  => 'Imeshindwa kuongeza kidokezo chako',
                ],
            ],
        ],
    ],
];
