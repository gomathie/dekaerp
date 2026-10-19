<?php

return [
    'navigation' => [
        'title' => 'Trajets',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'Véhicule et équipe',
            'schedule' => 'Planning',
        ],

        'fields' => [
            'company'            => 'Société',
            'number'             => 'Numéro du trajet',
            'number-placeholder' => 'Attribué automatiquement',
            'vehicle'            => 'Véhicule',
            'driver'             => 'Chauffeur',
            'dispatcher'         => 'Agent d’exploitation',
            'planned-start-at'   => 'Début planifié',
            'planned-end-at'     => 'Fin planifiée',
            'odometer-start'     => 'Compteur kilométrique au départ',
            'odometer-end'       => 'Compteur kilométrique à l’arrivée',
            'notes'              => 'Notes',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'Trajet',
            'state'            => 'Statut',
            'vehicle'          => 'Véhicule',
            'driver'           => 'Chauffeur',
            'shipments'        => 'Expéditions',
            'planned-start-at' => 'Début planifié',
            'company'          => 'Société',
        ],

        'filters' => [
            'state'   => 'Statut',
            'vehicle' => 'Véhicule',
            'driver'  => 'Chauffeur',
        ],
    ],

    'infolist' => [
        'summary'         => 'Trajet',
        'schedule'        => 'Planning',
        'load'            => 'Chargement',
        'actual-start-at' => 'Début réel',
        'actual-end-at'   => 'Fin réelle',
        'capacity'        => 'Capacité',
        'capacity-ok'     => 'Dans les limites de capacité du véhicule.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'Expéditions',
            'columns' => [
                'number'   => 'Expédition',
                'state'    => 'Statut',
                'customer' => 'Client',
                'weight'   => 'Poids (kg)',
                'volume'   => 'Volume (m³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'Ajouter une expédition',
                    'field'        => 'Expédition confirmée',
                    'notification' => 'Expédition ajoutée à ce trajet.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'Expédier',
            'heading'      => 'Expédier ce trajet ?',
            'notification' => 'Trajet expédié.',
        ],
        'startTrip' => [
            'label'        => 'Démarrer',
            'heading'      => 'Démarrer ce trajet ?',
            'notification' => 'Trajet démarré.',
        ],
        'completeTrip' => [
            'label'        => 'Terminer',
            'heading'      => 'Terminer ce trajet ?',
            'notification' => 'Trajet terminé.',
        ],
    ],
];
