<?php

return [
    'title' => 'Usuários e acessos',

    'table' => [
        'columns' => [
            'name'                 => 'Nome',
            'login'                => 'Acesso (e-mail)',
            'company-admin'        => 'Administrador da empresa',
            'active'               => 'Ativo',
            'must-change-password' => 'Deve alterar a senha',
        ],

        'empty-state' => [
            'heading'     => 'Ainda não há usuários',
            'description' => 'Convide ou crie o administrador na seção Administrador acima.',
        ],

        'actions' => [
            'copy-invitation-link' => [
                'label' => 'Copiar link do convite',
                'invitation' => 'Convite pendente',
                'link' => 'Link do convite',
            ],
            'set-password' => [
                'label'             => 'Definir senha',
                'modal-heading'     => 'Definir a senha de :email',
                'modal-description' => 'As senhas atuais não podem ser exibidas, pois são armazenadas com criptografia de mão única. Defina uma nova e entregue-a você mesmo ao usuário: ela não é enviada por e-mail.',

                'form' => [
                    'password'             => 'Nova senha',
                    'must-change-password' => 'Deve alterar esta senha no próximo acesso',
                ],
            ],
        ],
    ],
];
