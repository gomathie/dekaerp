<?php

return [
    'navigation' => [
        'title' => 'Paramètres de présence',
    ],
    'title'      => 'Paramètres de présence',
    'subheading' => 'Présence pour :company. Activez-la ici, puis les employés pointent depuis le tableau de bord.',
    'no-company' => 'Choisissez une société pour gérer ses paramètres de présence.',
    'sections'   => [
        'status' => 'Statut',
        'rules'  => 'Règles',
    ],
    'status' => [
        'enabled'  => 'Activée pour cette société',
        'disabled' => 'Désactivée pour cette société',
    ],
    'fields' => [
        'auto-checkout'         => 'Départ automatique après',
        'auto-checkout-help'    => 'Un départ oublié est clôturé après ce nombre d\'heures et signalé pour vérification.',
        'capture-location'      => 'Position à l\'arrivée',
        'capture-location-help' => 'Désactivée : rien n\'est enregistré. Facultative : enregistrée si l\'appareil la partage. Obligatoire : le pointage l\'exige.',
        'late-tolerance'        => 'En retard après',
        'overtime-tolerance'    => 'Heures supplémentaires après',
    ],
    'units' => [
        'hours'   => 'heures',
        'minutes' => 'minutes',
    ],
    'actions' => [
        'save'   => 'Enregistrer',
        'enable' => [
            'label'        => 'Activer',
            'heading'      => 'Activer la présence ?',
            'description'  => 'Les employés de cette société ayant une fiche employé peuvent pointer depuis le tableau de bord. Les responsables voient leur équipe.',
            'notification' => 'La présence est activée pour cette société.',
        ],
        'disable' => [
            'label'        => 'Désactiver',
            'heading'      => 'Désactiver la présence ?',
            'description'  => 'Le pointage s\'arrête et les écrans de présence sont masqués. Les enregistrements existants sont conservés.',
            'notification' => 'La présence est désactivée pour cette société.',
        ],
    ],
    'notifications' => [
        'saved' => 'Paramètres de présence enregistrés.',
    ],
];
