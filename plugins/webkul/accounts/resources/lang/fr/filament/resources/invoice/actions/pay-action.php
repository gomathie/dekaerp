<?php

return [
    'title' => 'Payer',

    'form' => [
        'fields' => [
            'journal'                => 'Journal',
            'amount'                 => 'Montant',
            'currency'               => 'Devise',
            'payment-method-line'    => 'Ligne de mode de paiement',
            'payment-date'           => 'Date de paiement',
            'recipient-bank-account' => 'Compte bancaire du bénéficiaire',
            'communication'          => 'Mémo',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title'      => 'Échec du paiement',
            'plain-body' => 'Ce paiement n’a pas pu être préparé. Notre équipe a été prévenue. Réessayez dans un instant ou contactez le support.',
        ],
    ],
];
