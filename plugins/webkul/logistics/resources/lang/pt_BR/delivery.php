<?php

return [
    'actions' => [
        'pickup' => [
            'label'                => 'Marcar como coletada',
            'notification'         => 'Remessa marcada como coletada',
            'transit-label'        => 'Iniciar transporte',
            'transit-notification' => 'Remessa marcada em trânsito',
        ],
        'deliver' => [
            'label'                         => 'Entregar remessa',
            'notification'                  => 'Remessa entregue',
            'stop-notification'             => 'Parada de entrega registrada',
            'out-for-delivery-label'        => 'Marcar como saiu para entrega',
            'out-for-delivery-notification' => 'Remessa marcada como saiu para entrega',
            'fields'                        => [
                'capture-pod'    => 'Registrar comprovante de entrega',
                'stop'           => 'Parada de entrega',
                'recipient-name' => 'Nome do destinatário',
                'received-at'    => 'Recebido em',
                'reference'      => 'Referência',
                'notes'          => 'Observações',
                'photo'          => 'Foto da entrega',
                'signature'      => 'Assinatura do destinatário',
            ],
        ],
        'fail' => [
            'label'               => 'Registrar falha na entrega',
            'reason'              => 'Motivo',
            'notification'        => 'Falha na entrega registrada',
            'resolve-label'       => 'Resolver falha na entrega',
            'resolution'          => 'Próxima etapa',
            'retry-notification'  => 'Remessa pronta para outra tentativa de entrega',
            'return-notification' => 'Remessa marcada como devolvida',
            'resolutions'         => [
                'retry'  => 'Tentar entregar novamente',
                'return' => 'Marcar como devolvida',
            ],
        ],
    ],
    'relation-manager' => [
        'title'   => 'Comprovante de entrega',
        'columns' => [
            'recipient'    => 'Destinatário',
            'received-at'  => 'Recebido em',
            'stop'         => 'Parada',
            'captured-via' => 'Registrado por',
            'photo'        => 'Foto',
            'signature'    => 'Assinatura',
        ],
    ],
    'validation' => [
        'pod-required'        => 'É necessário um comprovante de entrega antes que esta remessa possa ser marcada como entregue.',
        'stop-required'       => 'Selecione a parada de entrega à qual este comprovante pertence.',
        'stop-unavailable'    => 'A parada de entrega selecionada não está mais disponível.',
        'upload-failed'       => 'Não foi possível armazenar o arquivo do comprovante. Tente novamente.',
        'resolution-required' => 'Escolha entre tentar novamente ou devolver a remessa cuja entrega falhou.',
    ],
];
