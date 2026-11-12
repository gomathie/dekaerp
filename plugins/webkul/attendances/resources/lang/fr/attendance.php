<?php

return [
    'actions' => [
        'clock-in'  => 'Pointer l\'arrivée',
        'clock-out' => 'Pointer le départ',
        'correct'   => 'Corriger la présence',
        'export'    => 'Exporter la présence',
    ],
    'capture' => [
        'off'      => 'Désactivée',
        'optional' => 'Facultative',
        'required' => 'Obligatoire',
    ],
    'exceptions' => [
        'not-enabled' => 'La présence n\'est pas activée pour la société :company.',
    ],
    'notifications' => [
        'failed' => 'L\'opération a échoué. La modification n\'a pas été enregistrée.',
    ],
    'statuses' => [
        'clocked-in'     => 'Pointé depuis :time',
        'not-clocked-in' => 'Non pointé',
        'on-leave'       => 'En congé',
    ],
    'validation' => [
        'check-out-order'     => 'Le départ doit être postérieur à l\'arrivée.',
        'location-required'   => 'Une position est requise pour pointer.',
        'no-employee'         => 'Vous n\'avez pas de fiche employé dans cette société.',
        'not-allowed'         => 'Vous ne pouvez corriger que la présence de votre équipe.',
        'not-clocked-in'      => 'Vous n\'êtes pas pointé.',
        'not-your-attendance' => 'Vous ne pouvez pointer que pour vous-même.',
        'reason-required'     => 'Une correction doit avoir un motif.',
    ],
];
