<?php

return [
    'navigation' => [
        'title' => 'Conciliação bancária',
    ],

    'import' => [
        'label'       => 'Importar extrato',
        'description' => 'Envie o arquivo CSV do seu banco. Informe o número de cada coluna, contando a partir de 1 à esquerda.',
        'journal'     => 'Conta bancária',
        'file'        => 'Arquivo do extrato (CSV)',
        'name'        => 'Nome do extrato',
        'date-format' => 'Formato de data',
        'has-header'  => 'A primeira linha é um cabeçalho',
        'amount-help' => 'Ou deixe vazio e informe as colunas de entradas e saídas.',
        'done'        => ':count linhas importadas.',

        'columns' => [
            'date'      => 'Coluna de data',
            'label'     => 'Coluna de descrição',
            'amount'    => 'Coluna de valor',
            'money_in'  => 'Coluna de entradas',
            'money_out' => 'Coluna de saídas',
            'partner'   => 'Coluna de cliente ou fornecedor',
            'reference' => 'Coluna de referência',
        ],
    ],

    'columns' => [
        'date'    => 'Data',
        'label'   => 'Descrição',
        'partner' => 'Cliente ou fornecedor',
        'journal' => 'Conta bancária',
        'amount'  => 'Valor',
        'status'  => 'Situação',
        'payment' => 'Pagamento',
    ],

    'status' => [
        'reconciled'   => 'Conciliada',
        'to-reconcile' => 'A conciliar',
    ],

    'match' => [
        'label'       => 'Conciliar pagamento',
        'description' => 'Pagamentos desta conta bancária com o mesmo valor que ainda aguardam o banco.',
        'payment'     => 'Pagamento',
        'none'        => 'Nenhum pagamento corresponde. Registre primeiro o pagamento na fatura, ou lance a linha em uma conta.',
        'done'        => 'Linha conciliada. O pagamento agora está pago.',
    ],

    'book' => [
        'label'       => 'Lançar em uma conta',
        'description' => 'Para tarifas bancárias, juros ou transferências: lança a linha na conta escolhida.',
        'account'     => 'Conta',
        'entry-label' => 'Rótulo',
        'done'        => 'Linha lançada.',
    ],

    'empty' => [
        'heading'     => 'Nada a conciliar',
        'description' => 'Importe um extrato do seu banco para começar.',
    ],
];
