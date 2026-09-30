<?php

$dateFilters = [
    'from'    => 'De',
    'until'   => 'Até',
    'between' => 'De :from até :until',
    'since'   => 'Desde :from',
    'up-to'   => 'Até :until',
];

/*
 * Vehicle and driver trip history are the same table with a different filter in
 * front, so they share their column and filter labels rather than keeping two
 * copies to drift apart. TripHistoryExporter labels its file from the
 * vehicle-trips keys whichever page it was started from, which is only correct
 * while the two sets are identical - keep them so.
 */
$tripColumns = [
    'reference' => 'Viagem',
    'vehicle'   => 'Veículo',
    'driver'    => 'Motorista',
    'state'     => 'Status',
    'started'   => 'Iniciada',
    'ended'     => 'Finalizada',
    'shipments' => 'Remessas',
    'distance'  => 'Distância',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'Veículo',
    'driver'  => 'Motorista',
    'state'   => 'Status',
];

return [
    'export'          => 'Exportar',
    'export-complete' => ':count linhas exportadas.',

    'shipment-register' => [
        'title'  => 'Registro de remessas',
        'export' => 'Exportar',

        'columns' => [
            'reference'   => 'Remessa',
            'customer'    => 'Cliente',
            'state'       => 'Status',
            'origin'      => 'De',
            'destination' => 'Para',
            'expected'    => 'Prevista',
            'delivered'   => 'Entregue',
            'company'     => 'Empresa',
        ],

        'filters' => $dateFilters + [
            'state'    => 'Status',
            'customer' => 'Cliente',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'Desempenho das entregas',
        'subheading' => 'Medido em relação à data de entrega prevista. Remessas sem data prevista não são avaliadas.',
        'export'     => 'Exportar',

        'columns' => [
            'reference' => 'Remessa',
            'customer'  => 'Cliente',
            'expected'  => 'Prevista',
            'delivered' => 'Entregue',
            'outcome'   => 'Resultado',
            'delay'     => 'Atraso de',
        ],

        'outcomes' => [
            'on-time'      => 'No prazo',
            'late'         => 'Atrasada',
            'failed'       => 'Falhou',
            'overdue'      => 'Em atraso',
            'pending'      => 'Em andamento',
            'not-measured' => 'Nenhuma data prometida',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'Cliente',
            'late-only'   => 'Somente atrasadas',
            'failed-only' => 'Somente com falha',
        ],
    ],

    'profitability' => [
        'title'      => 'Rentabilidade das remessas',
        'subheading' => 'Cobranças faturáveis comparadas aos custos aprovados, cada um na moeda da remessa. Custos em rascunho não são contabilizados.',
        'export'     => 'Exportar',

        'columns' => [
            'reference' => 'Remessa',
            'customer'  => 'Cliente',
            'revenue'   => 'Receita',
            'costs'     => 'Custos',
            'margin'    => 'Margem',
            'currency'  => 'Moeda',
        ],

        'filters' => $dateFilters + [
            'customer'     => 'Cliente',
            'loss-making'  => 'Somente com prejuízo',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'Histórico de viagens do veículo',
        'export'  => 'Exportar',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'Histórico de viagens do motorista',
        'export'  => 'Exportar',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];
