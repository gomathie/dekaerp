<?php

    return [
        'setup' => [
            'title'               => 'Wafuasi',
            'submit-action-title' => 'Ongeza mfuasi',
            'tooltip'             => 'Ongeza mfuasi',

            'form' => [
                'fields' => [
                    'recipients'  => 'Wapokeaji',
                    'notify-user' => 'Arifu mtumiaji',
                    'add-a-note'  => 'Ongeza kidokezo',
                ],
            ],

            'actions' => [
                'notification' => [
                    'success' => [
                        'title' => 'Mfuasi ameongezwa',
                        'body'  => 'Mfuasi ameongezwa kikamilifu.',
                    ],

                    'partial_message' => [
                        'title'    => 'Ujumbe umetumwa pamoja na tahadhari',
                        'single'   => 'Mfuasi :count hakuarifiwa kwa kukosa barua pepe: :names',
                        'multiple' => 'Wafuasi :count hawakuarifiwa kwa kukosa barua pepe: :names',
                    ],

                    'error' => [
                        'title' => 'Hitilafu ya kuongeza mfuasi',
                        'body'  => 'Imeshindwa kumwongeza ":partner" kama mfuasi',
                    ],
                ],

                'mail' => [
                    'subject' => 'Mwaliko wa kufuatilia :model: :department',
                ],
            ],
        ],
    ];
