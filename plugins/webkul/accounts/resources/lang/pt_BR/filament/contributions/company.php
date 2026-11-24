<?php

return [
    'base-currency' => [
        'title'       => 'Moeda base',
        'description' => 'Alterar a moeda contábil de uma empresa que já tem lançamentos. Somente Super Admin; fica registrado no log de atividades.',
        'action'      => 'Alterar moeda base',
        'warning'     => 'Esta empresa tem :count lançamentos publicados. Eles mantêm sua moeda e taxa, mas os relatórios passarão a usar a nova moeda base. A alteração, seu nome e seu motivo são registrados.',
        'changed'     => 'Moeda base alterada',
        'fields'      => [
            'currency' => 'Nova moeda base',
            'reason'   => 'Motivo',
        ],
    ],

    'sale-currency' => [
        'title'       => 'Pergunta de moeda nas novas faturas',
        'description' => 'Para empresas que vendem em mais de uma moeda.',
        'field'       => 'Perguntar a moeda de cada venda',
        'helper'      => 'Uma nova fatura, cotação ou pedido de venda pergunta primeiro a moeda e, se for estrangeira, pede a confirmação da taxa do dia. Só é perguntado se a empresa tiver mais de uma moeda.',
    ],
];
