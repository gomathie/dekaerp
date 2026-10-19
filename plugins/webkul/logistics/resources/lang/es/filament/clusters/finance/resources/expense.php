<?php

return [
    'navigation' => [
        'title' => 'Gastos',
    ],

    'form' => [
        'sections' => [
            'details'   => 'Detalles del gasto',
            'reference' => 'Referencia y recibo',
        ],
        'fields' => [
            'company'          => 'Empresa',
            'date'             => 'Fecha',
            'amount'           => 'Importe',
            'currency'         => 'Moneda',
            'category'         => 'Categoría',
            'paid-by'          => 'Pagado por',
            'employee'         => 'Empleado',
            'payee'            => 'Proveedor o transportista',
            'shipment'         => 'Envío',
            'trip'             => 'Viaje',
            'vehicle'          => 'Vehículo',
            'vendor-reference' => 'Referencia del proveedor',
            'description'      => 'Descripción',
            'receipt'          => 'Recibo',
        ],
    ],

    'table' => [
        'columns' => [
            'date'        => 'Fecha',
            'category'    => 'Categoría',
            'description' => 'Descripción',
            'amount'      => 'Importe',
            'state'       => 'Estado',
            'shipment'    => 'Envío',
            'company'     => 'Empresa',
        ],
        'filters' => [
            'state'    => 'Estado',
            'category' => 'Categoría',
        ],
    ],

    'infolist' => [
        'sections' => [
            'summary' => 'Gasto',
        ],
        'fields' => [
            'state'       => 'Estado',
            'date'        => 'Fecha',
            'amount'      => 'Importe',
            'category'    => 'Categoría',
            'shipment'    => 'Envío',
            'trip'        => 'Viaje',
            'vehicle'     => 'Vehículo',
            'receipt'     => 'Recibo',
            'description' => 'Descripción',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'Enviar',
            'heading'      => '¿Enviar este gasto?',
            'notification' => 'Gasto enviado.',
        ],
        'approveExpense' => [
            'label'        => 'Aprobar',
            'heading'      => '¿Aprobar este gasto?',
            'notification' => 'Gasto aprobado.',
        ],
        'rejectExpense' => [
            'label'        => 'Rechazar',
            'heading'      => '¿Rechazar este gasto?',
            'notification' => 'Gasto rechazado.',
        ],
        'cannot-proceed' => 'Este gasto aún no está listo',
    ],
];
