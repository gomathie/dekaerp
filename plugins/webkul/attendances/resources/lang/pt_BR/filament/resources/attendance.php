<?php

return [
    'model' => [
        'label'  => 'Registo de presença',
        'plural' => 'Presença',
    ],
    'navigation' => [
        'title' => 'Presença',
    ],
    'table' => [
        'columns' => [
            'employee'   => 'Colaborador',
            'check-in'   => 'Entrada',
            'check-out'  => 'Saída',
            'worked'     => 'Trabalhado',
            'review'     => 'Requer revisão',
            'on-leave'   => 'De licença',
            'source'     => 'Origem entrada',
            'source-out' => 'Origem saída',
            'company'    => 'Empresa',
            'note'       => 'Nota',
            'latitude'   => 'Latitude (entrada)',
            'longitude'  => 'Longitude (entrada)',
            'ip'         => 'Endereço IP (entrada)',
        ],
        'filters' => [
            'open'         => 'Registados agora',
            'needs-review' => 'Requer revisão',
            'employee'     => 'Colaborador',
            'day'          => 'Dia',
            'on'           => 'Em',
        ],
    ],
    'actions' => [
        'export'  => 'Exportar',
        'correct' => [
            'label'        => 'Corrigir presença',
            'heading'      => 'Corrigir esta presença',
            'description'  => 'A alteração fica registada com o seu nome e o motivo indicado.',
            'fields'       => [
                'check-in'    => 'Entrada',
                'check-out'   => 'Saída',
                'note'        => 'Nota',
                'reason'      => 'Motivo',
                'reason-help' => 'Obrigatório. Diga o que estava errado e o que alterou.',
            ],
            'notification' => 'Presença corrigida.',
        ],
    ],
    'export-complete' => ':count linha(s) de presença exportada(s).',
];
