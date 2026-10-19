<?php

return [
    'navigation' => [
        'title' => 'Chauffeurs',
    ],

    'form' => [
        'fields' => [
            'company'            => 'Société',
            'employee'           => 'Employé',
            'partner'            => 'Contact du transporteur',
            'name'               => 'Nom',
            'phone'              => 'Téléphone',
            'license-number'     => 'Numéro de permis',
            'license-class'      => 'Catégorie de permis',
            'license-expires-at' => 'Expiration du permis',
            'is-active'          => 'Actif',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nom',
            'phone'              => 'Téléphone',
            'employee'           => 'Employé',
            'partner'            => 'Contact du transporteur',
            'license-number'     => 'Numéro de permis',
            'license-expires-at' => 'Expiration du permis',
            'company'            => 'Société',
            'is-active'          => 'Actif',
        ],

        'filters' => [
            'license-attention' => 'Expiré ou bientôt expiré',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'Chauffeur',
            'license' => 'Permis de conduire',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'Ajouter des chauffeurs depuis les employés',

            'fields' => [
                'employees' => 'Employés',
            ],

            'notification' => [
                'title' => 'Importation des employés terminée',
                'body'  => ':created créés, :skipped ignorés.',
            ],
        ],
    ],
];
