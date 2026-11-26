<?php

return [
    'title'      => 'Fatura recorrente',
    'navigation' => 'Faturas recorrentes',

    'sections' => [
        'details' => 'Programação',
        'lines'   => 'Linhas da fatura',
        'history' => 'Histórico de faturas',
    ],

    'fields' => [
        'name'         => 'Nome do modelo',
        'customer'     => 'Cliente',
        'journal'      => 'Diário de vendas',
        'currency'     => 'Moeda',
        'responsible'  => 'Responsável',
        'payment-term' => 'Condições de pagamento',
        'next-date'    => 'Data da próxima fatura',
        'frequency'    => 'Repetir',
        'ends-on'      => 'Data de término',
        'auto-email'   => 'Confirmar e enviar por e-mail automaticamente',
        'notes'        => 'Observações',
        'product'      => 'Produto ou serviço',
        'description'  => 'Descrição',
        'quantity'     => 'Quantidade',
        'price'        => 'Preço unitário',
        'discount'     => 'Desconto (%)',
        'taxes'        => 'Impostos',
        'status'       => 'Status',
        'last-issue'   => 'Último problema',
        'invoice'      => 'Fatura',
        'period-start' => 'Início do período',
        'period-end'   => 'Fim do período',
    ],

    'frequencies' => [
        'weekly'    => 'Semanal',
        'monthly'   => 'Mensal',
        'quarterly' => 'Trimestral',
        'yearly'    => 'Anual',
    ],

    'actions' => [
        'create'       => 'Nova fatura recorrente',
        'add-line'     => 'Adicionar linha',
        'generate-now' => 'Criar a próxima fatura agora',
        'pause'        => 'Pausar',
        'resume'       => 'Retomar',
        'stop'         => 'Parar',
        'history'      => 'Histórico',
    ],

    'errors' => [
        'inactive-customer' => 'Este cliente está inativo. Atualize o cliente antes de criar uma fatura.',
        'customer-email'    => 'Adicione um e-mail a este cliente antes de ativar o envio automático.',
        'journal'           => 'Escolha um diário de vendas ativo desta empresa.',
        'currency'          => 'Escolha uma moeda disponível.',
        'lines'             => 'Adicione pelo menos uma linha de fatura.',
        'locked-period'     => 'A data da próxima fatura está em um período contábil bloqueado.',
        'frequency'         => 'Escolha uma frequência suportada.',
        'company-mismatch'  => 'Este valor deve pertencer à empresa do modelo.',
        'responsible'       => 'Escolha um usuário desta empresa.',
        'state'             => 'Esta fatura recorrente não pode passar para esse status.',
        'end-date'          => 'A data de término deve ser igual ou posterior à data da próxima fatura.',
        'email-failed'      => 'A fatura :number foi criada, mas o e-mail não pôde ser enviado. Abra a fatura e envie-a novamente.',
    ],

    'notifications' => [
        'created' => [
            'title' => 'Uma fatura recorrente está pronta para revisão',
            'body'  => 'O rascunho da fatura :number foi criado. Revise-o e envie-o ao cliente.',
            'view'  => 'Abrir fatura',
        ],
    ],
];
