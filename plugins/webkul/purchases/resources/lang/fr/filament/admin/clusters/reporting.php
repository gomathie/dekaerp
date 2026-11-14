<?php

return [
    'navigation'      => 'Rapports',
    'export-complete' => 'Votre export des achats est prêt : :count lignes.',

    'analysis' => [
        'title'      => 'Analyse des achats',
        'subheading' => 'Bons de commande confirmés, en devise de la société. Regroupez par fournisseur, acheteur ou mois pour voir les totaux.',
        'export'     => 'Exporter',
        'sum'        => 'Total',
        'columns'    => [
            'reference' => 'Commande',
            'date'      => 'Date',
            'supplier'  => 'Fournisseur',
            'buyer'     => 'Acheteur',
            'untaxed'   => 'Hors taxes',
            'total'     => 'Total',
        ],
        'groups' => [
            'supplier' => 'Fournisseur',
            'buyer'    => 'Acheteur',
            'month'    => 'Mois',
        ],
        'filters' => [
            'from'  => 'Du',
            'until' => 'Au',
        ],
    ],

    'by-product' => [
        'title'      => 'Achats par produit',
        'subheading' => 'Lignes des bons de commande confirmés, en devise de la société. Regroupez par produit ou catégorie pour voir les quantités commandées et reçues.',
        'export'     => 'Exporter',
        'sum'        => 'Total',
        'columns'    => [
            'product'  => 'Produit',
            'category' => 'Catégorie',
            'order'    => 'Bon de commande',
            'date'     => 'Date',
            'supplier' => 'Fournisseur',
            'ordered'  => 'Commandé',
            'received' => 'Reçu',
            'untaxed'  => 'Hors taxes',
        ],
        'groups' => [
            'product'  => 'Produit',
            'category' => 'Catégorie',
        ],
        'filters' => [
            'from'  => 'Du',
            'until' => 'Au',
        ],
    ],
];
