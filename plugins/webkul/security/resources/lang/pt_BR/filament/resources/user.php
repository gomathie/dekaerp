<?php

return [
    'title' => 'Usuários',

    'navigation' => [
        'title' => 'Usuários',
    ],

    'global-search' => [
        'email' => 'E-mail',
    ],

    'form' => [
        'validation' => [
            'cannot-remove-last-admin'        => 'Não é possível remover a função de administrador do último usuário administrador.',
            'company-invalid'                 => 'Uma ou mais empresas selecionadas são inválidas.',
            'company-not-assigned'            => 'Você só pode atribuir usuários às empresas atribuídas à sua conta.',
            'company-required'                => 'Selecione pelo menos uma empresa.',
            'first-user-must-be-admin'        => 'O primeiro usuário do sistema deve receber uma função de administrador.',
            'role-invalid'                    => 'Selecione pelo menos uma função válida.',
            'role-not-assignable'             => 'Você só pode atribuir funções não protegidas cujas permissões você possui.',
            'self-administration-forbidden'   => 'Você não pode alterar suas próprias funções ou atribuições de empresa.',
            'target-administration-forbidden' => 'Somente um Super Admin pode modificar um administrador protegido.',
        ],

        'sections' => [
            'general-information' => [
                'title'  => 'Informações gerais',
                'fields' => [
                    'name'                        => 'Nome',
                    'email'                       => 'E-mail',
                    'password'                    => 'Senha',
                    'password-confirmation'       => 'Confirmação de senha',
                    'must-change-password'        => 'Exigir uma nova senha no próximo acesso',
                    'must-change-password-helper' => 'Use quando você mesmo definir a senha e a repassar. A pessoa ficará na página de perfil até substituí-la, e a exigência é removida assim que isso acontecer.',
                ],
            ],

            'permissions' => [
                'title'  => 'Permissões',
                'fields' => [
                    'roles'                                    => 'Funções',
                    'permissions'                              => 'Permissões',
                    'resource-permission'                      => 'Permissão de recurso',
                    'resource-permission-self-change-disabled' => 'Você não pode alterar sua própria permissão de recurso. Peça a outro administrador para atualizá-la.',
                    'teams'                                    => 'Equipes',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'  => 'Idioma e status',
                'fields' => [
                    'language' => 'Idioma preferido',
                    'status'   => 'Status',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multiempresa',
                'allowed-companies'           => 'Empresas permitidas',
                'default-company'             => 'Empresa padrão',
                'default-company-not-allowed' => 'A empresa padrão deve ser uma das empresas permitidas.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Avatar',
            'name'                => 'Nome',
            'email'               => 'E-mail',
            'teams'               => 'Equipes',
            'role'                => 'Função',
            'resource-permission' => 'Permissão de recurso',
            'default-company'     => 'Empresa padrão',
            'allowed-company'     => 'Empresa permitida',
            'created-by'          => 'Criado por',
            'created-at'          => 'Criado em',
            'updated-at'          => 'Atualizado em',
        ],

        'filters' => [
            'resource-permission' => 'Permissão de recurso',
            'teams'               => 'Equipes',
            'roles'               => 'Funções',
            'default-company'     => 'Empresa padrão',
            'allowed-companies'   => 'Empresas permitidas',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Usuário editado',
                    'body'  => 'O usuário foi editado com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Usuário excluído',
                    'body'  => 'O usuário foi excluído com sucesso.',
                    'error' => [
                        'title' => 'Usuário não pode ser excluído',
                        'body'  => 'Este é um usuário padrão ou você não pode excluir a si mesmo.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Usuário restaurado',
                    'body'  => 'O usuário foi restaurado com sucesso.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Usuários restaurados',
                    'body'  => 'Os usuários foram restaurados com sucesso.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Usuários excluídos',
                    'body'  => 'Os usuários foram excluídos com sucesso.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Usuários excluídos permanentemente',
                    'body'  => 'Os usuários foram excluídos permanentemente com sucesso.',
                    'error' => [
                        'title' => 'Usuário não pôde ser excluído',
                        'body'  => 'O usuário não pode ser excluído porque está em uso no momento.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Usuários criados',
                    'body'  => 'Os usuários foram criados com sucesso.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Informações gerais',
                'entries' => [
                    'name'                  => 'Nome',
                    'email'                 => 'E-mail',
                    'password'              => 'Senha',
                    'password-confirmation' => 'Confirmação de senha',
                ],
            ],

            'permissions' => [
                'title'   => 'Permissões',
                'entries' => [
                    'roles'               => 'Funções',
                    'permissions'         => 'Permissões',
                    'resource-permission' => 'Permissão de recurso',
                    'teams'               => 'Equipes',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'   => 'Idioma e status',
                'entries' => [
                    'language' => 'Idioma preferido',
                    'status'   => 'Status',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multiempresa',
                'allowed-companies'           => 'Empresas permitidas',
                'default-company'             => 'Empresa padrão',
                'default-company-not-allowed' => 'A empresa padrão deve ser uma das empresas permitidas.',
            ],
        ],
    ],
];
