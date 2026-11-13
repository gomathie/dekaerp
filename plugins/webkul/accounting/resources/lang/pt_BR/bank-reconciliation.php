<?php

return [
    'statement-name' => 'Extrato de :from a :to',
    'entry-label'    => 'Linha de extrato bancário',

    'fields' => [
        'date'   => 'data',
        'amount' => 'valor',
    ],

    'errors' => [
        'not-allowed'            => 'Você não pode conciliar esta conta bancária.',
        'not-a-bank-journal'     => 'Escolha um diário de banco ou de caixa.',
        'foreign-currency'       => 'Este diário usa uma moeda estrangeira. Por enquanto só contas na moeda da empresa podem ser conciliadas.',
        'missing-bank-account'   => 'Este diário não tem conta bancária. Defina primeiro a conta padrão.',
        'empty-file'             => 'O arquivo não tem linhas de extrato.',
        'too-many-rows'          => 'O arquivo tem mais de :max linhas. Divida-o em extratos menores.',
        'bad-row'                => 'Linha :row: não foi possível ler o campo :field. Verifique os números das colunas e o formato da data.',
        'already-reconciled'     => 'Esta linha já está conciliada.',
        'payment-does-not-match' => 'Esse pagamento não corresponde a esta linha: deve estar no mesmo diário bancário, com o mesmo valor e sentido, e ainda não conciliado.',
        'account-not-allowed'    => 'Essa conta não pode ser usada aqui. Contas de clientes e fornecedores são liquidadas conciliando um pagamento.',
    ],
];
