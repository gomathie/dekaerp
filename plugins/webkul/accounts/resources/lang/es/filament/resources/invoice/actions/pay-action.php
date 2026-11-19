<?php

return [
    'title' => 'Pagar',

    'form' => [
        'fields' => [
            'journal'                => 'Diario',
            'amount'                 => 'Importe',
            'currency'               => 'Moneda',
            'payment-method-line'    => 'Línea de método de pago',
            'payment-date'           => 'Fecha de pago',
            'recipient-bank-account' => 'Cuenta bancaria del destinatario',
            'communication'          => 'Nota',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title'      => 'Pago fallido',
            'plain-body' => 'No se pudo preparar este pago. Nuestro equipo ha sido avisado. Inténtelo de nuevo en un momento o contacte con soporte.',
        ],
    ],
];
