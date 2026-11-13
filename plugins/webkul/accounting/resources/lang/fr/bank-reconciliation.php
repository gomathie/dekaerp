<?php

return [
    'statement-name' => 'Relevé du :from au :to',
    'entry-label'    => 'Ligne de relevé bancaire',

    'fields' => [
        'date'   => 'date',
        'amount' => 'montant',
    ],

    'errors' => [
        'not-allowed'            => 'Vous ne pouvez pas rapprocher ce compte bancaire.',
        'not-a-bank-journal'     => 'Choisissez un journal de banque ou de caisse.',
        'foreign-currency'       => 'Ce journal utilise une devise étrangère. Pour l’instant, seuls les comptes en devise de la société peuvent être rapprochés.',
        'missing-bank-account'   => 'Ce journal n’a pas de compte bancaire. Définissez d’abord son compte par défaut.',
        'empty-file'             => 'Le fichier ne contient aucune ligne de relevé.',
        'too-many-rows'          => 'Le fichier contient plus de :max lignes. Divisez-le en relevés plus petits.',
        'bad-row'                => 'Ligne :row : le champ :field est illisible. Vérifiez les numéros de colonnes et le format de date.',
        'already-reconciled'     => 'Cette ligne est déjà rapprochée.',
        'payment-does-not-match' => 'Ce paiement ne correspond pas à cette ligne : il doit être dans le même journal bancaire, du même montant et du même sens, et pas encore rapproché.',
        'account-not-allowed'    => 'Ce compte ne peut pas être utilisé ici. Les comptes clients et fournisseurs se règlent en rapprochant un paiement.',
    ],
];
