<?php

return [
    'navigation' => [
        'title' => 'Conciliação bancária',
    ],

    'import' => [
        'label'                => 'Importar extrato',
        'description'          => 'Envie o arquivo CSV do seu banco. Informe o número de cada coluna, contando a partir de 1 à esquerda. Cada conta bancária lembra suas colunas para a próxima vez; se o banco mover uma coluna, ela é encontrada de novo pelo título.',
        'journal'              => 'Conta bancária',
        'file'                 => 'Arquivo do extrato (CSV)',
        'name'                 => 'Nome do extrato',
        'date-format'          => 'Formato de data',
        'has-header'           => 'A primeira linha é um cabeçalho',
        'amount-help'          => 'Ou deixe vazio e informe as colunas de entradas e saídas.',
        'balance-help'         => 'Opcional: o saldo corrente. O valor do último dia é o saldo final.',
        'closing-balance'      => 'Saldo final do extrato',
        'closing-balance-help' => 'Opcional. Sem ele ou uma coluna de saldo, o saldo final é o do extrato anterior mais estas linhas.',
        'done'                 => ':count linhas importadas.',

        'columns' => [
            'date'      => 'Coluna de data',
            'label'     => 'Coluna de descrição',
            'amount'    => 'Coluna de valor',
            'money_in'  => 'Coluna de entradas',
            'money_out' => 'Coluna de saídas',
            'partner'   => 'Coluna de cliente ou fornecedor',
            'reference' => 'Coluna de referência',
            'balance'   => 'Coluna de saldo',
        ],
    ],

    'match-all' => [
        'label'       => 'Conciliar todas as sugestões (:count)',
        'heading'     => 'Conciliar todas as linhas sugeridas?',
        'description' => '{0} Nenhuma linha tem agora um único pagamento sugerido.|{1} :count linha tem um único pagamento sugerido com o mesmo valor. Ela será conciliada com ele. As demais linhas ficam como estão.|[2,*] :count linhas têm um único pagamento sugerido com o mesmo valor. Cada uma será conciliada com seu pagamento. As demais linhas ficam como estão.',
        'submit'      => 'Conciliar',
        'done'        => '{0} Nenhuma linha foi conciliada.|{1} :count linha conciliada.|[2,*] :count linhas conciliadas.',
        'left'        => '{1} :count linha ficou aberta: o período está bloqueado ou ela mudou nesse meio-tempo.|[2,*] :count linhas ficaram abertas: o período está bloqueado ou elas mudaram nesse meio-tempo.',
    ],

    'balances' => [
        'heading'      => 'Conferência de saldo por conta',
        'statement'    => 'Saldo do extrato',
        'book'         => 'Saldo contábil',
        'difference'   => 'Diferença',
        'up-to'        => 'Extrato até :date',
        'no-statement' => 'Nenhum extrato importado ainda',
        'explain'      => 'Saldo do extrato: o saldo final do último extrato importado. Saldo contábil: o que o razão registra na conta; um pagamento conta quando é conciliado com uma linha do extrato.',

        'state' => [
            'clean'      => 'Em dia',
            'to-match'   => '{1} :count a conciliar|[2,*] :count a conciliar',
            'difference' => 'Diferença a verificar',
            'none'       => 'Sem extrato',
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

    'match-expense' => [
        'label'       => 'Conciliar despesa',
        'description' => 'Despesas pagas com esta conta bancária, com o mesmo valor, a poucos dias desta linha.',
        'expense'     => 'Despesa',
        'none'        => 'Nenhuma despesa corresponde. Registre a despesa primeiro ou lance a linha em uma conta.',
        'done'        => 'Linha conciliada com a despesa. Nada foi lançado de novo.',
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
