<?php

return [
    'unknown'             => 'Inconnu',
    'other'               => 'Autres',
    'share'               => 'Part',
    'status'              => 'Statut',
    'delivery-status'     => 'Statut de livraison',
    'billing'             => 'Facturation',
    'sales-value'         => 'Commandes confirmées et factures établies sans commande',
    'stats'               => [
        'heading'            => 'Indicateurs Clés',
        'total-quotations'   => 'Total des Devis',
        'total-sales-orders' => 'Total des Ventes',
        'total-revenue'      => 'Revenu Total',
        'average-revenue'    => 'Revenu Moyen',
        'increase'           => 'augmentation',
        'decrease'           => 'diminution',
    ],

    'order-activity'      => [
        'heading'     => 'Suivi des commandes',
        'description' => 'Commandes confirmées sur la période, selon leur état actuel',
        'to-deliver'  => 'À livrer',
        'delivered'   => 'Livrées',
        'to-invoice'  => 'À facturer',
        'invoiced'    => 'Facturées',
        'share'       => ':percent% de :total commandes',
    ],

    'monthly-sales'       => [
        'heading'  => 'Ventes dans le temps',
        'revenue'  => 'Revenu',
        'previous' => 'Période comparée',
    ],

    'revenue-by-category' => [
        'heading' => 'Revenu par Catégorie',
        'revenue' => 'Revenu',
    ],

    'sales-persons'       => [
        'heading' => 'Performance des Commerciaux',
        'revenue' => 'Revenu',
    ],

    'quotation-outcome'   => [
        'heading'     => 'Devis',
        'description' => 'Ce que sont devenus les devis faits sur la période',
        'won'         => 'Devenus commandes',
        'open'        => 'Encore ouverts',
        'cancelled'   => 'Annulés',
        'count'       => 'Devis',
    ],

    'top-products'        => [
        'heading' => 'Meilleurs Produits',
        'columns' => [
            'name'     => 'Produit',
            'quantity' => 'Quantité Vendue',
            'revenue'  => 'Revenu',
        ],
    ],

    'top-customers'       => [
        'heading' => 'Meilleurs Clients',
        'columns' => [
            'name'    => 'Client',
            'orders'  => 'Ventes',
            'revenue' => 'Revenu',
        ],
    ],

    'top-sales-orders'    => [
        'heading' => 'Meilleures Ventes',
        'columns' => [
            'reference'      => 'Numéro de commande',
            'customer'       => 'Client',
            'amount'         => 'Montant total',
            'invoice-status' => 'Statut de facture',
        ],
    ],

    'top-quotations'      => [
        'heading' => 'Meilleurs Devis',
        'columns' => [
            'reference' => 'Référence',
            'customer'  => 'Client',
            'amount'    => 'Montant',
        ],
    ],
];
