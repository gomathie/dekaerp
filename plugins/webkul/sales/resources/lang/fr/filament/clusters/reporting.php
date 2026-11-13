<?php

return [
    'navigation'      => 'Rapports',
    'export-complete' => 'Votre export des ventes est prêt : :count lignes.',

    'analysis' => [
        'title'      => 'Analyse des ventes',
        'subheading' => 'Commandes confirmées, en devise de la société. Regroupez par client, vendeur ou mois pour voir les totaux.',
        'export'     => 'Exporter',
        'sum'        => 'Total',
        'columns'    => [
            'reference'   => 'Commande',
            'date'        => 'Date',
            'customer'    => 'Client',
            'salesperson' => 'Vendeur',
            'untaxed'     => 'Hors taxes',
            'total'       => 'Total',
        ],
        'groups' => [
            'customer'    => 'Client',
            'salesperson' => 'Vendeur',
            'month'       => 'Mois',
        ],
        'filters' => [
            'from'  => 'Du',
            'until' => 'Au',
        ],
    ],
];
