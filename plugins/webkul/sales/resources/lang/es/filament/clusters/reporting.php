<?php

return [
    'navigation'      => 'Informes',
    'export-complete' => 'Su exportación de ventas está lista: :count filas.',

    'analysis' => [
        'title'      => 'Análisis de ventas',
        'subheading' => 'Pedidos confirmados en la moneda de la empresa. Agrupe por cliente, vendedor o mes para ver los totales.',
        'export'     => 'Exportar',
        'sum'        => 'Total',
        'columns'    => [
            'reference'   => 'Pedido',
            'date'        => 'Fecha',
            'customer'    => 'Cliente',
            'salesperson' => 'Vendedor',
            'untaxed'     => 'Sin impuestos',
            'total'       => 'Total',
        ],
        'groups' => [
            'customer'    => 'Cliente',
            'salesperson' => 'Vendedor',
            'month'       => 'Mes',
        ],
        'filters' => [
            'from'  => 'Desde',
            'until' => 'Hasta',
        ],
    ],
];
