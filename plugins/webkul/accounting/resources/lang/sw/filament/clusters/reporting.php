<?php

return [
    'navigation' => [
        'title' => 'Ripoti',
    ],
    'common' => [
        'from-to'                  => ':report - Kutoka :from hadi :to',
        'expand-all'               => 'Expand Zote',
        'collapse-all'             => 'Collapse Zote',
        'account'                  => 'Akaunti',
        'date'                     => 'Tarehe',
        'communication'            => 'Mawasiliano',
        'partner'                  => 'Mshirika',
        'journal'                  => 'Jarida',
        'invoice-date'             => 'Tarehe ya ankara',
        'due-date'                 => 'Tarehe ya malipo',
        'debit'                    => 'Debiti',
        'credit'                   => 'Krediti',
        'balance'                  => 'Salio',
        'total'                    => 'Jumla',
        'opening-balance'          => 'Opening Salio',
        'initial-balance'          => 'Initial Salio',
        'end-balance'              => 'Mwisho Salio',
        'not-due'                  => 'Si due',
        'no-data'                  => 'Hakuna data inapatikana',
        'no-accounts-transactions' => 'Hakuna akaunti na transactions katika hii kipindi',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Salio Sheet',
                'group' => 'Taarifa Ripoti',
            ],
            'actions' => [
                'export-excel' => 'Hamisha kwenda Excel',
                'export-pdf'   => 'Hamisha hadi PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi cha tarehe',
                'journals'   => 'Majarida',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'Mali',
                        'total-label' => 'Jumla Mali',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Sasa Mali',
                                'total-label' => 'Jumla Sasa Mali',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Isiyobadilika Mali',
                                'total-label' => 'Jumla Isiyobadilika Mali',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Non-sasa Mali',
                                'total-label' => 'Jumla Non-sasa Mali',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'Dhima',
                        'total-label' => 'Jumla Dhima',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Sasa Dhima',
                                'total-label' => 'Jumla Sasa Dhima',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Non-sasa Dhima',
                                'total-label' => 'Jumla Non-sasa Dhima',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'Mtaji',
                        'total-label' => 'Jumla Mtaji',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Mapato yasiyogawiwa',
                                'current-year'   => 'Sasa Mwaka Mapato yasiyogawiwa',
                                'previous-years' => 'Iliyotangulia Miaka Mapato yasiyogawiwa',
                                'total-label'    => 'Jumla Mapato yasiyogawiwa',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Mapato yaliyohifadhiwa',
                                'total-label' => 'Jumla Mapato yaliyohifadhiwa',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'Dhima + Mtaji',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Faida & Hasara',
                'group' => 'Taarifa Ripoti',
            ],
            'actions' => [
                'export-excel' => 'Hamisha kwenda Excel',
                'export-pdf'   => 'Hamisha hadi PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi cha tarehe',
                'journals'   => 'Majarida',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'Mapato',
                        'total-label'   => 'Jumla Mapato',
                        'empty-message' => 'Hakuna mapato akaunti na transactions katika hii kipindi',
                    ],
                    'expenses' => [
                        'title'         => 'Gharama',
                        'total-label'   => 'Jumla Gharama',
                        'empty-message' => 'Hakuna gharama akaunti na transactions katika hii kipindi',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Jumla Ledger',
                'group' => 'Audit Ripoti',
            ],
            'actions' => [
                'export-excel' => 'Hamisha kwenda Excel',
                'export-pdf'   => 'Hamisha hadi PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi cha tarehe',
                'journals'   => 'Majarida',
            ],
        ],
        'tax-summary' => [
            'navigation' => [
                'title' => 'Kodi Muhtasari',
                'group' => 'Audit Ripoti',
            ],
            'actions'  => ['export-excel' => 'Export to Excel'],
            'filters'  => ['date-range' => 'Date Range'],
            'columns'  => ['tax' => 'Tax', 'rate' => 'Rate', 'base' => 'Taxable amount', 'amount' => 'Tax'],
            'sections' => ['sales' => 'On sales (collected)', 'purchases' => 'On purchases (paid)'],
            'totals'   => [
                'collected'       => 'Jumla yaliyokusanywa',
                'paid'            => 'Jumla imelipwa',
                'net'             => 'Jumla halisi',
                'net-payable'     => 'Jumla halisi kodi hadi pay',
                'net-reclaimable' => 'Jumla halisi kodi hadi reclaim',
            ],
            'empty' => 'Hakuna kodi katika hii kipindi.',
            'note'  => 'Kutoka imewekwa ankara, bili, noti ya mkopos na refunds dated katika kipindi. Krediti maelezo na refunds reduce jumla. Hundi na yako accountant kabla ya filing.',
        ],
        'cash-flow' => [
            'navigation' => [
                'title' => 'Taslimu Flow',
                'group' => 'Taarifa Ripoti',
            ],
            'actions'  => ['export-excel' => 'Export to Excel'],
            'filters'  => ['date-range' => 'Date Range'],
            'columns'  => ['account' => 'From or to', 'in' => 'Money in', 'out' => 'Money out', 'net' => 'Net'],
            'sections' => ['operating' => 'Operating activities', 'investing' => 'Investing activities', 'financing' => 'Financing activities'],
            'totals'   => [
                'opening' => 'Taslimu saa anza ya kipindi',
                'section' => 'Jumla halisi taslimu',
                'net'     => 'Jumla halisi badilisha katika taslimu',
                'closing' => 'Taslimu saa mwisho ya kipindi',
            ],
            'other' => 'Nyingine',
            'empty' => 'Hakuna taslimu movement katika hii kipindi.',
            'note'  => 'Kutoka imewekwa maingizo washa benki na taslimu akaunti. Kila entry ni shown chini ya akaunti washa yake nyingine side. Uhamisho kati ya yako yake benki na taslimu akaunti ni iliyobaki nje.',
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Trial Salio',
                'group' => 'Audit Ripoti',
            ],
            'actions' => [
                'export-excel' => 'Hamisha kwenda Excel',
                'export-pdf'   => 'Hamisha hadi PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi cha tarehe',
                'journals'   => 'Majarida',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Mshirika Ledger',
                'group' => 'Mshirika Ripoti',
            ],
            'actions' => [
                'export-excel' => 'Hamisha Excel',
                'export-pdf'   => 'Hamisha PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi cha tarehe',
                'partners'   => 'Washirika',
                'journals'   => 'Majarida',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Aged Ya kupokea',
                'group' => 'Mshirika Ripoti',
            ],
            'actions' => [
                'export-excel' => 'Hamisha Excel',
                'export-pdf'   => 'Hamisha PDF',
            ],
            'filters' => [
                'as-of'         => 'Kama ya',
                'based-on'      => 'Based washa',
                'period-length' => 'Kipindi Length (siku)',
                'journals'      => 'Majarida',
                'partners'      => 'Washirika',
                'entries'       => 'Maingizo',
                'options'       => [
                    'due-date'       => 'Tarehe ya malipo',
                    'invoice-date'   => 'Tarehe ya ankara',
                    'days-30'        => '30 Siku',
                    'days-60'        => '60 Siku',
                    'days-90'        => '90 Siku',
                    'posted-entries' => 'Imewekwa Maingizo',
                    'all-entries'    => 'Zote Maingizo',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Aged Ya kulipa',
                'group' => 'Mshirika Ripoti',
            ],
            'actions' => [
                'export-excel' => 'Hamisha Excel',
                'export-pdf'   => 'Hamisha PDF',
            ],
            'filters' => [
                'as-of'         => 'Kama ya',
                'based-on'      => 'Based washa',
                'period-length' => 'Kipindi Length (siku)',
                'journals'      => 'Majarida',
                'partners'      => 'Washirika',
                'entries'       => 'Maingizo',
                'options'       => [
                    'due-date'       => 'Tarehe ya malipo',
                    'invoice-date'   => 'Tarehe ya ankara',
                    'days-30'        => '30 Siku',
                    'days-60'        => '60 Siku',
                    'days-90'        => '90 Siku',
                    'posted-entries' => 'Imewekwa Maingizo',
                    'all-entries'    => 'Zote Maingizo',
                ],
            ],
        ],
    ],
];
