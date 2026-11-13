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
];
