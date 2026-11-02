<?php

return [
    'administrator' => [
        'title'       => 'Administrador',
        'description' => 'A pessoa que administra esta empresa e gerencia os próprios usuários dela.',
        'none'        => 'Ainda não há administrador para esta empresa.',
        'pending'     => 'convite enviado, ainda não aceito',
        'inactive'    => 'desativado',
        'manage'      => 'Gerenciar este usuário',

        'fields' => [
            'email'                => 'E-mail do administrador',
            'email-helper'         => 'Sem senha, um convite é enviado para este endereço e a pessoa escolhe a própria senha. Deixe em branco para convidar o e-mail da empresa.',
            'email-helper-edit'    => 'Informe um endereço para convidar um novo administrador. Adicione uma senha para criar a conta na hora, ou para redefinir a senha do administrador desta empresa. Deixe em branco para não alterar nada.',
            'password'             => 'Senha do administrador',
            'password-helper'      => 'Opcional. Define a senha agora em vez de enviar um convite, ou a redefine para o administrador desta empresa. Nunca é enviada por e-mail: entregue-a você mesmo.',
            'must-change-password' => 'Deve alterar esta senha no próximo acesso',
        ],
    ],
];
