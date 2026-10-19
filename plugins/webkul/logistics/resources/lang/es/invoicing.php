<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'Tiempo de espera en :stop (:minutes min por encima del tiempo gratuito)',
    ],

    'charges' => [
        'title'  => 'Cargos',
        'fields' => [
            'product'            => 'Servicio',
            'description'        => 'Descripción',
            'quantity'           => 'Cantidad',
            'price-unit'         => 'Precio unitario',
            'discount'           => 'Descuento (%)',
            'taxes'              => 'Impuestos',
            'is-billable'        => 'Facturable',
            'is-billable-helper' => 'Al crear una factura solo se incluyen los cargos facturables.',
            'invoiced'           => 'Facturado',
        ],
    ],

    'invoices' => [
        'title'   => 'Facturas',
        'columns' => [
            'number'        => 'Factura',
            'state'         => 'Estado',
            'payment-state' => 'Pago',
            'date'          => 'Fecha',
            'total'         => 'Total',
        ],
        'actions' => [
            'open' => 'Abrir en Contabilidad',
        ],
    ],

    'unbilled' => [
        'title'   => 'Cargos sin facturar',
        'columns' => [
            'shipment'    => 'Envío',
            'customer'    => 'Cliente',
            'description' => 'Cargo',
            'quantity'    => 'Cant.',
            'subtotal'    => 'Importe',
            'total'       => 'Total',
        ],
        'filters' => [
            'currency' => 'Moneda',
            'shipment' => 'Envío',
        ],
    ],

    'actions' => [
        'create-invoice' => [
            'label'              => 'Crear factura',
            'heading'            => '¿Facturar este envío?',
            'description'        => 'Cada cargo facturable que aún no se haya facturado se convierte en una línea de una nueva factura de cliente. Los cargos ya facturados no se modifican.',
            'notification'       => 'Factura creada.',
            'notification-body'  => 'La factura :invoice está en Contabilidad como borrador para que pueda revisarla antes de contabilizarla.',
            'nothing-to-invoice' => 'Nada que facturar',
        ],
    ],
];
