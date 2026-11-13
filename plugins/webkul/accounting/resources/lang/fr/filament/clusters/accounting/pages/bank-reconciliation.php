<?php

return [
    'navigation' => [
        'title' => 'Rapprochement bancaire',
    ],

    'import' => [
        'label'       => 'Importer un relevé',
        'description' => 'Téléversez le fichier CSV de votre banque. Indiquez le numéro de chaque colonne, en comptant à partir de 1 à gauche.',
        'journal'     => 'Compte bancaire',
        'file'        => 'Fichier du relevé (CSV)',
        'name'        => 'Nom du relevé',
        'date-format' => 'Format de date',
        'has-header'  => 'La première ligne est un en-tête',
        'amount-help' => 'Ou laissez vide et indiquez les colonnes entrées et sorties.',
        'done'        => ':count lignes importées.',

        'columns' => [
            'date'      => 'Colonne date',
            'label'     => 'Colonne libellé',
            'amount'    => 'Colonne montant',
            'money_in'  => 'Colonne entrées',
            'money_out' => 'Colonne sorties',
            'partner'   => 'Colonne client ou fournisseur',
            'reference' => 'Colonne référence',
        ],
    ],

    'columns' => [
        'date'    => 'Date',
        'label'   => 'Libellé',
        'partner' => 'Client ou fournisseur',
        'journal' => 'Compte bancaire',
        'amount'  => 'Montant',
        'status'  => 'Statut',
        'payment' => 'Paiement',
    ],

    'status' => [
        'reconciled'   => 'Rapprochée',
        'to-reconcile' => 'À rapprocher',
    ],

    'match' => [
        'label'       => 'Rapprocher un paiement',
        'description' => 'Paiements de ce compte bancaire du même montant, en attente de la banque.',
        'payment'     => 'Paiement',
        'none'        => 'Aucun paiement ne correspond. Enregistrez d’abord le paiement sur la facture, ou comptabilisez la ligne sur un compte.',
        'done'        => 'Ligne rapprochée. Le paiement est maintenant payé.',
    ],

    'book' => [
        'label'       => 'Comptabiliser sur un compte',
        'description' => 'Pour les frais bancaires, intérêts ou virements : passe la ligne sur le compte choisi.',
        'account'     => 'Compte',
        'entry-label' => 'Libellé',
        'done'        => 'Ligne comptabilisée.',
    ],

    'empty' => [
        'heading'     => 'Rien à rapprocher',
        'description' => 'Importez un relevé de votre banque pour commencer.',
    ],
];
