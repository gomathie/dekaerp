<?php

return [
    'title' => 'Imprimir e enviar',

    'modal' => [
        'title' => 'Visualizar fatura',

        'form' => [
            'partners'    => 'Cliente',
            'subject'     => 'Assunto',
            'description' => 'Descrição',
            'files'       => 'Anexo',
        ],

        'action' => [
            'submit' => [
                'title' => 'Enviar',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Fatura enviada',
                'body'  => 'A fatura foi enviada com sucesso.',
            ],
        ],
    ],

    'message' => [
        'subject'   => 'Fatura de :partner (Ref. :number)',
        'greeting'  => 'Prezado(a) :name,',
        'body'      => 'Sua fatura :number de :company no valor de :amount já está disponível. Por favor, providencie o pagamento o quanto antes.',
        'bank'      => 'Ao efetuar o pagamento, informe a referência :number para a conta :account.',
        'questions' => 'Se tiver alguma dúvida, fique à vontade para entrar em contato.',
        'regards'   => 'Atenciosamente,',
    ],
];
