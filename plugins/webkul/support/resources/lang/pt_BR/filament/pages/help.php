<?php

return [
    'navigation' => [
        'label' => 'Ajuda',
    ],

    'title'      => 'Ajuda',
    'heading'    => 'Ajuda e recursos',
    'subheading' => 'Tudo de que você precisa para aproveitar ao máximo o :app.',

    'guides' => [
        'group' => 'Guias',

        'superadmin' => [
            'title'       => 'Guia do superadministrador',
            'description' => 'Administre a própria instalação do :app: módulos, empresas, funções de administrador, atualizações e manutenção.',
            'button'      => 'Abrir o guia do superadministrador',
        ],

        'admin' => [
            'title'       => 'Guia do administrador',
            'description' => 'Configure o :app para sua empresa, equipes, módulos e controles antes do início do trabalho diário.',
            'button'      => 'Lista de configuração do administrador',
            'sections'    => [
                [
                    'title' => 'Configurar a empresa',
                    'items' => [
                        '0' => 'Crie ou revise empresas, armazéns, moedas, impostos e configurações fiscais.',
                        '1' => 'Atribua os usuários às empresas que podem acessar e confirme o seletor de empresa ativa.',
                        '2' => 'Configure sequências, condições de pagamento, produtos, unidades de medida e modelos de documento antes de iniciar as transações.',
                    ],
                ],
                [
                    'title' => 'Controlar o acesso',
                    'items' => [
                        '0' => 'Crie funções para as equipes de finanças, vendas, estoque, RH, projetos e gestão.',
                        '1' => 'Conceda permissões de módulo apenas aos usuários que precisam delas.',
                        '2' => 'Revise campos personalizados, importações, exportações e tokens de API antes de liberar acesso amplo.',
                    ],
                ],
                [
                    'title' => 'Operar e manter',
                    'items' => [
                        '0' => 'Instale apenas os módulos que a empresa usa e oculte ou remova os demais.',
                        '1' => 'Acompanhe conversas, anexos, aprovações e responsáveis pelos registros durante a implantação.',
                        '2' => 'Mantenha as configurações de e-mail, PDF, backup e atualização alinhadas à política de implantação.',
                    ],
                ],
            ],
        ],

        'user' => [
            'title'          => 'Guia do usuário',
            'description'    => 'Use o guia integrado com os roteiros da DEKA e as páginas de referência dos módulos para o trabalho diário no :app.',
            'button'         => 'Abrir o guia do usuário',
            'full_heading'   => 'Guia completo do usuário',
            'count'          => '{0} Nenhuma página de guia|{1} 1 página de guia|[2,*] :count páginas de guia',
            'category_count' => '{0} Nenhuma página|{1} 1 página|[2,*] :count páginas',
            'callouts'       => [
                'tip'       => 'Dica',
                'note'      => 'Observação',
                'important' => 'Importante',
            ],
            'sections'    => [
                [
                    'title' => 'Primeiros passos',
                    'items' => [
                        '0' => 'Visão geral da plataforma e navegação',
                        '1' => 'Configuração multiempresa e seletor de empresa',
                    ],
                ],
                [
                    'title' => 'Processos principais',
                    'items' => [
                        '0' => 'Vender: vendas e faturamento',
                        '1' => 'Comprar: compras e contabilidade',
                        '2' => 'Produzir e movimentar: estoque e produção',
                        '3' => 'Gerir: projetos, RH e manutenção',
                        '4' => 'Plataforma, API e hospedagem própria',
                    ],
                ],
                [
                    'title' => 'Referência dos módulos: 12 módulos / 69 páginas',
                    'items' => [
                        '0' => 'Faturas, Vendas, Compras, Estoque, Fabricação, Manutenção',
                        '1' => 'Contatos, Projetos, Funcionários, Recrutamento, Ausências',
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'group' => 'Serviços',

        'cloud' => [
            'title'       => 'Hospedagem em nuvem',
            'description' => 'Hospedagem em nuvem gerenciada e econômica — implante seu ERP em minutos, totalmente otimizado, seguro e escalável.',
            'button'      => 'Ver preços',
        ],

        'support' => [
            'title'       => 'Suporte e manutenção',
            'description' => 'Suporte técnico dedicado e planos de manutenção contínua para manter seu ERP seguro, atualizado e funcionando sem problemas.',
            'button'      => 'Ver detalhes',
        ],

        'paid' => [
            'title'       => 'Serviços pagos',
            'description' => 'Ajuda especializada para desenvolvimento de módulos, customização, migração de dados, atualizações de versão e integrações sob medida.',
            'button'      => 'Começar',
        ],
    ],

    'resources' => [
        'group' => 'Recursos e documentação',

        'extensions' => [
            'title'       => 'Módulos',
            'description' => 'Explore complementos oficiais e da comunidade para estender o :app com novos módulos e recursos.',
            'button'      => 'Explorar módulos',
        ],

        'docs' => [
            'title'       => 'Documentação dev',
            'description' => 'Guias e referências para desenvolvedores que ajudam você a criar plugins, customizar módulos e estender a plataforma.',
            'button'      => 'Ler documentação',
        ],

        'guide' => [
            'title'       => 'Guia do usuário',
            'description' => 'Guias passo a passo cobrindo configuração inicial, ajustes e uso diário de cada módulo do ERP.',
            'button'      => 'Abrir guia',
        ],

        'website' => [
            'title'       => 'Site do produto',
            'description' => 'Visão geral dos módulos, notas de versão e tudo o que o :app pode fazer, no site do produto.',
            'button'      => 'Visitar site',
        ],
    ],

    'empty' => 'Os links de ajuda ainda não foram configurados para esta instalação do :app. Entre em contato com o administrador do sistema.',

    'contact' => [
        'title'       => 'Ainda precisa de ajuda?',
        'description' => 'Fale com nossa equipe sobre hospedagem, implementação, desenvolvimento customizado ou qualquer outra necessidade.',
        'live_chat'   => 'Chat ao vivo',
        'button'      => 'Fale Conosco',
    ],
];
