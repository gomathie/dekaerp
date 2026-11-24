<?php

return [
    'base-currency' => [
        'title'       => 'Moneda base',
        'description' => 'Cambiar la moneda contable de una empresa que ya tiene asientos. Solo Super Admin; queda registrado en el registro de actividad.',
        'action'      => 'Cambiar moneda base',
        'warning'     => 'Esta empresa tiene :count asientos publicados. Conservan su moneda y su tipo de cambio, pero los informes usarán a partir de ahora la nueva moneda base. Se registran el cambio, su nombre y su motivo.',
        'changed'     => 'Moneda base cambiada',
        'fields'      => [
            'currency' => 'Nueva moneda base',
            'reason'   => 'Motivo',
        ],
    ],

    'sale-currency' => [
        'title'       => 'Pregunta de moneda en las facturas nuevas',
        'description' => 'Para empresas que venden en más de una moneda.',
        'field'       => 'Preguntar la moneda de cada venta',
        'helper'      => 'Una factura nueva pregunta primero la moneda y, si es extranjera, hace confirmar el tipo del día. Solo se pregunta si la empresa tiene más de una moneda.',
    ],
];
