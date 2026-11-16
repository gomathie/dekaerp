<?php

return [
    'navigation' => [
        'title' => 'Véhicules',
    ],

    'form' => [
        'fields' => [
            'registration-no'            => 'Numéro d’immatriculation',
            'name'                       => 'Nom',
            'company'                    => 'Société',
            'vehicle-type'               => 'Type de véhicule',
            'ownership'                  => 'Propriété',
            'carrier'                    => 'Transporteur',
            'default-driver'             => 'Chauffeur par défaut',
            'equipment'                  => 'Équipement de maintenance',
            'telematics-device-ref'      => 'Référence du dispositif télématique',
            'telematics-agent-id'        => 'ID d’agent télématique',
            'telematics-agent-id-helper' => 'L’identifiant propre de ce véhicule sur la plateforme télématique, qui n’est pas l’IMEI du dispositif. PILOT l’appelle ID d’agent.',
            'capacity-kg'                => 'Capacité en poids',
            'capacity-m3'                => 'Capacité en volume',
            'is-active'                  => 'Actif',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'Immatriculation',
            'name'                  => 'Nom',
            'ownership'             => 'Propriété',
            'vehicle-type'          => 'Type',
            'carrier'               => 'Transporteur',
            'capacity-kg'           => 'Capacité',
            'telematics-device-ref' => 'Télématique',
            'telematics-agent-id'   => 'Agent télématique',
            'company'               => 'Société',
            'is-active'             => 'Actif',
        ],

        'filters' => [
            'ownership'    => 'Propriété',
            'vehicle-type' => 'Type de véhicule',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'Véhicule',
            'capacity'     => 'Capacité',
            'integrations' => 'Intégrations',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'Importer depuis les équipements de Maintenance',

            'fields' => [
                'equipment' => 'Équipement',
            ],

            'notification' => [
                'title' => 'Importation des équipements de maintenance terminée',
                'body'  => ':created créés, :skipped ignorés.',
            ],
        ],
    ],
];
