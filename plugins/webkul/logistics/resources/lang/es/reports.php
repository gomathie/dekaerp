<?php

$dateFilters = [
    'from'    => 'Desde',
    'until'   => 'Hasta',
    'between' => 'Del :from al :until',
    'since'   => 'Desde :from',
    'up-to'   => 'Hasta :until',
];

/*
 * Vehicle and driver trip history are the same table with a different filter in
 * front, so they share their column and filter labels rather than keeping two
 * copies to drift apart. TripHistoryExporter labels its file from the
 * vehicle-trips keys whichever page it was started from, which is only correct
 * while the two sets are identical - keep them so.
 */
$tripColumns = [
    'reference' => 'Viaje',
    'vehicle'   => 'Vehículo',
    'driver'    => 'Conductor',
    'state'     => 'Estado',
    'started'   => 'Iniciado',
    'ended'     => 'Finalizado',
    'shipments' => 'Envíos',
    'distance'  => 'Distancia',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'Vehículo',
    'driver'  => 'Conductor',
    'state'   => 'Estado',
];

return [
    'export'          => 'Exportar',
    'export-complete' => 'Se exportaron :count filas.',

    'shipment-register' => [
        'title'  => 'Registro de envíos',
        'export' => 'Exportar',

        'columns' => [
            'reference'   => 'Envío',
            'customer'    => 'Cliente',
            'state'       => 'Estado',
            'origin'      => 'Desde',
            'destination' => 'Hasta',
            'expected'    => 'Previsto',
            'delivered'   => 'Entregado',
            'company'     => 'Empresa',
        ],

        'filters' => $dateFilters + [
            'state'    => 'Estado',
            'customer' => 'Cliente',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'Rendimiento de entregas',
        'subheading' => 'Se mide respecto a la fecha de entrega prevista. Los envíos sin fecha prevista no se puntúan.',
        'export'     => 'Exportar',

        'columns' => [
            'reference' => 'Envío',
            'customer'  => 'Cliente',
            'expected'  => 'Previsto',
            'delivered' => 'Entregado',
            'outcome'   => 'Resultado',
            'delay'     => 'Retraso',
        ],

        'outcomes' => [
            'on-time'      => 'A tiempo',
            'late'         => 'Con retraso',
            'failed'       => 'Fallido',
            'overdue'      => 'Atrasado',
            'pending'      => 'En curso',
            'not-measured' => 'Sin fecha prometida',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'Cliente',
            'late-only'   => 'Solo con retraso',
            'failed-only' => 'Solo fallidos',
        ],
    ],

    'profitability' => [
        'title'      => 'Rentabilidad de envíos',
        'subheading' => 'Cargos facturables frente a costes aprobados, cada uno en la moneda del envío. Los costes en borrador no se incluyen.',
        'export'     => 'Exportar',

        'columns' => [
            'reference' => 'Envío',
            'customer'  => 'Cliente',
            'revenue'   => 'Ingresos',
            'costs'     => 'Costes',
            'margin'    => 'Margen',
            'currency'  => 'Moneda',
        ],

        'filters' => $dateFilters + [
            'customer'     => 'Cliente',
            'loss-making'  => 'Solo con pérdidas',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'Historial de viajes del vehículo',
        'export'  => 'Exportar',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'Historial de viajes del conductor',
        'export'  => 'Exportar',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];
