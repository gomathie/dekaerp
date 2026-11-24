<?php

return [
    'form' => [
        'sections' => [
            'fields' => [
                'title'        => 'Titre',
                'type'         => 'Type',
                'name'         => 'Nom',
                'type'         => 'Type',
                'create-type'  => 'Créer un type',
                'duration'     => 'Durée',
                'start-date'   => 'Date de début',
                'end-date'     => 'Date de fin',
                'display-type' => 'Type d\'affichage',
                'description'  => 'Description',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'title'        => 'Titre',
            'start-date'   => 'Date de début',
            'end-date'     => 'Date de fin',
            'display-type' => 'Type d\'affichage',
            'description'  => 'Description',
            'created-by'   => 'Créé par',
            'created-at'   => 'Créé le',
            'updated-at'   => 'Mis à jour le',
        ],

        'groups' => [
            'group-by-type'         => 'Grouper par type',
            'group-by-display-type' => 'Grouper par type d\'affichage',
        ],

        'header-actions' => [
            'add-resume' => 'Ajouter un CV',
        ],

        'filters' => [
            'type'            => 'Type',
            'start-date-from' => 'Date de début à partir de',
            'start-date-to'   => 'Date de début jusqu\'à',
            'created-from'    => 'Créé à partir de',
            'created-to'      => 'Créé jusqu\'à',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Entrée du CV mise à jour',
                    'body'  => 'L\'entrée du CV a été mise à jour avec succès.',
                ],
            ],

            'create' => [
                'notification' => [
                    'title' => 'Entrée du CV créée',
                    'body'  => 'L\'entrée du CV a été créée avec succès.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Entrée du CV supprimée',
                    'body'  => 'L\'entrée du CV a été supprimée avec succès.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Entrées du CV supprimées',
                    'body'  => 'Les entrées du CV ont été supprimées avec succès.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'title'        => 'Titre',
            'display-type' => 'Type d\'affichage',
            'type'         => 'Type',
            'description'  => 'Description',
            'duration'     => 'Durée',
            'start-date'   => 'Date de début',
            'end-date'     => 'Date de fin',
        ],
    ],
];
