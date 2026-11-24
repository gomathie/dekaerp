<?php

return [
    'base-currency' => [
        'title'       => 'Devise de base',
        'description' => 'Changer la devise comptable d’une société qui a déjà des écritures. Réservé au Super Admin et enregistré dans le journal d’activité.',
        'action'      => 'Changer la devise de base',
        'warning'     => 'Cette société a :count écritures comptabilisées. Elles gardent leur devise et leur taux, mais les rapports utiliseront désormais la nouvelle devise de base. Le changement, votre nom et votre raison sont enregistrés.',
        'changed'     => 'Devise de base changée',
        'fields'      => [
            'currency' => 'Nouvelle devise de base',
            'reason'   => 'Raison',
        ],
    ],

    'sale-currency' => [
        'title'       => 'Question de devise sur les nouvelles factures',
        'description' => 'Pour les sociétés qui vendent dans plusieurs devises.',
        'field'       => 'Demander la devise de chaque vente',
        'helper'      => 'Une nouvelle facture, un nouveau devis ou une nouvelle commande demande d\'abord la devise et, pour une devise étrangère, fait confirmer le taux du jour. Posée seulement si la société a plusieurs devises.',
    ],
];
