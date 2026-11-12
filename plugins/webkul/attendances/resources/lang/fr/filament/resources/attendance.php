<?php

return [
    'model' => [
        'label'  => 'Pointage',
        'plural' => 'Pointages',
    ],
    'navigation' => [
        'title' => 'Pointage',
    ],
    'table' => [
        'columns' => [
            'employee'   => 'Employé',
            'check-in'   => 'Arrivée',
            'check-out'  => 'Départ',
            'worked'     => 'Travaillé',
            'review'     => 'À vérifier',
            'on-leave'   => 'En congé',
            'source'     => 'Source arrivée',
            'source-out' => 'Source départ',
            'company'    => 'Société',
            'note'       => 'Note',
            'latitude'   => 'Latitude (arrivée)',
            'longitude'  => 'Longitude (arrivée)',
            'ip'         => 'Adresse IP (arrivée)',
        ],
        'filters' => [
            'open'         => 'Pointés maintenant',
            'needs-review' => 'À vérifier',
            'employee'     => 'Employé',
            'day'          => 'Jour',
            'on'           => 'Le',
        ],
    ],
    'actions' => [
        'export'  => 'Exporter',
        'correct' => [
            'label'        => 'Corriger le pointage',
            'heading'      => 'Corriger ce pointage',
            'description'  => 'La modification est enregistrée avec votre nom et le motif indiqué.',
            'fields'       => [
                'check-in'    => 'Arrivée',
                'check-out'   => 'Départ',
                'note'        => 'Note',
                'reason'      => 'Motif',
                'reason-help' => 'Obligatoire. Indiquez ce qui était faux et ce que vous avez corrigé.',
            ],
            'notification' => 'Pointage corrigé.',
        ],
    ],
    'export-complete' => ':count ligne(s) de pointage exportée(s).',
];
