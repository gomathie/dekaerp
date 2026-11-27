<?php

return [
    'title' => 'Confirmar',

    'currency-summary' => [
        'total'       => 'Total :amount = :value a :rate.',
        'rate-date'   => 'Este tipo de cambio es del :date (la fecha del documento), no de hoy.',
        'question'    => '¿Confirmar y contabilizar?',
        'latest'      => 'Al tipo de cambio más reciente, :rate del :date, sería :value.',
        'use-latest'  => 'Contabilizar al tipo más reciente',
        'latest-note' => 'Contabilizado al tipo de cambio más reciente, :rate del :date, en lugar del tipo de la fecha del documento.',
    ],

    'unused-credit' => [
        'question'   => 'Este cliente tiene :amount sin usar. ¿Aplicarlo?',
        'apply'      => 'Aplicar',
        'dont-apply' => 'No aplicar',
    ],

    'notification' => [
        'error' => [
            'title' => 'Error de confirmación',
        ],
    ],
];
