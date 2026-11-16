<?php

return [
    'title' => 'Contatos',

    'header-actions' => [
        'create' => [
            'label' => 'Criar contato',
        ],

        'import' => [
            'label' => 'Importar do Excel',

            'modal' => [
                'heading'                 => 'Importar contatos de uma planilha',
                'description'             => 'Baixe o arquivo de exemplo, preencha um contato por linha e envie-o aqui. Até :max contatos por envio.',
                'submit'                  => 'Importar contatos',
                'download-template-csv'   => 'Baixar exemplo (CSV)',
                'download-template-excel' => 'Baixar exemplo (Excel)',

                'file' => [
                    'label'  => 'Arquivo preenchido',
                    'helper' => 'Excel (.xlsx, .xls) ou CSV. Mantenha a linha de cabeçalho como está. Coluna obrigatória: :columns. As linhas com problema são informadas pelo número da linha e as demais são importadas.',
                ],
            ],

            'notifications' => [
                'imported'         => ':count contatos importados',
                'skipped'          => ':count linhas foram ignoradas:',
                'nothing-imported' => 'Nenhum contato foi importado',
                'more-errors'      => '... e mais :count linhas.',
                'unreadable'       => 'Não foi possível ler esse arquivo. Envie um arquivo do Excel ou CSV.',
            ],
        ],
    ],

    'tabs' => [
        'individuals' => 'Pessoas físicas',
        'companies'   => 'Empresas',
        'employees'   => 'Colaboradores',
        'customers'   => 'Clientes',
        'vendors'     => 'Fornecedores',
        'archived'    => 'Arquivados',
    ],
];
