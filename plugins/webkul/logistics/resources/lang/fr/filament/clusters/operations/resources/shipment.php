<?php

return [
    'navigation' => [
        'title' => 'Expéditions',
    ],

    'global-search' => [
        'customer'  => 'Client',
        'state'     => 'Statut',
        'reference' => 'Référence client',
    ],

    'form' => [
        'tabs' => [
            'general'    => 'Général',
            'route'      => 'Itinéraire et arrêts',
            'cargo'      => 'Cargaison',
            'assignment' => 'Affectation',
        ],

        'fields' => [
            'number'                 => 'Numéro d’expédition',
            'number-placeholder'     => 'Attribué lors de l’enregistrement de l’expédition',
            'company'                => 'Société',
            'customer'               => 'Client',
            'customer-reference'     => 'Référence client',
            'sale-order'             => 'Depuis la commande client',
            'sale-order-helper'      => 'Copie la devise et la référence client depuis une commande client de ce client.',
            'service-type'           => 'Type de service',
            'transport-mode'         => 'Mode de transport',
            'priority'               => 'Priorité',
            'currency'               => 'Devise',
            'origin'                 => 'Origine',
            'destination'            => 'Destination',
            'pickup-address'         => 'Adresse d’enlèvement',
            'delivery-address'       => 'Adresse de livraison',
            'planned-pickup-at'      => 'Enlèvement planifié',
            'expected-delivery-at'   => 'Livraison prévue',
            'stops'                  => 'Arrêts',
            'stop-type'              => 'Type',
            'stop-address'           => 'Adresse',
            'stop-contact'           => 'Contact',
            'stop-phone'             => 'Téléphone',
            'stop-planned-arrival'   => 'Arrivée planifiée',
            'stop-instructions'      => 'Instructions',
            'declared-value'         => 'Valeur déclarée',
            'is-fragile'             => 'Fragile',
            'is-hazardous'           => 'Dangereux',
            'is-hazardous-helper'    => 'Indicateur uniquement. Les documents relatifs aux marchandises dangereuses ne sont pas gérés ici.',
            'cargo-lines'            => 'Cargaison',
            'line-description'       => 'Description',
            'line-package-type'      => 'Type de colis',
            'line-quantity'          => 'Quantité',
            'line-weight'            => 'Poids',
            'line-volume'            => 'Volume',
            'instructions'           => 'Instructions particulières',
            'dispatcher'             => 'Agent d’exploitation',
            'carrier'                => 'Transporteur',
            'carrier-helper'         => 'Pour le transport sous-traité. Les coûts du transporteur sont enregistrés comme dépenses.',
            'carrier-reference'      => 'Référence du transporteur',
            'waybill-no'             => 'Numéro de lettre de voiture',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'Numéro',
            'customer'             => 'Client',
            'state'                => 'Statut',
            'planned-pickup-at'    => 'Enlèvement planifié',
            'expected-delivery-at' => 'Livraison prévue',
            'service-type'         => 'Service',
            'packages'             => 'Colis',
            'dispatcher'           => 'Agent d’exploitation',
            'company'              => 'Société',
        ],

        'filters' => [
            'state'         => 'Statut',
            'customer'      => 'Client',
            'service-type'  => 'Type de service',
            'pickup-from'   => 'Enlèvement à partir du',
            'pickup-until'  => 'Enlèvement jusqu’au',
        ],
    ],

    'infolist' => [
        'summary'            => 'Expédition',
        'route'              => 'Itinéraire',
        'cargo'              => 'Cargaison',
        'assignment'         => 'Affectation',
        'actual-pickup-at'   => 'Enlèvement réel',
        'actual-delivery-at' => 'Livraison réelle',
        'total-weight'       => 'Poids total',
        'total-volume'       => 'Volume total',
    ],

    'timeline' => [
        'title'   => 'Chronologie',
        'columns' => [
            'occurred-at' => 'Date et heure',
            'type'        => 'Événement',
            'source'      => 'Source',
            'user'        => 'Par',
            'location'    => 'Emplacement',
            'notes'       => 'Notes',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'Confirmer',
            'heading'      => 'Confirmer cette expédition ?',
            'notification' => 'Expédition confirmée',
        ],

        'hold' => [
            'label'        => 'Mettre en attente',
            'heading'      => 'Mettre cette expédition en attente ?',
            'reason'       => 'Motif',
            'notification' => 'Expédition mise en attente',
        ],

        'release' => [
            'label'        => 'Libérer',
            'heading'      => 'Libérer cette expédition ?',
            'description'  => 'L’expédition repasse à l’état Confirmé et peut être de nouveau affectée à un trajet.',
            'notification' => 'Expédition libérée',
        ],

        'cancel' => [
            'label'        => 'Annuler l’expédition',
            'heading'      => 'Annuler cette expédition ?',
            'description'  => 'Une expédition annulée ne peut pas être rouverte. Ses frais sont conservés à titre de référence.',
            'reason'       => 'Motif',
            'notification' => 'Expédition annulée',
        ],
    ],
];
