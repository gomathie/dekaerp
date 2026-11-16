<?php

return [
    'banner' => [
        'message' => 'Você está conectado como :target (:company). Personificado por :impersonator.',
        'ends-at' => 'Termina às :time',
        'end'     => 'Encerrar personificação',
    ],

    'notifications' => [
        'started' => 'Agora você está conectado como :name.',
        'refused' => 'A personificação não foi iniciada',

        'ended-automatically' => [
            'expired' => 'A personificação atingiu o limite de tempo e foi encerrada.',
            'invalid' => 'A personificação não é mais permitida e foi encerrada.',
        ],

        'granted' => 'Acesso à personificação concedido.',
        'revoked' => 'Acesso à personificação removido.',
    ],

    'actions' => [
        'impersonate' => [
            'label'             => 'Personificar',
            'modal-heading'     => 'Entrar como :name',
            'modal-description' => 'Você agirá como este usuário, apenas com as permissões dele, por no máximo :minutes minutos. Tudo o que fizer é registrado com o seu nome.',
            'submit'            => 'Iniciar personificação',

            'form' => [
                'reason'   => 'Motivo',
                'password' => 'Sua senha',
            ],
        ],

        'grant' => [
            'label'             => 'Permitir personificação',
            'modal-description' => 'Este administrador multiempresa poderá personificar usuários das empresas atribuídas a ele.',
        ],

        'revoke' => [
            'label'             => 'Remover acesso à personificação',
            'modal-description' => 'Uma personificação em andamento termina no próximo carregamento de página.',
        ],
    ],
];
