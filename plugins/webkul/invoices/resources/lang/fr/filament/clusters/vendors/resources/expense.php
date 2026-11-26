<?php

return [
    'title' => 'Dépense',

    'navigation' => [
        'title' => 'Dépenses',
    ],

    'form' => [
        'fields' => [
            'number'          => 'Numéro',
            'date'            => 'Date',
            'amount'          => 'Montant',
            'currency'        => 'Devise',
            'expense-account' => 'Compte de charges',
            'paid-from'       => 'Payé depuis',
            'vendor'          => 'Fournisseur',
            'vendor-name'     => 'Nom du fournisseur',
            'tax'             => 'Taxe',
            'tax-amount'      => 'Montant de la taxe',
            'reference'       => 'Référence',
            'note'            => 'Note',
            'receipt'         => 'Justificatif',
            'entry'           => 'Écriture comptable',
            'reason'          => 'Motif de la modification',
        ],

        'hints' => [
            'currency'        => 'Une autre devise utilise le taux du jour, comme sur les factures.',
            'expense-account' => 'Uniquement les comptes de charges de cette société. Le dernier utilisé est proposé en premier.',
            'paid-from'       => 'Tous les comptes bancaires et de caisse de la société.',
            'vendor'          => 'Facultatif. Saisissez un nouveau nom pour ajouter le fournisseur.',
            'tax'             => 'Facultatif. Vide : pas de ligne de taxe.',
            'no-tax'          => 'Pas de taxe',
            'receipt'         => 'Une photo ou un PDF, 10 Mo au plus. Sur un téléphone, vous pouvez prendre une photo.',
            'reason'          => 'Cette dépense est déjà en comptabilité. Indiquez pourquoi elle change.',
        ],

        'tax-included' => ':tax (incluse)',

        'summary' => [
            'empty' => 'Saisissez un montant pour voir ce qui sera comptabilisé.',
            'posts' => 'À l\'enregistrement, :total sort du compte de paiement : :net au compte de charges et :tax de taxe.',
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Date',
            'expense-account' => 'Compte de charges',
            'paid-from'       => 'Payé depuis',
            'vendor'          => 'Fournisseur',
            'reference'       => 'Référence',
            'amount'          => 'Montant',
            'receipt'         => 'Justificatif',
        ],

        'total'  => 'Total',
        'export' => 'Exporter',

        'filters' => [
            'period'          => 'Période',
            'from'            => 'Du',
            'until'           => 'Au',
            'paid-from'       => 'Payé depuis',
            'expense-account' => 'Compte de charges',
            'vendor'          => 'Fournisseur',
            'with-receipt'    => 'Avec justificatif',
        ],
    ],

    'actions' => [
        'new'          => 'Nouvelle dépense',
        'save'         => 'Enregistrer',
        'save-and-new' => 'Enregistrer et nouveau',

        'delete' => [
            'label'       => 'Supprimer',
            'description' => 'L\'écriture de la dépense est annulée : en comptabilité, l\'argent revient sur le compte de paiement.',
            'done'        => 'Dépense supprimée',
        ],
    ],

    'notifications' => [
        'created' => 'Dépense enregistrée et comptabilisée',
        'saved'   => 'Dépense mise à jour',
        'refused' => 'La dépense n\'a pas été enregistrée',
        'failed'  => 'Une erreur s\'est produite. Rien n\'a été modifié.',
    ],

    'export' => [
        'columns' => [
            'number'          => 'Numéro',
            'date'            => 'Date',
            'expense-account' => 'Compte de charges',
            'paid-from'       => 'Payé depuis',
            'vendor'          => 'Fournisseur',
            'reference'       => 'Référence',
            'currency'        => 'Devise',
            'amount'          => 'Montant',
            'tax'             => 'Taxe',
            'amount-company'  => 'Montant dans la devise de la société',
            'note'            => 'Note',
        ],

        'notification' => [
            'completed' => 'L\'export des dépenses est terminé : :count lignes exportées.',
            'failed'    => ':count lignes n\'ont pas pu être exportées.',
        ],
    ],
];
