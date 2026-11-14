<?php

return [
    'base-currency' => [
        'title'       => 'Devise de base',
        'description' => 'Changer la devise comptable d’une société qui a déjà des écritures. Réservé au Super Admin et enregistré dans le journal d’activité.',
        'action'      => 'Changer la devise de base',
        'warning'     => 'Cette société a :count écritures comptabilisées. Elles gardent leur devise et leur taux, mais les rapports utiliseront désormais la nouvelle devise de base. Le changement, votre nom et votre raison sont enregistrés.',
        'changed'     => 'Devise de base changée',
        'fields'      => [
            'currency' => 'Nouvelle devise de base',
            'reason'   => 'Raison',
        ],
    ],
];
