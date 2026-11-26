<?php

return [
    'title' => 'Despesa',

    'navigation' => [
        'title' => 'Despesas',
    ],

    'form' => [
        'fields' => [
            'number'              => 'Número',
            'date'                => 'Data',
            'amount'              => 'Valor',
            'currency'            => 'Moeda',
            'expense-account'     => 'Conta de despesa',
            'paid-from'           => 'Pago com',
            'vendor'              => 'Fornecedor',
            'vendor-name'         => 'Nome do fornecedor',
            'tax'                 => 'Imposto',
            'tax-amount'          => 'Valor do imposto',
            'amount-includes-tax' => 'O valor inclui o imposto',
            'reference'           => 'Referência',
            'note'                => 'Observação',
            'receipt'             => 'Comprovante',
            'entry'               => 'Lançamento contábil',
            'reason'              => 'Motivo da alteração',
        ],

        'hints' => [
            'currency'            => 'Outra moeda usa a taxa do dia, como nas faturas.',
            'expense-account'     => 'Somente as contas de despesa desta empresa. A última usada é sugerida primeiro.',
            'paid-from'           => 'Todas as contas bancárias e de caixa da empresa.',
            'vendor'              => 'Opcional. Digite um nome novo para adicionar o fornecedor.',
            'tax'                 => 'Opcional. Em branco: sem linha de imposto.',
            'no-tax'              => 'Sem imposto',
            'amount-includes-tax' => 'Ligado: o imposto é descontado do valor. Desligado: o imposto é somado.',
            'receipt'             => 'Uma foto ou um PDF, no máximo 10 MB. No celular, você pode tirar uma foto.',
            'reason'              => 'Esta despesa já está na contabilidade. Diga por que ela muda.',
        ],

        'tax-included' => ':tax (incluído)',

        'summary' => [
            'empty' => 'Informe um valor para ver o que será lançado.',
            'posts' => 'Ao salvar, :total sai da conta de pagamento: :net para a conta de despesa e :tax de imposto.',
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Data',
            'expense-account' => 'Conta de despesa',
            'paid-from'       => 'Pago com',
            'vendor'          => 'Fornecedor',
            'reference'       => 'Referência',
            'amount'          => 'Valor',
            'receipt'         => 'Comprovante',
        ],

        'total'  => 'Total',
        'export' => 'Exportar',

        'filters' => [
            'period'          => 'Período',
            'from'            => 'De',
            'until'           => 'Até',
            'paid-from'       => 'Pago com',
            'expense-account' => 'Conta de despesa',
            'vendor'          => 'Fornecedor',
            'with-receipt'    => 'Com comprovante',
        ],
    ],

    'actions' => [
        'new'          => 'Nova despesa',
        'save'         => 'Salvar',
        'save-and-new' => 'Salvar e nova',

        'delete' => [
            'label'       => 'Excluir',
            'description' => 'O lançamento da despesa é cancelado, então na contabilidade o dinheiro volta para a conta de pagamento.',
            'done'        => 'Despesa excluída',
        ],
    ],

    'notifications' => [
        'created' => 'Despesa salva e lançada',
        'saved'   => 'Despesa atualizada',
        'refused' => 'A despesa não foi salva',
        'failed'  => 'Algo deu errado. Nada foi alterado.',
    ],

    'export' => [
        'columns' => [
            'number'          => 'Número',
            'date'            => 'Data',
            'expense-account' => 'Conta de despesa',
            'paid-from'       => 'Pago com',
            'vendor'          => 'Fornecedor',
            'reference'       => 'Referência',
            'currency'        => 'Moeda',
            'amount'          => 'Valor',
            'tax'             => 'Imposto',
            'amount-company'  => 'Valor na moeda da empresa',
            'note'            => 'Observação',
        ],

        'notification' => [
            'completed' => 'A exportação de despesas foi concluída: :count linhas exportadas.',
            'failed'    => ':count linhas não puderam ser exportadas.',
        ],
    ],
];
