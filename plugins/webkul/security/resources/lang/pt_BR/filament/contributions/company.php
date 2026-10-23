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
            'email'        => 'E-mail do administrador',
            'email-helper' => 'Um convite é enviado para este endereço, onde a pessoa define o próprio nome e senha. Deixe em branco para usar o e-mail da empresa.',
        ],
    ],
];
