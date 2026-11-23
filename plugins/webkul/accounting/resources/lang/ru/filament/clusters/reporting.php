<?php

return [
    'navigation' => [
        'title' => 'Отчёты',
    ],
    'common' => [
        'from-to'                  => ':report - От :from до :to',
        'expand-all'               => 'Expand Все',
        'collapse-all'             => 'Collapse Все',
        'account'                  => 'Счёт',
        'date'                     => 'Дата',
        'communication'            => 'Сообщения',
        'partner'                  => 'Контрагент',
        'journal'                  => 'Журнал',
        'invoice-date'             => 'Дата счёта',
        'due-date'                 => 'Срок оплаты',
        'debit'                    => 'Дебет',
        'credit'                   => 'Кредит',
        'balance'                  => 'Остаток',
        'total'                    => 'Итого',
        'opening-balance'          => 'Opening Остаток',
        'initial-balance'          => 'Initial Остаток',
        'end-balance'              => 'Конец Остаток',
        'not-due'                  => 'Не due',
        'no-data'                  => 'Нет data доступно',
        'no-accounts-transactions' => 'Нет счета с transactions в этот период',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Остаток Sheet',
                'group' => 'Выписка Отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт до PDF',
            ],
            'filters' => [
                'date-range' => 'Диапазон дат',
                'journals'   => 'Журналы',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'Активы',
                        'total-label' => 'Итого Активы',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Текущий Активы',
                                'total-label' => 'Итого Текущий Активы',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Фиксированный Активы',
                                'total-label' => 'Итого Фиксированный Активы',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Non-текущий Активы',
                                'total-label' => 'Итого Non-текущий Активы',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'Обязательства',
                        'total-label' => 'Итого Обязательства',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Текущий Обязательства',
                                'total-label' => 'Итого Текущий Обязательства',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Non-текущий Обязательства',
                                'total-label' => 'Итого Non-текущий Обязательства',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'Капитал',
                        'total-label' => 'Итого Капитал',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Нераспределённая прибыль',
                                'current-year'   => 'Текущий Год Нераспределённая прибыль',
                                'previous-years' => 'Предыдущий Годы Нераспределённая прибыль',
                                'total-label'    => 'Итого Нераспределённая прибыль',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Нераспределённая прибыль',
                                'total-label' => 'Итого Нераспределённая прибыль',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'Обязательства + Капитал',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Прибыль & Убыток',
                'group' => 'Выписка Отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт до PDF',
            ],
            'filters' => [
                'date-range' => 'Диапазон дат',
                'journals'   => 'Журналы',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'Выручка',
                        'total-label'   => 'Итого Выручка',
                        'empty-message' => 'Нет выручка счета с transactions в этот период',
                    ],
                    'expenses' => [
                        'title'         => 'Расходы',
                        'total-label'   => 'Итого Расходы',
                        'empty-message' => 'Нет расход счета с transactions в этот период',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Общее Ledger',
                'group' => 'Audit Отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт до PDF',
            ],
            'filters' => [
                'date-range' => 'Диапазон дат',
                'journals'   => 'Журналы',
            ],
        ],
        'tax-summary' => [
            'navigation' => [
                'title' => 'Налог Сводка',
                'group' => 'Audit Отчёты',
            ],
            'actions'  => ['export-excel' => 'Export to Excel'],
            'filters'  => ['date-range' => 'Date Range'],
            'columns'  => ['tax' => 'Tax', 'rate' => 'Rate', 'base' => 'Taxable amount', 'amount' => 'Tax'],
            'sections' => ['sales' => 'On sales (collected)', 'purchases' => 'On purchases (paid)'],
            'totals'   => [
                'collected'       => 'Итого получено',
                'paid'            => 'Итого оплачен',
                'net'             => 'Нетто',
                'net-payable'     => 'Нетто налог до pay',
                'net-reclaimable' => 'Нетто налог до reclaim',
            ],
            'empty' => 'Нет налог в этот период.',
            'note'  => 'От проведён счета, счета поставщиков, кредит-нотаs и refunds dated в период. Кредит заметки и refunds reduce итоги. Чек с ваш accountant до filing.',
        ],
        'cash-flow' => [
            'navigation' => [
                'title' => 'Касса Flow',
                'group' => 'Выписка Отчёты',
            ],
            'actions'  => ['export-excel' => 'Export to Excel'],
            'filters'  => ['date-range' => 'Date Range'],
            'columns'  => ['account' => 'From or to', 'in' => 'Money in', 'out' => 'Money out', 'net' => 'Net'],
            'sections' => ['operating' => 'Operating activities', 'investing' => 'Investing activities', 'financing' => 'Financing activities'],
            'totals'   => [
                'opening' => 'Касса в начать период',
                'section' => 'Нетто касса',
                'net'     => 'Нетто изменить в касса',
                'closing' => 'Касса в конец период',
            ],
            'other' => 'Другое',
            'empty' => 'Нет касса movement в этот период.',
            'note'  => 'От проведён проводки вкл. банк и касса счета. Каждый entry shown менее счёт вкл. его другое side. Перемещения между ваш собственную банк и касса счета осталось вне.',
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Trial Остаток',
                'group' => 'Audit Отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт до PDF',
            ],
            'filters' => [
                'date-range' => 'Диапазон дат',
                'journals'   => 'Журналы',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Контрагент Ledger',
                'group' => 'Контрагент Отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт Excel',
                'export-pdf'   => 'Экспорт PDF',
            ],
            'filters' => [
                'date-range' => 'Диапазон дат',
                'partners'   => 'Контрагенты',
                'journals'   => 'Журналы',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Aged К получению',
                'group' => 'Контрагент Отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт Excel',
                'export-pdf'   => 'Экспорт PDF',
            ],
            'filters' => [
                'as-of'         => 'Как',
                'based-on'      => 'Based вкл.',
                'period-length' => 'Период Length (дни)',
                'journals'      => 'Журналы',
                'partners'      => 'Контрагенты',
                'entries'       => 'Проводки',
                'options'       => [
                    'due-date'       => 'Срок оплаты',
                    'invoice-date'   => 'Дата счёта',
                    'days-30'        => '30 Дни',
                    'days-60'        => '60 Дни',
                    'days-90'        => '90 Дни',
                    'posted-entries' => 'Проведён Проводки',
                    'all-entries'    => 'Все Проводки',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Aged К оплате',
                'group' => 'Контрагент Отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт Excel',
                'export-pdf'   => 'Экспорт PDF',
            ],
            'filters' => [
                'as-of'         => 'Как',
                'based-on'      => 'Based вкл.',
                'period-length' => 'Период Length (дни)',
                'journals'      => 'Журналы',
                'partners'      => 'Контрагенты',
                'entries'       => 'Проводки',
                'options'       => [
                    'due-date'       => 'Срок оплаты',
                    'invoice-date'   => 'Дата счёта',
                    'days-30'        => '30 Дни',
                    'days-60'        => '60 Дни',
                    'days-90'        => '90 Дни',
                    'posted-entries' => 'Проведён Проводки',
                    'all-entries'    => 'Все Проводки',
                ],
            ],
        ],
    ],
];
