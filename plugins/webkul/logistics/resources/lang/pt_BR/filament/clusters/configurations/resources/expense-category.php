<?php

return [
    'navigation' => [
        'title' => 'Categorias de despesa',
    ],

    'form' => [
        'fields' => [
            'requires-receipt'         => 'Comprovante obrigatório',
            'is-subcontracting'        => 'Custo de subcontratado ou transportadora',
            'is-subcontracting-helper' => 'Os custos desta categoria são pagos a um fornecedor (transportadora, agente) e faturados por fornecedor.',
        ],
    ],

    'table' => [
        'columns' => [
            'requires-receipt'  => 'Comprovante obrigatório',
            'is-subcontracting' => 'Subcontratação',
        ],
    ],
];
