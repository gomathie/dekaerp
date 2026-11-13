<?php

return [
    'navigation'      => 'Relatórios',
    'export-complete' => 'Sua exportação de vendas está pronta: :count linhas.',

    'analysis' => [
        'title'      => 'Análise de vendas',
        'subheading' => 'Pedidos confirmados na moeda da empresa. Agrupe por cliente, vendedor ou mês para ver os totais.',
        'export'     => 'Exportar',
        'sum'        => 'Total',
        'columns'    => [
            'reference'   => 'Pedido',
            'date'        => 'Data',
            'customer'    => 'Cliente',
            'salesperson' => 'Vendedor',
            'untaxed'     => 'Sem impostos',
            'total'       => 'Total',
        ],
        'groups' => [
            'customer'    => 'Cliente',
            'salesperson' => 'Vendedor',
            'month'       => 'Mês',
        ],
        'filters' => [
            'from'  => 'De',
            'until' => 'Até',
        ],
    ],
];
