<?php

return [
    'title' => 'Imprimir y enviar',

    'modal' => [
        'title' => 'Vista previa de la factura',

        'form' => [
            'partners'    => 'Cliente',
            'subject'     => 'Asunto',
            'description' => 'Descripción',
            'files'       => 'Adjunto',
        ],

        'action' => [
            'submit' => [
                'title' => 'Enviar',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Factura enviada',
                'body'  => 'La factura se ha enviado correctamente.',
            ],
        ],
    ],

    'message' => [
        'subject'   => 'Factura de :partner (Ref. :number)',
        'greeting'  => 'Estimado/a :name:',
        'body'      => 'Su factura :number de :company por :amount ya está disponible. Le rogamos que realice el pago lo antes posible.',
        'bank'      => 'Al realizar el pago, indique la referencia :number para la cuenta :account.',
        'questions' => 'Si tiene alguna pregunta, no dude en contactarnos.',
        'regards'   => 'Atentamente,',
    ],
];
