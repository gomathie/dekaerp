<?php

return [
    'title' => 'Pagar',

    'paid-now' => [
        'label'   => 'Pagada ahora',
        'helper'  => 'Al guardar se confirma la factura y se registra el pago completo.',
        'invalid' => 'Esta forma de pago no se puede usar para esta factura.',
    ],

    'form' => [
        'more-options' => 'Más opciones',
        'fields'       => [
            'paid-how'               => '¿Cómo se pagó?',
            'new-way'                => 'Nueva forma de pago (p. ej. Wave, tarjeta)',
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
