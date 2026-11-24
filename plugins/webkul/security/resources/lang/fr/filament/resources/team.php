<?php

return [
    'title' => 'Équipes',

    'navigation' => [
        'title' => 'Équipes',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nom',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nom',
            'created-by' => 'Créé par',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Équipe mise à jour',
                    'body'  => 'L’équipe a été mise à jour avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Équipe supprimée',
                    'body'  => 'L’équipe a été supprimée avec succès.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Équipes créées',
                    'body'  => 'Les équipes ont été créées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nom',
        ],
    ],
];
