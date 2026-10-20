<?php

return [
    'title'    => 'Confirmar entrega',
    'shipment' => 'Remessa',
    'stop'     => 'Parada',

    'ttl' => [
        '4'  => '4 horas',
        '8'  => '8 horas',
        '16' => '16 horas',
        '24' => '24 horas (1 dia)',
        '48' => '48 horas (2 dias)',
    ],

    'form' => [
        'recipient-name'      => 'Recebido por',
        'recipient-name-help' => 'O nome da pessoa que recebeu a entrega.',
        'recipient-id'        => 'Documento do destinatário',
        'recipient-id-help'   => 'Um documento ou uma referência da pessoa que recebe, conforme exigido pela sua empresa.',
        'photo'               => 'Foto',
        'photo-help'          => 'Uma foto dos produtos entregues, da porta ou do conhecimento de transporte assinado.',
        'signature'           => 'Assinatura',
        'signature-help'      => 'Peça ao destinatário para assinar no quadro.',
        'signature-clear'     => 'Limpar',
        'notes'               => 'Observações',
        'location'            => 'Anexar minha localização',
        'location-attached'   => 'Localização anexada.',
        'location-failed'     => 'Localização indisponível. Você ainda pode enviar.',
        'submit'              => 'Confirmar entrega',
        'submitting'          => 'Confirmando…',
    ],

    'done' => [
        'title'   => 'Entrega confirmada',
        'message' => 'Obrigado. A entrega foi registrada e este link agora está encerrado.',
    ],

    'expired' => [
        'title'   => 'Este link não é mais válido',
        'message' => 'Ele pode ter expirado, já ter sido usado ou ter sido revogado. Peça um novo link ao escritório.',
    ],

    'actions' => [
        'revoke-pod-link' => [
            'label'        => 'Cancelar link do comprovante',
            'heading'      => 'Cancelar o link do comprovante de entrega?',
            'description'  => 'Qualquer link já enviado para esta remessa deixará de funcionar imediatamente. Use esta opção se um link foi enviado para o número errado.',
            'notification' => '{0}Não havia link ativo para cancelar.|{1}Link cancelado.|[2,*]:count links cancelados.',
        ],
        'send-pod-link' => [
            'label'        => 'Link do comprovante',
            'heading'      => 'Link de uso único para comprovante de entrega',
            'description'  => 'Compartilhe este link com o motorista. Ele funciona uma vez, somente para esta parada, e expira após :hours horas. Emitir um novo link cancela qualquer link anterior.',
            'copy'         => 'Copiar link',
            'no-stop'      => 'Esta remessa não tem nenhuma parada de entrega ainda aberta, portanto não há nada para registrar.',
            'notification' => 'Link emitido.',
        ],
    ],
];
