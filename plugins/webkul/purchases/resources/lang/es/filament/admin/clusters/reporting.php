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

    'by-product' => [
        'title'      => 'Compras por producto',
        'subheading' => 'Líneas de órdenes de compra confirmadas en la moneda de la empresa. Agrupe por producto o categoría para ver cantidades pedidas y recibidas.',
        'export'     => 'Exportar',
        'sum'        => 'Total',
        'columns'    => [
            'product'  => 'Producto',
            'category' => 'Categoría',
            'order'    => 'Orden de compra',
            'date'     => 'Fecha',
            'supplier' => 'Proveedor',
            'ordered'  => 'Pedido',
            'received' => 'Recibido',
            'untaxed'  => 'Sin impuestos',
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
