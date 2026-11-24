<?php

return [
    'navigation' => [
        'title' => 'Relatórios',
    ],
    'common' => [
        'from-to'                  => ':report - De :from até :to',
        'expand-all'               => 'Expandir tudo',
        'collapse-all'             => 'Recolher tudo',
        'account'                  => 'Conta',
        'date'                     => 'Data',
        'communication'            => 'Comunicação',
        'partner'                  => 'Parceiro',
        'journal'                  => 'Diário',
        'invoice-date'             => 'Data da fatura',
        'due-date'                 => 'Data de vencimento',
        'debit'                    => 'Débito',
        'credit'                   => 'Crédito',
        'balance'                  => 'Saldo',
        'total'                    => 'Total',
        'opening-balance'          => 'Saldo inicial',
        'initial-balance'          => 'Saldo inicial',
        'end-balance'              => 'Saldo final',
        'not-due'                  => 'A vencer',
        'no-data'                  => 'Nenhum dado disponível',
        'no-accounts-transactions' => 'Nenhuma conta com transações neste período',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Balanço patrimonial',
                'group' => 'Relatórios de demonstrações',
            ],
            'actions' => [
                'export-excel' => 'Exportar para Excel',
                'export-pdf'   => 'Exportar para PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'journals'   => 'Diários',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'ATIVOS',
                        'total-label' => 'Total de ATIVOS',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Ativos circulantes',
                                'total-label' => 'Total de ativos circulantes',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Ativos imobilizados',
                                'total-label' => 'Total de ativos imobilizados',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Ativos não circulantes',
                                'total-label' => 'Total de ativos não circulantes',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'PASSIVOS',
                        'total-label' => 'Total de PASSIVOS',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Passivos circulantes',
                                'total-label' => 'Total de passivos circulantes',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Passivos não circulantes',
                                'total-label' => 'Total de passivos não circulantes',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'PATRIMÔNIO LÍQUIDO',
                        'total-label' => 'Total do PATRIMÔNIO LÍQUIDO',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Lucros não alocados',
                                'current-year'   => 'Lucros não alocados do ano atual',
                                'previous-years' => 'Lucros não alocados de anos anteriores',
                                'total-label'    => 'Total de lucros não alocados',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Lucros retidos',
                                'total-label' => 'Total de lucros retidos',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'PASSIVOS + PATRIMÔNIO LÍQUIDO',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Lucros e perdas',
                'group' => 'Relatórios de demonstrações',
            ],
            'actions' => [
                'export-excel' => 'Exportar para Excel',
                'export-pdf'   => 'Exportar para PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'journals'   => 'Diários',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'RECEITA',
                        'total-label'   => 'Receita total',
                        'empty-message' => 'Nenhuma conta de receita com transações neste período',
                    ],
                    'expenses' => [
                        'title'         => 'DESPESAS',
                        'total-label'   => 'Total de despesas',
                        'empty-message' => 'Nenhuma conta de despesa com transações neste período',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Razão geral',
                'group' => 'Relatórios de auditoria',
            ],
            'actions' => [
                'export-excel' => 'Exportar para Excel',
                'export-pdf'   => 'Exportar para PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'journals'   => 'Diários',
            ],
        ],
        'tax-summary' => [
            'navigation' => [
                'title' => 'Resumo de impostos',
                'group' => 'Relatórios de auditoria',
            ],
            'actions'  => ['export-excel' => 'Exportar para Excel'],
            'filters'  => ['date-range' => 'Período'],
            'columns'  => ['tax' => 'Imposto', 'rate' => 'Alíquota', 'base' => 'Base de cálculo', 'amount' => 'Imposto'],
            'sections' => ['sales' => 'Sobre vendas (recolhido)', 'purchases' => 'Sobre compras (pago)'],
            'totals'   => [
                'collected'       => 'Total recolhido',
                'paid'            => 'Total pago',
                'net'             => 'Líquido',
                'net-payable'     => 'Imposto líquido a pagar',
                'net-reclaimable' => 'Imposto líquido a recuperar',
            ],
            'empty' => 'Nenhum imposto neste período.',
            'note'  => 'Com base em faturas, contas, notas de crédito e reembolsos lançados e datados no período. Notas de crédito e reembolsos reduzem os totais. Confirme com seu contador antes de declarar.',
        ],
        'cash-flow' => [
            'navigation' => [
                'title' => 'Fluxo de caixa',
                'group' => 'Relatórios de demonstrações',
            ],
            'actions'  => ['export-excel' => 'Exportar para Excel'],
            'filters'  => ['date-range' => 'Período'],
            'columns'  => ['account' => 'Origem ou destino', 'in' => 'Entradas', 'out' => 'Saídas', 'net' => 'Líquido'],
            'sections' => ['operating' => 'Atividades operacionais', 'investing' => 'Atividades de investimento', 'financing' => 'Atividades de financiamento'],
            'totals'   => [
                'opening' => 'Caixa no início do período',
                'section' => 'Caixa líquido',
                'net'     => 'Variação líquida do caixa',
                'closing' => 'Caixa no fim do período',
            ],
            'other' => 'Outros',
            'empty' => 'Nenhum movimento de caixa neste período.',
            'note'  => 'Com base em lançamentos nas contas de banco e caixa. Cada lançamento aparece sob a conta da contrapartida. Transferências entre suas próprias contas de banco e caixa ficam de fora.',
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Balancete',
                'group' => 'Relatórios de auditoria',
            ],
            'actions' => [
                'export-excel' => 'Exportar para Excel',
                'export-pdf'   => 'Exportar para PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'journals'   => 'Diários',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Razão de parceiros',
                'group' => 'Relatórios de parceiros',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'date-range' => 'Intervalo de datas',
                'partners'   => 'Parceiros',
                'journals'   => 'Diários',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Contas a receber por vencimento',
                'group' => 'Relatórios de parceiros',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'as-of'         => 'Em',
                'based-on'      => 'Baseado em',
                'period-length' => 'Duração do período (dias)',
                'journals'      => 'Diários',
                'partners'      => 'Parceiros',
                'entries'       => 'Lançamentos',
                'options'       => [
                    'due-date'       => 'Data de vencimento',
                    'invoice-date'   => 'Data da fatura',
                    'days-30'        => '30 dias',
                    'days-60'        => '60 dias',
                    'days-90'        => '90 dias',
                    'posted-entries' => 'Lançamentos publicados',
                    'all-entries'    => 'Todos os lançamentos',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Contas a pagar por vencimento',
                'group' => 'Relatórios de parceiros',
            ],
            'actions' => [
                'export-excel' => 'Exportar Excel',
                'export-pdf'   => 'Exportar PDF',
            ],
            'filters' => [
                'as-of'         => 'Em',
                'based-on'      => 'Baseado em',
                'period-length' => 'Duração do período (dias)',
                'journals'      => 'Diários',
                'partners'      => 'Parceiros',
                'entries'       => 'Lançamentos',
                'options'       => [
                    'due-date'       => 'Data de vencimento',
                    'invoice-date'   => 'Data da fatura',
                    'days-30'        => '30 dias',
                    'days-60'        => '60 dias',
                    'days-90'        => '90 dias',
                    'posted-entries' => 'Lançamentos publicados',
                    'all-entries'    => 'Todos os lançamentos',
                ],
            ],
        ],
        'multi-currency' => [
            'navigation'       => [
                'title' => 'Multimoeda',
                'group' => 'Relatórios de auditoria',
            ],
            'actions'          => [
                'export-excel' => 'Exportar para Excel',
            ],
            'filters'          => [
                'date-range'     => 'Período',
                'side'           => 'Documentos',
                'sales'          => 'Vendas (faturas e notas de crédito)',
                'purchases'      => 'Compras (faturas de fornecedor e reembolsos)',
                'currency'       => 'Moeda',
                'all-currencies' => 'Todas as moedas',
            ],
            'note'             => 'Os valores em :base usam a taxa de câmbio com que cada documento foi lançado, salvo quando a coluna indica a taxa de hoje.',
            'empty'            => 'Nenhuma fatura lançada neste período.',
            'documents-count'  => '{1} 1 documento|[2,*] :count documentos',
            'company-currency' => 'Moeda da empresa',
            'figures'          => [
                'invoiced' => 'Faturado',
                'received' => 'Recebido',
                'open'     => 'Em aberto',
            ],
            'average-rate'     => 'Taxa média: :rate',
            'sections'         => [
                'by-currency' => 'Por moeda',
                'exchange'    => 'Ganhos e perdas cambiais lançados',
                'documents'   => 'Documentos',
            ],
            'columns'          => [
                'currency'      => 'Moeda',
                'invoiced'      => 'Faturado',
                'average-rate'  => 'Taxa média',
                'invoiced-base' => 'Faturado em :base',
                'open'          => 'Em aberto',
                'open-base'     => 'Em aberto em :base (lançado)',
                'open-today'    => 'Em aberto em :base (hoje)',
                'difference'    => 'Diferença',
                'number'        => 'Número',
                'date'          => 'Data',
                'contact'       => 'Contato',
                'amount'        => 'Valor',
                'rate'          => 'Taxa',
                'base-amount'   => 'Em :base',
                'status'        => 'Situação',
            ],
            'totals'           => [
                'total' => 'Total em :base',
            ],
            'today-note'       => 'O valor de hoje usa a última taxa registrada. Uma moeda sem taxa mostra — e fica fora desse total.',
            'exchange'         => [
                'gains'  => 'Ganhos',
                'losses' => 'Perdas',
                'net'    => 'Líquido',
            ],
            'documents-limit'  => 'Mostrando os :count documentos mais recentes. Os totais acima contam todos.',
        ],
    ],
];
