<?php

return [
    'title' => 'Estornar',

    'modal' => [
        'heading' => 'Criar estorno',

        'form' => [
            'reason'  => 'Motivo exibido no estorno',
            'journal' => 'Diário',
            'date'    => 'Data',
        ],
    ],

    'one-step' => [
        'scope'               => 'O que creditar',
        'whole'               => 'A fatura inteira',
        'lines'               => 'Algumas linhas',
        'line-list'           => 'Linhas',
        'disposition'         => 'Depois',
        'apply'               => 'Abater da fatura',
        'refund'              => 'Devolver o dinheiro',
        'credit'              => 'Guardar como crédito',
        'saved'               => 'Nota de crédito lançada.',
        'history'             => 'Nota de crédito lançada (:disposition). Motivo: :reason',
        'reason'              => 'O motivo é obrigatório.',
        'invoice'             => 'Somente uma fatura de cliente lançada pode ser creditada assim.',
        'lines-invalid'       => 'Escolha linhas desta fatura.',
        'disposition-invalid' => 'Escolha o que fazer com a nota de crédito.',
        'not-allowed'         => 'Você não tem permissão para devolver o dinheiro.',
        'company'             => 'Você não pode creditar uma fatura dessa empresa.',
        'journal'             => 'Escolha um diário de banco, caixa ou cartão desta empresa.',
        'not-settled'         => 'O dinheiro não foi devolvido.',
        'not-applied'         => 'A nota de crédito não foi abatida da fatura.',
    ],
];
