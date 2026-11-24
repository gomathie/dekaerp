<?php

return [
    'administrator' => [
        'title'       => 'Administrador',
        'description' => 'A pessoa que administra esta empresa e gerencia os próprios usuários dela.',
        'none'        => 'Ainda não há administrador para esta empresa.',
        'pending'     => 'convite enviado, ainda não aceito',
        'inactive'    => 'desativado',
        'manage'      => 'Gerenciar este usuário',

        'status' => [
            'active'   => 'Ativo',
            'inactive' => 'Desativado',
        ],

        'set-password' => [
            'label'       => 'Definir senha',
            'heading'     => 'Nova senha para :email',
            'description' => 'Ela não é enviada por e-mail: entregue-a você mesmo.',
        ],

        'add' => [
            'label'                => 'Adicionar administrador',
            'heading'              => 'Adicionar um administrador',
            'submit'               => 'Adicionar',
            'method'               => 'Como a pessoa deve obter acesso?',
            'invite'               => 'Enviar um convite',
            'invite-description'   => 'A pessoa recebe um e-mail e escolhe a própria senha.',
            'password'             => 'Definir uma senha agora',
            'password-description' => 'Para quando o e-mail não chega ou o cliente liga. Também redefine a senha de alguém que já está nesta empresa.',
        ],

        'invitation' => [
            'waiting'          => 'Convite enviado, ainda não aceito',
            'pending'          => 'Pendente',
            'expires'          => 'Pendente até :date',
            'resend'           => 'Reenviar',
            'copy-link'        => 'Copiar link',
            'copy-heading'     => 'Link de acesso para :email',
            'copy-description' => 'Qualquer pessoa com este link pode configurar esta conta de administrador. Envie-o apenas para ela, por um canal de confiança.',
            'link'             => 'Link',
        ],

        'fields' => [
            'email'                => 'E-mail do administrador',
            'email-helper'         => 'Sem senha, um convite é enviado para este endereço e a pessoa escolhe a própria senha. Deixe em branco para convidar o e-mail da empresa.',
            'email-helper-edit'    => 'Informe um endereço para convidar um novo administrador. Adicione uma senha para criar a conta na hora, ou para redefinir a senha do administrador desta empresa. Deixe em branco para não alterar nada.',
            'password'             => 'Senha do administrador',
            'password-helper'      => 'Opcional. Define a senha agora em vez de enviar um convite, ou a redefine para o administrador desta empresa. Nunca é enviada por e-mail: entregue-a você mesmo.',
            'must-change-password' => 'Deve alterar esta senha no próximo acesso',
            'name'                 => 'Nome do administrador',
        ],
    ],

    'currencies' => [
        'title'       => 'Moedas',
        'description' => 'Moedas que esta empresa pode usar em faturas, contas a pagar, cotações e pedidos de compra, além da moeda padrão. Deixe vazio para permitir todas as moedas ativas.',
        'fields'      => [
            'permitted' => 'Moedas adicionais',
            'available' => 'Disponíveis nos documentos agora: :list',
        ],
    ],

    'account' => [
        'title'       => 'Tipo de conta',
        'description' => 'Se esta empresa está em teste ou é comercial. Somente a equipe de instalação pode escolher, e apenas na criação.',

        'fields' => [
            'type'         => 'Tipo de conta',
            'commercial'   => 'Comercial',
            'test'         => 'Teste',
            'days'         => 'Dias de teste',
            'days-helper'  => 'Quantos dias o teste dura. Dá para acrescentar mais depois. Um teste não é faturado.',
        ],
    ],
];
