<?php

return [
    'title' => 'Registrar pagamento',

    'paid-now' => [
        'label'       => 'Paga agora',
        'helper'      => 'Ao salvar, a fatura é confirmada e o pagamento total é registrado.',
        'invalid'     => 'Esta forma de pagamento não pode ser usada para esta fatura.',
        'not-allowed' => 'Você não tem permissão para registrar pagamentos.',
    ],

    'form' => [
        'more-options' => 'Mais opções',
        'fields'       => [
            'paid-how'               => 'Como foi pago?',
            'new-way'                => 'Nova forma de pagamento (ex.: Wave, cartão)',
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
