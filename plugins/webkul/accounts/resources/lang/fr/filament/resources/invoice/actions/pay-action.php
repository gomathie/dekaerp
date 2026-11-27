<?php

return [
    'title' => 'Enregistrer un paiement',

    'paid-now' => [
        'label'       => 'Payée maintenant',
        'helper'      => 'L\'enregistrement confirme la facture et enregistre le paiement complet.',
        'invalid'     => 'Ce moyen de paiement ne peut pas être utilisé pour cette facture.',
        'not-allowed' => 'Vous n\'êtes pas autorisé à enregistrer des paiements.',
    ],

    'form' => [
        'more-options' => 'Plus d\'options',
        'hints'        => [
            'balance-after'  => 'Solde après ce paiement : :amount',
            'kept-as-credit' => ':amount sera conservé comme crédit pour ce client',
        ],
        'fields'       => [
            'paid-how'               => 'Comment a-t-il été payé ?',
            'new-way'                => 'Nouveau moyen de paiement (ex. Wave, carte)',
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
