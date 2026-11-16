<?php

return [
    'navigation' => [
        'title' => 'Despesas',
    ],

    'form' => [
        'sections' => [
            'details'   => 'Detalhes da despesa',
            'reference' => 'Referência e comprovante',
        ],
        'fields' => [
            'company'          => 'Empresa',
            'date'             => 'Data',
            'amount'           => 'Valor',
            'currency'         => 'Moeda',
            'category'         => 'Categoria',
            'paid-by'          => 'Pago por',
            'employee'         => 'Funcionário',
            'payee'            => 'Fornecedor ou transportadora',
            'shipment'         => 'Remessa',
            'trip'             => 'Viagem',
            'vehicle'          => 'Veículo',
            'vendor-reference' => 'Referência do fornecedor',
            'description'      => 'Descrição',
            'receipt'          => 'Comprovante',
        ],
    ],

    'table' => [
        'columns' => [
            'date'        => 'Data',
            'category'    => 'Categoria',
            'description' => 'Descrição',
            'amount'      => 'Valor',
            'state'       => 'Status',
            'shipment'    => 'Remessa',
            'company'     => 'Empresa',
        ],
        'filters' => [
            'state'    => 'Status',
            'category' => 'Categoria',
        ],
    ],

    'infolist' => [
        'sections' => [
            'summary' => 'Despesa',
        ],
        'fields' => [
            'state'       => 'Status',
            'date'        => 'Data',
            'amount'      => 'Valor',
            'category'    => 'Categoria',
            'shipment'    => 'Remessa',
            'trip'        => 'Viagem',
            'vehicle'     => 'Veículo',
            'receipt'     => 'Comprovante',
            'description' => 'Descrição',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'Enviar',
            'heading'      => 'Enviar esta despesa?',
            'notification' => 'Despesa enviada.',
        ],
        'approveExpense' => [
            'label'        => 'Aprovar',
            'heading'      => 'Aprovar esta despesa?',
            'notification' => 'Despesa aprovada.',
        ],
        'rejectExpense' => [
            'label'        => 'Rejeitar',
            'heading'      => 'Rejeitar esta despesa?',
            'notification' => 'Despesa rejeitada.',
        ],
        'cannot-proceed' => 'Esta despesa ainda não está pronta',
    ],
];
