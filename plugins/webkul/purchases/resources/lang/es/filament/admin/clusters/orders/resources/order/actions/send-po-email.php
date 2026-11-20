<?php

return [
    'label' => 'Enviar pedido de compra por correo electrónico',

    'form' => [
        'fields' => [
            'to'      => 'Para',
            'subject' => 'Asunto',
            'message' => 'Mensaje',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'Correo electrónico enviado',
                'body'  => 'El correo electrónico se ha enviado correctamente.',
            ],

            'warning' => [
                'title' => 'Algunos correos no se enviaron',
                'body'  => 'Algunos proveedores no recibirán el correo porque no tienen dirección de correo electrónico.',
            ],

            'danger' => [
                'title' => 'Correo no enviado',
                'body'  => 'Añada una dirección de correo electrónico a los proveedores seleccionados e inténtelo de nuevo.',
            ],
        ],
    ],
];
