<?php

return [
    'navigation'      => 'Informes',
    'export-complete' => 'Su exportación de compras está lista: :count filas.',

    'analysis' => [
        'title'      => 'Análisis de compras',
        'subheading' => 'Pedidos de compra confirmados en la moneda de la empresa. Agrupe por proveedor, comprador o mes para ver los totales.',
        'export'     => 'Exportar',
        'sum'        => 'Total',
        'columns'    => [
            'reference' => 'Pedido',
            'date'      => 'Fecha',
            'supplier'  => 'Proveedor',
            'buyer'     => 'Comprador',
            'untaxed'   => 'Sin impuestos',
            'total'     => 'Total',
        ],
        'groups' => [
            'supplier' => 'Proveedor',
            'buyer'    => 'Comprador',
            'month'    => 'Mes',
        ],
        'filters' => [
            'from'  => 'Desde',
            'until' => 'Hasta',
        ],
    ],
];
