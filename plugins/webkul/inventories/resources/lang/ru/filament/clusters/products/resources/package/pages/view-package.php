<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Печать',

            'actions' => [
                'without-content' => [
                    'label' => 'Печать штрихкода',
                ],

                'with-content' => [
                    'label' => 'Печать штрихкода с содержимым',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Упаковка удалена',
                    'body'  => 'Упаковка успешно удалена.',
                ],

                'error' => [
                    'title' => 'Не удалось удалить упаковку',
                    'body'  => 'Упаковку нельзя удалить, так как она используется.',
                ],
            ],
        ],
    ],
];
