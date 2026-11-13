<?php

return [
    'navigation' => [
        'title' => 'Visão geral',
    ],

    'title' => 'Visão geral',

    'sections' => [
        'sales'     => 'Vendas',
        'purchases' => 'Compras',
        'invoices'  => 'Faturas e contas',
    ],

    'filters' => [
        'sections'       => 'Mostrar',
        'all-sections'   => 'Todas as seções',
        'date-range'     => 'Período',
        'product'        => 'Produto',
        'all-products'   => 'Todos os produtos',
        'category'       => 'Categoria',
        'all-categories' => 'Todas as categorias',
    ],

    'headline' => [
        'heading'         => 'Visão rápida',
        'sales'           => 'Vendas',
        'purchases'       => 'Compras',
        'invoices'        => 'Faturas',
        'revenue'         => 'Receita',
        'orders'          => '{0} Nenhum pedido|{1} 1 pedido|[2,*] :count pedidos',
        'open-quotations' => 'Cotações abertas',
        'spend'           => 'Gasto em compras',
        'open-rfqs'       => 'Solicitações abertas',
        'to-collect'      => 'A receber',
        'to-pay'          => 'A pagar',
        'overdue'         => '{0} Nenhuma vencida|{1} 1 vencida|[2,*] :count vencidas',
    ],

    'attention' => [
        'heading'          => 'Precisa de atenção',
        'empty'            => 'Tudo em dia. Nada está esperando por você.',
        'overdue-invoices' => '{1} 1 fatura vencida|[2,*] :count faturas vencidas',
        'overdue-bills'    => '{1} 1 conta vencida|[2,*] :count contas vencidas',
        'bills-due-soon'   => '{1} 1 conta vence esta semana|[2,*] :count contas vencem esta semana',
        'old-quotations'   => '{1} 1 cotação aguardando há mais de 7 dias|[2,*] :count cotações aguardando há mais de 7 dias',
        'rfqs-to-approve'  => '{1} 1 compra para aprovar|[2,*] :count compras para aprovar',
    ],

    'actions' => [
        'new-invoice'   => 'Nova fatura',
        'new-bill'      => 'Nova conta',
        'new-quotation' => 'Nova cotação',
        'new-rfq'       => 'Nova solicitação de cotação',
    ],

    'cash' => [
        'heading'     => 'Entradas e saídas de dinheiro',
        'description' => 'Faturas recebidas e contas pagas por dia',
        'in'          => 'Entradas',
        'out'         => 'Saídas',
    ],

    'trend' => ':change em relação ao período anterior',

    'modules' => [
        'heading' => 'Em toda a empresa',
        'open'    => 'Abrir',
    ],
];
