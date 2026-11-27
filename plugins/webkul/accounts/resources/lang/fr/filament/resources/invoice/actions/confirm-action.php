<?php

return [
    'title' => 'Confirmer',

    'currency-summary' => [
        'total'       => 'Total :amount = :value au taux de :rate.',
        'rate-date'   => 'Ce taux est celui du :date (date du document), pas celui d\'aujourd\'hui.',
        'question'    => 'Confirmer et comptabiliser ?',
        'latest'      => 'Au taux le plus récent, :rate du :date, ce serait :value.',
        'use-latest'  => 'Comptabiliser au taux le plus récent',
        'latest-note' => 'Comptabilisé au taux le plus récent, :rate du :date, au lieu du taux de la date du document.',
    ],

    'unused-credit' => [
        'question'   => 'Ce client a :amount non utilisé. L\'appliquer ?',
        'apply'      => 'Appliquer',
        'dont-apply' => 'Ne pas appliquer',
    ],

    'notification' => [
        'error' => [
            'title' => 'Erreur de confirmation',
        ],
    ],
];
