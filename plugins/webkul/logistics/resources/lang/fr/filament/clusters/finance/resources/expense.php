<?php

return [
    'navigation' => [
        'title' => 'Dépenses',
    ],

    'form' => [
        'sections' => [
            'details'   => 'Détails de la dépense',
            'reference' => 'Référence et justificatif',
        ],
        'fields' => [
            'company'          => 'Société',
            'date'             => 'Date',
            'amount'           => 'Montant',
            'currency'         => 'Devise',
            'category'         => 'Catégorie',
            'paid-by'          => 'Payé par',
            'employee'         => 'Employé',
            'payee'            => 'Fournisseur ou transporteur',
            'shipment'         => 'Expédition',
            'trip'             => 'Trajet',
            'vehicle'          => 'Véhicule',
            'vendor-reference' => 'Référence fournisseur',
            'description'      => 'Description',
            'receipt'          => 'Justificatif',
        ],
    ],

    'table' => [
        'columns' => [
            'date'        => 'Date',
            'category'    => 'Catégorie',
            'description' => 'Description',
            'amount'      => 'Montant',
            'state'       => 'Statut',
            'shipment'    => 'Expédition',
            'company'     => 'Société',
        ],
        'filters' => [
            'state'    => 'Statut',
            'category' => 'Catégorie',
        ],
    ],

    'infolist' => [
        'sections' => [
            'summary' => 'Dépense',
        ],
        'fields' => [
            'state'       => 'Statut',
            'date'        => 'Date',
            'amount'      => 'Montant',
            'category'    => 'Catégorie',
            'shipment'    => 'Expédition',
            'trip'        => 'Trajet',
            'vehicle'     => 'Véhicule',
            'receipt'     => 'Justificatif',
            'description' => 'Description',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'Soumettre',
            'heading'      => 'Soumettre cette dépense ?',
            'notification' => 'Dépense soumise.',
        ],
        'approveExpense' => [
            'label'        => 'Approuver',
            'heading'      => 'Approuver cette dépense ?',
            'notification' => 'Dépense approuvée.',
        ],
        'rejectExpense' => [
            'label'        => 'Rejeter',
            'heading'      => 'Rejeter cette dépense ?',
            'notification' => 'Dépense rejetée.',
        ],
        'cannot-proceed' => 'Cette dépense n’est pas encore prête',
    ],
];
