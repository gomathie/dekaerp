<?php

return [
    'validation' => [
        'upload-failed' => 'Não foi possível armazenar o arquivo do comprovante. Tente novamente.',
    ],
    'relation-manager' => [
        'title' => 'Custos',

        'fields' => [
            'date'           => 'Data',
            'category'       => 'Categoria',
            'payee'          => 'Pago a',
            'amount'         => 'Valor',
            'approved-total' => 'Aprovado',
            'state'          => 'Status',
            'bill'           => 'Fatura de fornecedor',
            'not-billed'     => 'Não faturado',
        ],
    ],

    'margin' => [
        'heading' => 'Receita e custo',
        'revenue' => 'Receita',
        'costs'   => 'Custos',
        'margin'  => 'Margem',
        'helper'  => 'Cobranças faturáveis comparadas aos custos aprovados. Custos em rascunho e rejeitados não são contabilizados.',
    ],

    'actions' => [
        'post-bill' => [
            'label'          => 'Criar fatura de fornecedor',
            'heading'        => 'Criar faturas de fornecedor em rascunho?',
            'description'    => 'Cada custo aprovado e não faturado desta remessa se torna uma fatura em rascunho, uma por beneficiário. Nada é contabilizado: o Financeiro ainda precisa revisá-las e contabilizá-las.',
            'cannot-post'    => 'Estes custos ainda não podem ser faturados',
            'draft-reminder' => 'Criadas como rascunhos. Revise-as e contabilize-as em Contabilidade.',
            'notification'   => '{0}Não restou nada para faturar.|{1}Uma fatura de fornecedor em rascunho foi criada.|[2,*]:count faturas de fornecedor em rascunho foram criadas.',
        ],
    ],
];
