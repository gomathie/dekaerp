<?php

return [
    'title' => 'Revertir',

    'modal' => [
        'heading' => 'Crear reversión',

        'form' => [
            'reason'  => 'Motivo mostrado en la reversión',
            'journal' => 'Diario',
            'date'    => 'Fecha',
        ],
    ],

    'one-step' => [
        'scope'               => 'Qué acreditar',
        'whole'               => 'Toda la factura',
        'lines'               => 'Algunas líneas',
        'line-list'           => 'Líneas',
        'disposition'         => 'Después',
        'apply'               => 'Descontarla de la factura',
        'refund'              => 'Devolver el dinero',
        'credit'              => 'Guardarla como crédito',
        'saved'               => 'Nota de crédito contabilizada.',
        'history'             => 'Nota de crédito contabilizada (:disposition). Motivo: :reason',
        'reason'              => 'El motivo es obligatorio.',
        'invoice'             => 'Solo una factura de cliente contabilizada se puede acreditar así.',
        'lines-invalid'       => 'Elija líneas de esta factura.',
        'disposition-invalid' => 'Elija qué hacer con la nota de crédito.',
        'not-allowed'         => 'No tiene permiso para devolver el dinero.',
        'company'             => 'No puede acreditar una factura de esa empresa.',
        'journal'             => 'Elija un diario de banco, caja o tarjeta de esta empresa.',
        'not-settled'         => 'El dinero no se devolvió.',
        'not-applied'         => 'La nota de crédito no se descontó de la factura.',
    ],
];
