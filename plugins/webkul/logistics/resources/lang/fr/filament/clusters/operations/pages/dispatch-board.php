<?php

return [
    'title' => 'Tableau d’expédition',

    'tabs' => [
        'unassigned'      => 'Non affectées',
        'awaiting-pickup' => 'En attente d’enlèvement',
        'active'          => 'Trajets actifs',
        'due-today'       => 'Prévues aujourd’hui',
        'overdue'         => 'En retard',
        'failed'          => 'Échecs',
    ],

    'columns' => [
        'number'               => 'Expédition',
        'state'                => 'Statut',
        'customer'             => 'Client',
        'destination'          => 'Destination',
        'expected-delivery-at' => 'Livraison prévue',
    ],
];
