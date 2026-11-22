<?php

return [
    'title' => 'Gestionar presupuesto y pedido',

    'breadcrumb' => 'Gestionar presupuesto y pedido',

    'navigation' => [
        'title' => 'Gestionar presupuesto y pedido',
    ],

    'form' => [
        'fields' => [
            'validity-suffix'             => 'días',
            'validity'                    => 'Validez predeterminada del presupuesto',
            'validity-help'               => 'El número predeterminado de días durante los cuales un presupuesto es válido.',
            'lock-confirm-sales'          => 'Bloquear ventas confirmadas',
            'lock-confirm-sales-help'     => 'Si está habilitado, el pedido de venta se bloqueará tras su confirmación.',
            'goods-leave-on-invoice'      => 'Los clientes se llevan la mercancía al facturar',
            'goods-leave-on-invoice-help' => 'Valor predeterminado para las facturas que crean un pedido de venta. Activado: la entrega se registra como hecha al confirmar la factura. Desactivado: la entrega queda pendiente de validar por el almacén.',
        ],
    ],
];
