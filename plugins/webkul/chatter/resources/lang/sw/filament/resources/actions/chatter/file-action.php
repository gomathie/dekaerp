<?php

return [
    'setup' => [
        'title'   => 'Viambatisho',
        'tooltip' => 'Pakia Viambatisho',

        'modal-submit-action-label' => 'Pakia',

        'form' => [
            'fields' => [
                'files'                  => 'Faili',
                'attachment-helper-text' => 'Max faili ukubwa: 10MB. Inaruhusiwa types: Images, PDF, Word, Excel, Text',

                'actions' => [
                    'delete' => [
                        'title' => 'Faili imefutwa',
                        'body'  => 'Faili ina imefutwa successfully.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Viambatisho Uploaded',
                    'body'  => 'Viambatisho uploaded successfully.',
                ],

                'warning'  => [
                    'title' => 'Hakuna mpya faili',
                    'body'  => 'Zote faili ina tayari uploaded.',
                ],

                'error' => [
                    'title' => 'Kiambatisho pakia hitilafu',
                    'body'  => 'Imeshindwa hadi pakia viambatisho ',
                ],
            ],
        ],
    ],
];
