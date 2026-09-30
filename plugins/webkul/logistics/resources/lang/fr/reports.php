<?php

$dateFilters = [
    'from'    => 'Du',
    'until'   => 'Au',
    'between' => 'Du :from au :until',
    'since'   => 'Depuis le :from',
    'up-to'   => 'Jusqu’au :until',
];

/*
 * Vehicle and driver trip history are the same table with a different filter in
 * front, so they share their column and filter labels rather than keeping two
 * copies to drift apart. TripHistoryExporter labels its file from the
 * vehicle-trips keys whichever page it was started from, which is only correct
 * while the two sets are identical - keep them so.
 */
$tripColumns = [
    'reference' => 'Trajet',
    'vehicle'   => 'Véhicule',
    'driver'    => 'Chauffeur',
    'state'     => 'Statut',
    'started'   => 'Démarré',
    'ended'     => 'Terminé',
    'shipments' => 'Expéditions',
    'distance'  => 'Distance',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'Véhicule',
    'driver'  => 'Chauffeur',
    'state'   => 'Statut',
];

return [
    'export'          => 'Exporter',
    'export-complete' => ':count lignes exportées.',

    'shipment-register' => [
        'title'  => 'Registre des expéditions',
        'export' => 'Exporter',

        'columns' => [
            'reference'   => 'Expédition',
            'customer'    => 'Client',
            'state'       => 'Statut',
            'origin'      => 'De',
            'destination' => 'À',
            'expected'    => 'Prévue',
            'delivered'   => 'Livrée',
            'company'     => 'Société',
        ],

        'filters' => $dateFilters + [
            'state'    => 'Statut',
            'customer' => 'Client',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'Performance des livraisons',
        'subheading' => 'Mesurée par rapport à la date de livraison prévue. Les expéditions sans date prévue ne sont pas évaluées.',
        'export'     => 'Exporter',

        'columns' => [
            'reference' => 'Expédition',
            'customer'  => 'Client',
            'expected'  => 'Prévue',
            'delivered' => 'Livrée',
            'outcome'   => 'Résultat',
            'delay'     => 'Retard de',
        ],

        'outcomes' => [
            'on-time'      => 'À l’heure',
            'late'         => 'En retard',
            'failed'       => 'Échec',
            'overdue'      => 'Échue',
            'pending'      => 'En cours',
            'not-measured' => 'Aucune date promise',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'Client',
            'late-only'   => 'En retard uniquement',
            'failed-only' => 'Échecs uniquement',
        ],
    ],

    'profitability' => [
        'title'      => 'Rentabilité des expéditions',
        'subheading' => 'Frais facturables comparés aux coûts approuvés, chacun dans la devise de l’expédition. Les coûts en brouillon ne sont pas comptabilisés.',
        'export'     => 'Exporter',

        'columns' => [
            'reference' => 'Expédition',
            'customer'  => 'Client',
            'revenue'   => 'Revenus',
            'costs'     => 'Coûts',
            'margin'    => 'Marge',
            'currency'  => 'Devise',
        ],

        'filters' => $dateFilters + [
            'customer'     => 'Client',
            'loss-making'  => 'Déficitaires uniquement',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'Historique des trajets du véhicule',
        'export'  => 'Exporter',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'Historique des trajets du chauffeur',
        'export'  => 'Exporter',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];
