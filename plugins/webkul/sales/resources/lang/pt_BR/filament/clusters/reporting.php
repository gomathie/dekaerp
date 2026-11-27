<?php

return [
    'navigation'      => 'Relatórios',
    'export-complete' => 'Sua exportação de vendas está pronta: :count linhas.',

    'analysis' => [
        'title'      => 'Análise de vendas',
        'subheading' => 'Pedidos confirmados e faturas sem pedido, na moeda da empresa. Agrupe por cliente, vendedor ou mês para ver os totais.',
        'export'     => 'Exportar para Excel',
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

    'by-product' => [
        'title'      => 'Vendas por produto',
        'subheading' => 'Linhas de pedidos confirmados e de faturas sem pedido, na moeda da empresa. Agrupe por produto ou categoria para ver quantidades e totais.',
        'export'     => 'Exportar para Excel',
        'sum'        => 'Total',
        'columns'    => [
            'product'  => 'Produto',
            'category' => 'Categoria',
            'order'    => 'Pedido ou fatura',
            'date'     => 'Data',
            'customer' => 'Cliente',
            'quantity' => 'Quantidade',
            'untaxed'  => 'Sem impostos',
            'none'     => 'Sem produto',
        ],
        'groups' => [
            'product'  => 'Produto',
            'category' => 'Categoria',
        ],
        'filters' => [
            'from'  => 'De',
            'until' => 'Até',
        ],
    ],
];
