<?php

return [
    'title' => 'Pagar',

    'form' => [
        'fields' => [
            'journal'                => 'Diário',
            'amount'                 => 'Valor',
            'currency'               => 'Moeda',
            'payment-method-line'    => 'Linha do método de pagamento',
            'payment-date'           => 'Data do pagamento',
            'recipient-bank-account' => 'Conta bancária do destinatário',
            'communication'          => 'Memorando',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title'      => 'Falha no pagamento',
            'plain-body' => 'Não foi possível preparar este pagamento. Nossa equipe foi avisada. Tente novamente em instantes ou fale com o suporte.',
        ],
    ],
];
