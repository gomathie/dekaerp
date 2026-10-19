<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'Tempo de espera em :stop (:minutes min acima da tolerância gratuita)',
    ],

    'charges' => [
        'title'  => 'Cobranças',
        'fields' => [
            'product'            => 'Serviço',
            'description'        => 'Descrição',
            'quantity'           => 'Quantidade',
            'price-unit'         => 'Preço unitário',
            'discount'           => 'Desconto (%)',
            'taxes'              => 'Impostos',
            'is-billable'        => 'Faturável',
            'is-billable-helper' => 'Somente cobranças faturáveis são incluídas quando uma fatura é criada.',
            'invoiced'           => 'Faturado',
        ],
    ],

    'invoices' => [
        'title'   => 'Faturas',
        'columns' => [
            'number'        => 'Fatura',
            'state'         => 'Status',
            'payment-state' => 'Pagamento',
            'date'          => 'Data',
            'total'         => 'Total',
        ],
        'actions' => [
            'open' => 'Abrir em Contabilidade',
        ],
    ],

    'unbilled' => [
        'title'   => 'Cobranças não faturadas',
        'columns' => [
            'shipment'    => 'Remessa',
            'customer'    => 'Cliente',
            'description' => 'Cobrança',
            'quantity'    => 'Qtd.',
            'subtotal'    => 'Valor',
            'total'       => 'Total',
        ],
        'filters' => [
            'currency' => 'Moeda',
            'shipment' => 'Remessa',
        ],
    ],

    'actions' => [
        'create-invoice' => [
            'label'              => 'Criar fatura',
            'heading'            => 'Faturar esta remessa?',
            'description'        => 'Cada cobrança faturável que ainda não foi faturada se torna uma linha em uma nova fatura de cliente. Cobranças já faturadas permanecem inalteradas.',
            'notification'       => 'Fatura criada.',
            'notification-body'  => 'A fatura :invoice está em Contabilidade como rascunho para você revisar antes de contabilizá-la.',
            'nothing-to-invoice' => 'Nada para faturar',
        ],
    ],
];
