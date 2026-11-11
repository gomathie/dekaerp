<?php

return [
    'capacity-error' => 'Esta empresa atingiu o limite de :limit usuários (:used em uso, incluindo convites pendentes). Aumente o limite para adicionar mais usuários.',
    'title'          => 'Empresas',

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

                    'seats-exhausted' => 'A empresa foi criada, mas não há assentos de usuário livres, então ninguém pôde ser convidado para administrá-la. Aumente o limite de assentos e envie um convite pela página de Usuários.',
                    'invite-failed'   => 'A empresa foi criada, mas o convite não pôde ser emitido. O endereço pode já pertencer a um usuário, ou a empresa pode ainda não ser uma que você possa atribuir. Convide pela página de Usuários.',
                    'mail-failed'     => 'A empresa foi criada e o convite emitido, mas o e-mail não pôde ser enviado. Entregue este link de acesso a :email por um canal confiável — ele expira em 7 dias e a pessoa escolhe a própria senha: :link',
                ],
            ],
        ],

        'edit' => [
            'administrator' => [
                'skipped' => [
                    'title'           => 'Nenhum administrador foi convidado',
                    'disabled'        => 'Os convites estão desativados nas configurações de usuários, então :email não foi convidado. Defina uma senha em vez disso, ou ative os convites.',
                    'missing-role'    => 'O papel “Company Admin” não existe nesta instalação, então ninguém pôde ser convidado.',
                    'seats-exhausted' => 'Esta empresa não tem assentos de usuário livres, então :email não foi convidado. Aumente primeiro o limite de assentos.',
                    'invite-failed'   => 'O convite não pôde ser emitido. O endereço pode já pertencer a um usuário, ou a empresa pode não ser uma que você possa atribuir. Para redefinir a senha de um administrador existente, informe também uma senha.',
                    'mail-failed'     => 'O convite foi emitido, mas o e-mail não pôde ser enviado. Entregue este link de acesso a :email por um canal confiável — ele expira em 7 dias e a pessoa escolhe a própria senha: :link',
                ],
            ],
        ],

        'administrator-account' => [
            'created' => [
                'title' => 'Conta de administrador criada',
                'body'  => ':email já pode entrar com a senha que você definiu. Ela não foi enviada por e-mail: entregue-a você mesmo.',
            ],

            'password-reset' => [
                'title' => 'Senha do administrador alterada',
                'body'  => 'A senha de :email foi alterada. Ela não foi enviada por e-mail: entregue-a você mesmo.',
            ],

            'refused' => [
                'title'             => 'O administrador não foi alterado',
                'missing-email'     => 'Informe o e-mail do administrador junto com a senha.',
                'missing-role'      => 'O papel “Company Admin” não existe nesta instalação, então nenhuma conta foi criada.',
                'not-member'        => ':email pertence a um usuário que não é membro desta empresa, então a senha dele não foi alterada aqui. Gerencie esse usuário pela página de Usuários.',
                'not-allowed'       => 'Você não tem permissão para gerenciar o administrador desta empresa.',
                'seats-exhausted'   => 'Esta empresa não tem assentos de usuário livres, então nenhuma conta foi criada para :email. Aumente primeiro o limite de assentos.',
            ],
        ],
    ],
];
