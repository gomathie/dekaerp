<?php

return [
    'setup' => [
        'title'   => 'Viambatisho',
        'tooltip' => 'Pakia viambatisho',

        'modal-submit-action-label' => 'Pakia',

        'form' => [
            'fields' => [
                'files'                  => 'Faili',
                'attachment-helper-text' => 'Ukubwa wa juu wa faili: 10MB. Aina zinazoruhusiwa: Picha, PDF, Word, Excel, Maandishi',

                'actions' => [
                    'delete' => [
                        'title' => 'Faili imefutwa',
                        'body'  => 'Faili imefutwa kikamilifu.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Viambatisho vimepakiwa',
                    'body'  => 'Viambatisho vimepakiwa kikamilifu.',
                ],

                'warning'  => [
                    'title' => 'Hakuna faili mpya',
                    'body'  => 'Faili zote zimeshapakiwa.',
                ],

                'error' => [
                    'title' => 'Hitilafu ya kupakia viambatisho',
                    'body'  => 'Imeshindwa kupakia viambatisho ',
                ],
            ],
        ],
    ],
];
