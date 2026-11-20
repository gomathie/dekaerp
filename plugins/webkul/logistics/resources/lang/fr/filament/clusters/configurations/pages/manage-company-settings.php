<?php

return [
    'navigation' => [
        'title' => 'Paramètres',
    ],

    'title'      => 'Paramètres de logistique',
    'subheading' => 'Paramètres pour :company. La logistique est activée et désactivée séparément pour chaque société.',
    'no-company' => 'Sélectionnez d’abord une société : les paramètres de logistique sont conservés par société.',

    'sections' => [
        'status'     => 'Statut',
        'delivery'   => 'Preuve de livraison',
        'operations' => 'Opérations',
        'accounting' => 'Comptabilité',
    ],

    'status' => [
        'enabled'  => 'La logistique est activée pour cette société',
        'disabled' => 'La logistique n’est pas activée pour cette société',
    ],

    'readiness' => [
        'label' => 'État de préparation',
        'ok'    => 'Tout ce dont la logistique a besoin est en place.',
    ],

    'fields' => [
        'require-pod-for-delivery'    => 'Exiger une preuve de livraison pour marquer une expédition comme livrée',
        'require-pod-photo'           => 'Exiger une photo avec la preuve de livraison',
        'stop-link-ttl-hours'         => 'Validité du lien d’arrêt (heures)',
        'default-service-type'        => 'Type de service par défaut',
        'capacity-check'              => 'Contrôle de la capacité du véhicule',
        'overdue-grace-minutes'       => 'En retard après (minutes suivant l’heure prévue)',
        'free-waiting-minutes'        => 'Temps d’attente gratuit à un arrêt (minutes)',
        'free-waiting-minutes-helper' => 'Au-delà, des frais d’attente sont suggérés. Ils ne sont jamais ajoutés automatiquement.',
        'invoice-journal'             => 'Journal des factures clients',
        'bill-journal'                => 'Journal des factures fournisseurs',
        'default-expense-account'     => 'Compte de dépenses par défaut',
        'expense-approval-required'   => 'Les dépenses doivent être approuvées avant facturation',
    ],

    'actions' => [
        'save' => 'Enregistrer',

        'enable' => [
            'label'                     => 'Activer la logistique',
            'heading'                   => 'Activer la logistique pour cette société ?',
            'description'               => 'Les menus et enregistrements de logistique deviennent accessibles aux utilisateurs de cette société qui disposent des autorisations de logistique.',
            'description-with-problems' => 'La logistique peut être activée, mais certaines fonctions resteront indisponibles tant que les problèmes suivants ne seront pas corrigés : :problems',
            'notification'              => 'Logistique activée',
        ],

        'disable' => [
            'label'        => 'Désactiver la logistique',
            'heading'      => 'Désactiver la logistique pour cette société ?',
            'description'  => 'La logistique est masquée et aucun nouvel enregistrement ne peut être créé pour cette société. Les données existantes sont conservées et une nouvelle activation rétablit l’accès.',
            'notification' => 'Logistique désactivée',
        ],
    ],

    'notifications' => [
        'saved' => 'Paramètres enregistrés',
    ],
];
