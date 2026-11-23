<?php

return [
    'form' => [
        'fields' => [
            'code' => 'Код',
            'name' => 'Название',
        ],
    ],

    'table' => [
        'columns' => [
            'code'       => 'Код',
            'name'       => 'Название',
            'created-by' => 'Создано От',
        ],

        'groups' => [
            'code' => 'Код',
            'name' => 'Название',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Incoterm обновлено',
                    'body'  => 'incoterm есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterm удалено',
                    'body'  => 'incoterm есть удалено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Incoterm restored',
                    'body'  => 'incoterm есть restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Incoterms restored',
                    'body'  => 'incoterms есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Incoterms удалено',
                    'body'  => 'incoterms есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Incoterms force удалено',
                    'body'  => 'incoterms есть force удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Название',
            'code' => 'Код',
        ],
    ],
];
