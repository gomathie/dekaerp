<?php

return [
    'navigation' => [
        'title' => 'Motoristas',
    ],

    'form' => [
        'fields' => [
            'company'            => 'Empresa',
            'employee'           => 'Funcionário',
            'partner'            => 'Contato da transportadora',
            'name'               => 'Nome',
            'phone'              => 'Telefone',
            'license-number'     => 'Número da CNH',
            'license-class'      => 'Categoria da CNH',
            'license-expires-at' => 'Validade da CNH',
            'is-active'          => 'Ativo',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'Nome',
            'phone'              => 'Telefone',
            'employee'           => 'Funcionário',
            'partner'            => 'Contato da transportadora',
            'license-number'     => 'Número da CNH',
            'license-expires-at' => 'Validade da CNH',
            'company'            => 'Empresa',
            'is-active'          => 'Ativo',
        ],

        'filters' => [
            'license-attention' => 'Vencida ou próxima do vencimento',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'Motorista',
            'license' => 'CNH',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'Adicionar motoristas a partir de funcionários',

            'fields' => [
                'employees' => 'Funcionários',
            ],

            'notification' => [
                'title' => 'Importação de funcionários concluída',
                'body'  => ':created criados, :skipped ignorados.',
            ],
        ],
    ],
];
