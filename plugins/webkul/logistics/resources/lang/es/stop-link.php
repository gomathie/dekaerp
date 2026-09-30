<?php

return [
    'title'    => 'Confirmar entrega',
    'shipment' => 'Envío',
    'stop'     => 'Parada',

    'ttl' => [
        4  => '4 horas',
        8  => '8 horas',
        16 => '16 horas',
        24 => '24 horas (1 día)',
        48 => '48 horas (2 días)',
    ],

    'form' => [
        'recipient-name'      => 'Recibido por',
        'recipient-name-help' => 'El nombre de la persona que recibió la entrega.',
        'recipient-id'        => 'Identificación del destinatario',
        'recipient-id-help'   => 'Una identificación o referencia de la persona que recibe, según lo requiera su empresa.',
        'photo'               => 'Foto',
        'photo-help'          => 'Una foto de la mercancía entregada, la puerta o la carta de porte firmada.',
        'signature'           => 'Firma',
        'signature-help'      => 'Pida al destinatario que firme en el recuadro.',
        'signature-clear'     => 'Borrar',
        'notes'               => 'Notas',
        'location'            => 'Adjuntar mi ubicación',
        'location-attached'   => 'Ubicación adjuntada.',
        'location-failed'     => 'Ubicación no disponible. Aun así puede enviar.',
        'submit'              => 'Confirmar entrega',
        'submitting'          => 'Confirmando…',
    ],

    'done' => [
        'title'   => 'Entrega confirmada',
        'message' => 'Gracias. La entrega se ha registrado y este enlace ya está cerrado.',
    ],

    'expired' => [
        'title'   => 'Este enlace ya no es válido',
        'message' => 'Puede haber caducado, haberse utilizado ya o haberse retirado. Solicite un enlace nuevo a la oficina.',
    ],

    'actions' => [
        'revoke-pod-link' => [
            'label'        => 'Cancelar enlace de comprobante',
            'heading'      => '¿Cancelar el enlace del comprobante de entrega?',
            'description'  => 'Cualquier enlace ya enviado para este envío dejará de funcionar inmediatamente. Utilice esta opción si el enlace se envió al número equivocado.',
            'notification' => '{0}No había ningún enlace activo que cancelar.|{1}Enlace cancelado.|[2,*]Se cancelaron :count enlaces.',
        ],
        'send-pod-link' => [
            'label'        => 'Enlace de comprobante',
            'heading'      => 'Enlace de comprobante de entrega de un solo uso',
            'description'  => 'Comparta este enlace con el conductor. Funciona una vez, solo para esta parada, y caduca después de :hours horas. Al emitir un enlace nuevo se cancela cualquier enlace anterior.',
            'copy'         => 'Copiar enlace',
            'no-stop'      => 'Este envío no tiene ninguna parada de entrega abierta, por lo que no hay nada que registrar.',
            'notification' => 'Enlace emitido.',
        ],
    ],
];
