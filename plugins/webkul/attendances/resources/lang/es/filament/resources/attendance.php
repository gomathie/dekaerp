<?php

return [
    'model' => [
        'label'  => 'Registro de asistencia',
        'plural' => 'Asistencia',
    ],
    'navigation' => [
        'title' => 'Asistencia',
    ],
    'table' => [
        'columns' => [
            'employee'   => 'Empleado',
            'check-in'   => 'Entrada',
            'check-out'  => 'Salida',
            'worked'     => 'Trabajado',
            'review'     => 'Requiere revisión',
            'on-leave'   => 'De permiso',
            'source'     => 'Origen entrada',
            'source-out' => 'Origen salida',
            'company'    => 'Empresa',
            'note'       => 'Nota',
            'latitude'   => 'Latitud (entrada)',
            'longitude'  => 'Longitud (entrada)',
            'ip'         => 'Dirección IP (entrada)',
        ],
        'filters' => [
            'open'         => 'Fichados ahora',
            'needs-review' => 'Requiere revisión',
            'employee'     => 'Empleado',
            'day'          => 'Día',
            'on'           => 'El',
        ],
    ],
    'actions' => [
        'export'  => 'Exportar',
        'correct' => [
            'label'        => 'Corregir asistencia',
            'heading'      => 'Corregir esta asistencia',
            'description'  => 'El cambio se registra con su nombre y el motivo indicado.',
            'fields'       => [
                'check-in'    => 'Entrada',
                'check-out'   => 'Salida',
                'note'        => 'Nota',
                'reason'      => 'Motivo',
                'reason-help' => 'Obligatorio. Indique qué estaba mal y qué cambió.',
            ],
            'notification' => 'Asistencia corregida.',
        ],
    ],
    'export-complete' => ':count fila(s) de asistencia exportada(s).',
];
