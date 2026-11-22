<?php

return [
    'create-sale-order' => [
        'label'  => 'Crear pedido de venta',
        'helper' => 'Al confirmar la factura se crea a partir de ella un pedido de venta confirmado, con entrega para los productos almacenables, para que aparezca en Ventas e Inventario.',
    ],

    'linked-order' => [
        'label' => 'Pedido de venta',
    ],
    'goods-delivered-now' => [
        'label'  => 'El cliente se ha llevado la mercancía',
        'helper' => 'Registra la entrega como hecha al confirmar la factura. Desmarque para dejarla al almacén.',
    ],

    'delivery' => [
        'label'               => 'Entrega',
        'waiting'             => 'Pendiente del almacén',
        'done'                => 'Entregada',
        'mark-delivered'      => 'Marcar como entregada',
        'confirm-heading'     => '¿Registrar la entrega?',
        'confirm-description' => 'Los artículos de esta factura se registrarán como entregados y saldrán del stock. Los artículos sin stock suficiente quedan pendientes.',
        'delivered'           => 'Entrega registrada.',
        'not-enough-stock'    => 'Algunos artículos no tienen stock; siguen pendientes en Inventario.',
    ],
];
