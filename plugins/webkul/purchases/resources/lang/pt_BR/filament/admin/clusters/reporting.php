<?php

return [
    'navigation'      => 'Relatórios',
    'export-complete' => 'Sua exportação de compras está pronta: :count linhas.',

    'analysis' => [
        'title'      => 'Análise de compras',
        'subheading' => 'Pedidos de compra confirmados na moeda da empresa. Agrupe por fornecedor, comprador ou mês para ver os totais.',
        'export'     => 'Exportar',
        'sum'        => 'Total',
        'columns'    => [
            'reference' => 'Pedido',
            'date'      => 'Data',
            'supplier'  => 'Fornecedor',
            'buyer'     => 'Comprador',
            'untaxed'   => 'Sem impostos',
            'total'     => 'Total',
        ],
        'groups' => [
            'supplier' => 'Fornecedor',
            'buyer'    => 'Comprador',
            'month'    => 'Mês',
        ],
        'filters' => [
            'from'  => 'De',
            'until' => 'Até',
        ],
    ],

    'by-product' => [
        'title'      => 'Compras por produto',
        'subheading' => 'Linhas de pedidos de compra confirmados na moeda da empresa. Agrupe por produto ou categoria para ver quantidades pedidas e recebidas.',
        'export'     => 'Exportar',
        'sum'        => 'Total',
        'columns'    => [
            'product'  => 'Produto',
            'category' => 'Categoria',
            'order'    => 'Pedido de compra',
            'date'     => 'Data',
            'supplier' => 'Fornecedor',
            'ordered'  => 'Pedido',
            'received' => 'Recebido',
            'untaxed'  => 'Sem impostos',
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
