<?php

return [
    'breadcrumb' => 'Gerenciar usuários',
    'title'      => 'Gerenciar usuários',
    'group'      => 'Geral',

    'navigation' => [
        'label' => 'Gerenciar usuários',
    ],

    'form' => [
        'enable-user-invitation' => [
            'label'       => 'Habilitar convite de usuário',
            'helper-text' => 'Permitir que usuários convidem outros usuários para a aplicação.',
        ],

        'enable-reset-password' => [
            'label'       => 'Habilitar redefinição de senha',
            'helper-text' => 'Permitir que usuários redefinam suas senhas.',
        ],

        'default-role' => [
            'label'       => 'Função padrão',
            'helper-text' => 'A função padrão atribuída a novos usuários.',
        ],
        'company-admin-business-role' => [
            'label'       => 'Função de negócio do administrador da empresa',
            'helper-text' => 'Atribuída a cada novo administrador da empresa junto com Company Admin, para que ele possa gerir a empresa. As funções da plataforma não são oferecidas.',
        ],

        'default-company' => [
            'label'       => 'Empresa padrão',
            'helper-text' => 'A empresa padrão atribuída a novos usuários.',
        ],
    ],
];
