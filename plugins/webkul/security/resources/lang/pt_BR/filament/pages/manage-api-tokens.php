<?php

return [
    'navigation' => [
        'label' => 'Tokens de API',
    ],

    'group'      => 'Geral',
    'breadcrumb' => 'Tokens de API',
    'title'      => 'Tokens de API',
    'subheading' => 'Tokens que as integrações de clientes usam para se autenticar na API. Um token carrega os papéis e as empresas permitidas do usuário a quem pertence.',

    'abilities' => [
        'read'  => 'Somente leitura',
        'write' => 'Escrita',
        'all'   => 'Acesso total',
    ],

    'table' => [
        'columns' => [
            'name'      => 'Rótulo',
            'user'      => 'Atua como',
            'abilities' => 'Escopos',
            'last-used' => 'Último uso',
            'expires'   => 'Expira',
            'created'   => 'Emitido',
        ],

        'never-used' => 'Nunca usado',
    ],

    'actions' => [
        'issue' => [
            'label'  => 'Emitir token',
            'submit' => 'Emitir token',

            'fields' => [
                'user'             => 'Atua como usuário',
                'user-helper'      => 'O token pode fazer exatamente o que este usuário pode fazer. Use um usuário dedicado por cliente, nunca um administrador.',
                'name'             => 'Rótulo',
                'name-helper'      => 'Como você reconhecerá este token depois, por exemplo acme-warehouse-sync.',
                'abilities'        => 'Escopos',
                'abilities-helper' => 'Somente leitura não permite POST, PATCH nem DELETE. Acesso total ignora a verificação de escopo.',
            ],
        ],

        'revoke' => [
            'label'       => 'Revogar',
            'heading'     => 'Revogar este token?',
            'description' => 'A integração que o utiliza deixará de funcionar imediatamente. Isso não pode ser desfeito.',
            'bulk'        => 'Revogar selecionados',
        ],
    ],

    'notifications' => [
        'issued' => [
            'title' => 'Token emitido',
            'body'  => 'Copie agora - não será possível exibi-lo novamente.',
        ],

        'revoked'      => 'Token revogado',
        'missing-user' => 'Esse usuário não existe mais.',
    ],

    'plaintext' => [
        'title'       => 'Copie este token agora',
        'description' => 'Ele é armazenado apenas como hash, portanto esta é a única vez em que pode ser exibido. Envie-o ao cliente por um canal confiável.',
    ],

    'empty' => [
        'heading'     => 'Ainda não há tokens de API',
        'description' => 'Emita um para que uma integração de cliente possa se autenticar na API.',
    ],
];
