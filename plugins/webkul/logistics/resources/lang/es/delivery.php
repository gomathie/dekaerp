<?php

return [
    'actions' => [
        'pickup' => [
            'label'                => 'Marcar como recogido',
            'notification'         => 'Envío marcado como recogido',
            'transit-label'        => 'Iniciar tránsito',
            'transit-notification' => 'Envío marcado en tránsito',
        ],
        'deliver' => [
            'label'                         => 'Entregar envío',
            'notification'                  => 'Envío entregado',
            'stop-notification'             => 'Parada de entrega registrada',
            'out-for-delivery-label'        => 'Marcar en reparto',
            'out-for-delivery-notification' => 'Envío marcado en reparto',
            'fields'                        => [
                'capture-pod'    => 'Registrar comprobante de entrega',
                'stop'           => 'Parada de entrega',
                'recipient-name' => 'Nombre del destinatario',
                'received-at'    => 'Recibido el',
                'reference'      => 'Referencia',
                'notes'          => 'Notas',
                'photo'          => 'Foto de la entrega',
                'signature'      => 'Firma del destinatario',
            ],
        ],
        'fail' => [
            'label'               => 'Registrar entrega fallida',
            'reason'              => 'Motivo',
            'notification'        => 'Entrega fallida registrada',
            'resolve-label'       => 'Resolver entrega fallida',
            'resolution'          => 'Siguiente paso',
            'retry-notification'  => 'Envío listo para otro intento de entrega',
            'return-notification' => 'Envío marcado como devuelto',
            'resolutions'         => [
                'retry'  => 'Reintentar la entrega',
                'return' => 'Marcar como devuelto',
            ],
        ],
    ],
    'relation-manager' => [
        'title'   => 'Comprobante de entrega',
        'columns' => [
            'recipient'    => 'Destinatario',
            'received-at'  => 'Recibido el',
            'stop'         => 'Parada',
            'captured-via' => 'Registrado mediante',
            'photo'        => 'Foto',
            'signature'    => 'Firma',
        ],
    ],
    'validation' => [
        'pod-required'        => 'Se requiere un comprobante de entrega antes de poder marcar este envío como entregado.',
        'stop-required'       => 'Seleccione la parada de entrega a la que pertenece este comprobante.',
        'stop-unavailable'    => 'La parada de entrega seleccionada ya no está disponible.',
        'upload-failed'       => 'No se pudo guardar el archivo del comprobante. Inténtelo de nuevo.',
        'resolution-required' => 'Elija si desea reintentar o devolver el envío cuya entrega falló.',
    ],
];
