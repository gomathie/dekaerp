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

    'delete' => [
        'question'  => '{1} Cancelar também o pedido de venda :orders e sua entrega?|[2,*] Cancelar também os pedidos de venda :orders e suas entregas?',
        'yes'       => 'Sim, cancelar',
        'no'        => 'Não, manter para faturar depois',
        'stays'     => '{1} O pedido de venda :orders é mantido e volta para "A faturar".|[2,*] Os pedidos de venda :orders são mantidos e voltam para "A faturar".',
        'cancelled' => 'Cancelados: :orders.',
    ],
];
