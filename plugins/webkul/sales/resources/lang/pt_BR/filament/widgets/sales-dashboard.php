<?php

return [
    'unknown'             => 'Desconhecido',
    'other'               => 'Outros',
    'share'               => 'Participação',
    'status'              => 'Situação',
    'delivery-status'     => 'Situação da entrega',
    'billing'             => 'Faturamento',
    'sales-value'         => 'Pedidos confirmados e faturas emitidas sem pedido',
    'stats'               => [
        'heading'            => 'Métricas Principais',
        'total-quotations'   => 'Total de Cotações',
        'total-sales-orders' => 'Total de Vendas',
        'total-revenue'      => 'Receita Total',
        'average-revenue'    => 'Receita Média',
        'increase'           => 'aumento',
        'decrease'           => 'redução',
    ],

    'order-activity'      => [
        'heading'     => 'Atividade dos pedidos',
        'description' => 'Pedidos confirmados no período, conforme o estado atual',
        'to-deliver'  => 'A entregar',
        'delivered'   => 'Entregues',
        'to-invoice'  => 'A faturar',
        'invoiced'    => 'Faturados',
        'share'       => ':percent% de :total pedidos',
    ],

    'monthly-sales'       => [
        'heading'  => 'Vendas ao longo do tempo',
        'revenue'  => 'Receita',
        'previous' => 'Período comparado',
    ],

    'revenue-by-category' => [
        'heading' => 'Receita por Categoria',
        'revenue' => 'Receita',
    ],

    'sales-persons'       => [
        'heading' => 'Desempenho por Vendedor',
        'revenue' => 'Receita',
    ],

    'quotation-outcome'   => [
        'heading'     => 'Cotações',
        'description' => 'O que aconteceu com as cotações feitas no período',
        'won'         => 'Viraram pedidos',
        'open'        => 'Ainda abertas',
        'cancelled'   => 'Canceladas',
        'count'       => 'Cotações',
    ],

    'top-products'        => [
        'heading' => 'Principais Produtos',
        'columns' => [
            'name'     => 'Produto',
            'quantity' => 'Quantidade Vendida',
            'revenue'  => 'Receita',
        ],
    ],

    'top-customers'       => [
        'heading' => 'Principais Clientes',
        'columns' => [
            'name'    => 'Cliente',
            'orders'  => 'Vendas',
            'revenue' => 'Receita',
        ],
    ],

    'top-sales-orders'    => [
        'heading' => 'Principais Vendas',
        'columns' => [
            'reference'      => 'Número do Pedido',
            'customer'       => 'Cliente',
            'amount'         => 'Valor total',
            'invoice-status' => 'Status da fatura',
        ],
    ],

    'top-quotations'      => [
        'heading' => 'Principais Cotações',
        'columns' => [
            'reference' => 'Referência',
            'customer'  => 'Cliente',
            'amount'    => 'Valor',
        ],
    ],
];
