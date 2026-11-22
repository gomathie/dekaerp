<?php

return [
    'create-sale-order' => [
        'label'  => 'Criar pedido de venda',
        'helper' => 'Ao confirmar a fatura, um pedido de venda confirmado é criado a partir dela, com entrega para produtos estocáveis, para que apareça em Vendas e Estoque.',
    ],

    'linked-order' => [
        'label' => 'Pedido de venda',
    ],
    'goods-delivered-now' => [
        'label'  => 'O cliente levou as mercadorias',
        'helper' => 'Registra a entrega como feita ao confirmar a fatura. Desmarque para deixá-la com o estoque.',
    ],

    'delivery' => [
        'label'               => 'Entrega',
        'waiting'             => 'Aguardando o estoque',
        'done'                => 'Entregue',
        'mark-delivered'      => 'Marcar como entregue',
        'confirm-heading'     => 'Registrar a entrega?',
        'confirm-description' => 'Os itens desta fatura serão registrados como entregues e baixados do estoque. Itens sem estoque suficiente continuam aguardando.',
        'delivered'           => 'Entrega registrada.',
        'not-enough-stock'    => 'Alguns itens não estão em estoque; continuam aguardando no Estoque.',
    ],
];
