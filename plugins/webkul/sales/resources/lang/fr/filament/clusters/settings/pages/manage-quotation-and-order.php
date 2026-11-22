<?php

return [
    'title' => 'Gérer les devis et commandes',

    'breadcrumb' => 'Gérer les devis et commandes',

    'navigation' => [
        'title' => 'Gérer les devis et commandes',
    ],

    'form' => [
        'fields' => [
            'validity-suffix'             => 'jours',
            'validity'                    => 'Validité par défaut du devis',
            'validity-help'               => 'Le nombre de jours par défaut pendant lesquels un devis est valide.',
            'lock-confirm-sales'          => 'Verrouiller après confirmation des ventes',
            'lock-confirm-sales-help'     => 'Si activé, la commande client sera verrouillée après confirmation.',
            'goods-leave-on-invoice'      => 'Les clients emportent les marchandises à la facturation',
            'goods-leave-on-invoice-help' => 'Valeur par défaut des factures qui créent une commande client. Activé : la livraison est enregistrée comme faite à la confirmation de la facture. Désactivé : la livraison reste à valider par l’entrepôt.',
        ],
    ],
];
