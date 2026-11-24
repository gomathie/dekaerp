<?php

return [
    'notification' => [
        'title' => 'Fatura criada',
        'body'  => 'A fatura foi criada com sucesso.',
    ],

    'direct-sale' => [
        'button'           => 'Nova venda direta',
        'walk-in-customer' => 'Cliente avulso',
    ],

    'receipt' => [
        'label'     => 'Imprimir recibo',
        'heading'   => 'Recibo',
        'title'     => 'Recibo',
        'number'    => 'Recibo nº',
        'date'      => 'Data',
        'customer'  => 'Cliente',
        'served-by' => 'Atendido por',
        'item'      => 'Item',
        'qty'       => 'Qtd.',
        'amount'    => 'Valor',
        'subtotal'  => 'Subtotal',
        'tax'       => 'Imposto',
        'total'     => 'Total',
        'paid'      => 'Pago',
        'balance'   => 'Saldo devedor',
        'thank-you' => 'Obrigado pela preferência.',
    ],

    'sale-currency' => [
        'heading'     => 'Em que moeda foi feita esta venda?',
        'description' => 'Escolha a moeda em que o cliente paga. A fatura é emitida nessa moeda, e o valor na moeda da empresa aparece ao lado dos totais.',
        'base'        => ':code (moeda da empresa)',
        'continue'    => 'Continuar',
        'rate'        => 'Taxa de câmbio',
        'feed'        => 'Taxa do dia do feed diário, de :date. Altere-a se combinou outra taxa.',
        'stale'       => 'A última taxa registrada é de :date, há mais de 3 dias. Verifique-a antes de continuar.',
        'no-rate'     => 'Não há taxa registrada para esta moeda. Digite a taxa combinada.',
        'chatter'     => 'Taxa de câmbio confirmada na criação da fatura: :rate.',
        'change'      => 'Moeda e taxa',
        'kept'        => 'Taxa mantida para esta venda: :rate.',
        'kept-by'     => 'Taxa mantida para esta venda: :rate, confirmada por :name.',
        'dropped'     => 'A taxa confirmada era de outra moeda, então esta fatura usa a taxa do dia.',
    ],
];
