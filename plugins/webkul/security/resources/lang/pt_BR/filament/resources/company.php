<?php

return [
    'title' => 'Empresas',

    'navigation' => [
        'title' => 'Empresas',
    ],

    'global-search' => [
        'email' => 'E-mail',
    ],

    'pages' => [
        'create' => [
            'administrator' => [
                'invited' => [
                    'title' => 'Administrador convidado',
                    'body'  => 'Um convite foi enviado para :email. Essa pessoa escolhe a própria senha e depois pode adicionar os usuários da empresa.',
                ],

                'skipped' => [
                    'title'         => 'Nenhum administrador foi convidado',
                    'missing-email' => 'A empresa foi criada, mas não tem endereço de e-mail, então ninguém pôde ser convidado para administrá-la. Adicione um e envie um convite pela página de Usuários.',
                    'missing-role'  => 'A empresa foi criada, mas o papel “Company Admin” não existe nesta instalação, então ninguém pôde ser convidado.',
                    'invite-failed' => 'A empresa foi criada, mas o convite não pôde ser emitido. O endereço pode já pertencer a um usuário, ou a empresa pode ainda não ser uma que você possa atribuir. Convide pela página de Usuários.',
                    'mail-failed'   => 'A empresa foi criada e o convite emitido, mas o e-mail não pôde ser enviado. Entregue este link de acesso a :email por um canal confiável — ele expira em 7 dias e a pessoa escolhe a própria senha: :link',
                ],
            ],
        ],
    ],
];
