<?php

return [
    'title' => 'Gerenciar orçamento e pedido',

    'breadcrumb' => 'Gerenciar orçamento e pedido',

    'navigation' => [
        'title' => 'Gerenciar orçamento e pedido',
    ],

    'form' => [
        'fields' => [
            'validity-suffix'             => 'dias',
            'validity'                    => 'Validade padrão da orçamento',
            'validity-help'               => 'O número padrão de dias em que uma orçamento é válida.',
            'lock-confirm-sales'          => 'Bloquear vendas confirmadas',
            'lock-confirm-sales-help'     => 'Se habilitado, o pedido de venda será bloqueado após a confirmação.',
            'goods-leave-on-invoice'      => 'Os clientes levam as mercadorias ao faturar',
            'goods-leave-on-invoice-help' => 'Padrão para faturas que criam um pedido de venda. Ligado: a entrega é registrada como feita ao confirmar a fatura. Desligado: a entrega fica para o estoque validar.',
        ],
    ],
];
