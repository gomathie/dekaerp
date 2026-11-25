<?php

return [
    'navigation'      => 'Informes',
    'export-complete' => 'Su exportación de ventas está lista: :count filas.',

    'analysis' => [
        'title'      => 'Análisis de ventas',
        'subheading' => 'Pedidos confirmados y facturas sin pedido, en la moneda de la empresa. Agrupe por cliente, vendedor o mes para ver los totales.',
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

    'by-product' => [
        'title'      => 'Ventas por producto',
        'subheading' => 'Líneas de pedidos confirmados y de facturas sin pedido, en la moneda de la empresa. Agrupe por producto o categoría para ver cantidades y totales.',
        'export'     => 'Exportar',
        'sum'        => 'Total',
        'columns'    => [
            'product'  => 'Producto',
            'category' => 'Categoría',
            'order'    => 'Pedido o factura',
            'date'     => 'Fecha',
            'customer' => 'Cliente',
            'quantity' => 'Cantidad',
            'untaxed'  => 'Sin impuestos',
            'none'     => 'Sin producto',
        ],
        'groups' => [
            'product'  => 'Producto',
            'category' => 'Categoría',
        ],
        'filters' => [
            'from'  => 'Desde',
            'until' => 'Hasta',
        ],
    ],
];
