<?php

return [
    'navigation' => [
        'title' => 'Ripoti',
    ],
    'common' => [
        'from-to'                  => ':report - kuanzia :from hadi :to',
        'expand-all'               => 'Panua zote',
        'collapse-all'             => 'Kunja zote',
        'account'                  => 'Akaunti',
        'date'                     => 'Tarehe',
        'communication'            => 'Mawasiliano',
        'partner'                  => 'Mhusika',
        'journal'                  => 'Jarida',
        'invoice-date'             => 'Tarehe ya ankara',
        'due-date'                 => 'Tarehe ya mwisho',
        'debit'                    => 'Debiti',
        'credit'                   => 'Krediti',
        'balance'                  => 'Salio',
        'total'                    => 'Jumla',
        'opening-balance'          => 'Salio la kufungua',
        'initial-balance'          => 'Salio la mwanzo',
        'end-balance'              => 'Salio la mwisho',
        'not-due'                  => 'Haijafikia tarehe',
        'no-data'                  => 'Hakuna data',
        'no-accounts-transactions' => 'Hakuna akaunti zenye miamala katika kipindi hiki',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Mizania',
                'group' => 'Taarifa za fedha',
            ],
            'actions' => [
                'export-excel' => 'Hamisha kwenda Excel',
                'export-pdf'   => 'Hamisha kwenda PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi',
                'journals'   => 'Majarida',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'MALI',
                        'total-label' => 'Jumla ya MALI',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Mali za muda mfupi',
                                'total-label' => 'Jumla ya mali za muda mfupi',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Mali za kudumu',
                                'total-label' => 'Jumla ya mali za kudumu',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Mali za muda mrefu',
                                'total-label' => 'Jumla ya mali za muda mrefu',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'MADENI',
                        'total-label' => 'Jumla ya MADENI',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Madeni ya muda mfupi',
                                'total-label' => 'Jumla ya madeni ya muda mfupi',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Madeni ya muda mrefu',
                                'total-label' => 'Jumla ya madeni ya muda mrefu',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'MTAJI',
                        'total-label' => 'Jumla ya MTAJI',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Mapato yasiyogawiwa',
                                'current-year'   => 'Mapato yasiyogawiwa ya mwaka huu',
                                'previous-years' => 'Mapato yasiyogawiwa ya miaka iliyopita',
                                'total-label'    => 'Jumla ya mapato yasiyogawiwa',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Mapato yaliyobakizwa',
                                'total-label' => 'Jumla ya mapato yaliyobakizwa',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'MADENI + MTAJI',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Faida na hasara',
                'group' => 'Taarifa za fedha',
            ],
            'actions' => [
                'export-excel' => 'Hamisha kwenda Excel',
                'export-pdf'   => 'Hamisha kwenda PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi',
                'journals'   => 'Majarida',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'MAPATO',
                        'total-label'   => 'Jumla ya mapato',
                        'empty-message' => 'Hakuna akaunti za mapato zenye miamala katika kipindi hiki',
                    ],
                    'expenses' => [
                        'title'         => 'GHARAMA',
                        'total-label'   => 'Jumla ya gharama',
                        'empty-message' => 'Hakuna akaunti za gharama zenye miamala katika kipindi hiki',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Leja kuu',
                'group' => 'Ripoti za ukaguzi',
            ],
            'actions' => [
                'export-excel' => 'Hamisha kwenda Excel',
                'export-pdf'   => 'Hamisha kwenda PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi',
                'journals'   => 'Majarida',
            ],
        ],
        'tax-summary' => [
            'navigation' => [
                'title' => 'Muhtasari wa kodi',
                'group' => 'Ripoti za ukaguzi',
            ],
            'actions'  => ['export-excel' => 'Hamisha kwenda Excel'],
            'filters'  => ['date-range' => 'Kipindi'],
            'columns'  => ['tax' => 'Kodi', 'rate' => 'Kiwango', 'base' => 'Kiasi kinachotozwa kodi', 'amount' => 'Kiasi cha kodi'],
            'sections' => ['sales' => 'Kwenye mauzo (iliyokusanywa)', 'purchases' => 'Kwenye ununuzi (iliyolipwa)'],
            'totals'   => [
                'collected'       => 'Jumla iliyokusanywa',
                'paid'            => 'Jumla iliyolipwa',
                'net'             => 'Halisi',
                'net-payable'     => 'Kodi halisi ya kulipa',
                'net-reclaimable' => 'Kodi halisi ya kudai',
            ],
            'empty' => 'Hakuna kodi katika kipindi hiki.',
            'note'  => 'Kutoka ankara, bili, hati za mkopo na marejesho yaliyorekodiwa yenye tarehe ndani ya kipindi. Hati za mkopo na marejesho hupunguza jumla. Wasiliana na mhasibu wako kabla ya kuwasilisha.',
        ],
        'cash-flow' => [
            'navigation' => [
                'title' => 'Mtiririko wa fedha',
                'group' => 'Taarifa za fedha',
            ],
            'actions'  => ['export-excel' => 'Hamisha kwenda Excel'],
            'filters'  => ['date-range' => 'Kipindi'],
            'columns'  => ['account' => 'Kutoka au kwa', 'in' => 'Pesa zilizoingia', 'out' => 'Pesa zilizotoka', 'net' => 'Halisi'],
            'sections' => ['operating' => 'Shughuli za uendeshaji', 'investing' => 'Shughuli za uwekezaji', 'financing' => 'Shughuli za ufadhili'],
            'totals'   => [
                'opening' => 'Fedha mwanzoni mwa kipindi',
                'section' => 'Fedha halisi',
                'net'     => 'Mabadiliko halisi ya fedha',
                'closing' => 'Fedha mwishoni mwa kipindi',
            ],
            'other' => 'Nyingine',
            'empty' => 'Hakuna mwendo wa fedha katika kipindi hiki.',
            'note'  => 'Kutoka maingizo yaliyorekodiwa kwenye akaunti za benki na fedha taslimu. Kila ingizo linaonyeshwa chini ya akaunti iliyo upande wake mwingine. Uhamisho kati ya akaunti zako za benki na fedha taslimu hauhesabiwi.',
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Mizania ya majaribio',
                'group' => 'Ripoti za ukaguzi',
            ],
            'actions' => [
                'export-excel' => 'Hamisha kwenda Excel',
                'export-pdf'   => 'Hamisha kwenda PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi',
                'journals'   => 'Majarida',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Leja ya wahusika',
                'group' => 'Ripoti za wahusika',
            ],
            'actions' => [
                'export-excel' => 'Hamisha Excel',
                'export-pdf'   => 'Hamisha PDF',
            ],
            'filters' => [
                'date-range' => 'Kipindi',
                'partners'   => 'Wahusika',
                'journals'   => 'Majarida',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Madai kwa umri',
                'group' => 'Ripoti za wahusika',
            ],
            'actions' => [
                'export-excel' => 'Hamisha Excel',
                'export-pdf'   => 'Hamisha PDF',
            ],
            'filters' => [
                'as-of'         => 'Kufikia',
                'based-on'      => 'Kulingana na',
                'period-length' => 'Urefu wa kipindi (siku)',
                'journals'      => 'Majarida',
                'partners'      => 'Wahusika',
                'entries'       => 'Maingizo',
                'options'       => [
                    'due-date'       => 'Tarehe ya mwisho',
                    'invoice-date'   => 'Tarehe ya ankara',
                    'days-30'        => 'Siku 30',
                    'days-60'        => 'Siku 60',
                    'days-90'        => 'Siku 90',
                    'posted-entries' => 'Maingizo yaliyorekodiwa',
                    'all-entries'    => 'Maingizo yote',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Madeni kwa umri',
                'group' => 'Ripoti za wahusika',
            ],
            'actions' => [
                'export-excel' => 'Hamisha Excel',
                'export-pdf'   => 'Hamisha PDF',
            ],
            'filters' => [
                'as-of'         => 'Kufikia',
                'based-on'      => 'Kulingana na',
                'period-length' => 'Urefu wa kipindi (siku)',
                'journals'      => 'Majarida',
                'partners'      => 'Wahusika',
                'entries'       => 'Maingizo',
                'options'       => [
                    'due-date'       => 'Tarehe ya mwisho',
                    'invoice-date'   => 'Tarehe ya ankara',
                    'days-30'        => 'Siku 30',
                    'days-60'        => 'Siku 60',
                    'days-90'        => 'Siku 90',
                    'posted-entries' => 'Maingizo yaliyorekodiwa',
                    'all-entries'    => 'Maingizo yote',
                ],
            ],
        ],
        'multi-currency' => [
            'navigation'       => [
                'title' => 'Sarafu nyingi',
                'group' => 'Ripoti za ukaguzi',
            ],
            'actions'          => [
                'export-excel' => 'Hamisha kwenda Excel',
            ],
            'filters'          => [
                'date-range'     => 'Kipindi',
                'side'           => 'Hati',
                'sales'          => 'Mauzo (ankara na hati za mkopo)',
                'purchases'      => 'Ununuzi (bili na marejesho)',
                'currency'       => 'Sarafu',
                'all-currencies' => 'Sarafu zote',
            ],
            'note'             => 'Kiasi kwa :base kinatumia kiwango cha ubadilishaji cha wakati kila hati ilirekodiwa, isipokuwa safu wima inaposema kiwango cha leo.',
            'empty'            => 'Hakuna ankara wala bili zilizorekodiwa katika kipindi hiki.',
            'documents-count'  => '{1} Hati 1|[2,*] Hati :count',
            'company-currency' => 'Sarafu ya kampuni',
            'figures'          => [
                'invoiced' => 'Imetozwa',
                'received' => 'Imepokelewa',
                'open'     => 'Wazi',
            ],
            'average-rate'     => 'Kiwango cha wastani: :rate',
            'sections'         => [
                'by-currency' => 'Kwa sarafu',
                'exchange'    => 'Faida na hasara za ubadilishaji zilizorekodiwa',
                'documents'   => 'Hati',
            ],
            'columns'          => [
                'currency'      => 'Sarafu',
                'invoiced'      => 'Imetozwa',
                'average-rate'  => 'Kiwango cha wastani',
                'invoiced-base' => 'Imetozwa kwa :base',
                'open'          => 'Wazi',
                'open-base'     => 'Wazi kwa :base (iliyorekodiwa)',
                'open-today'    => 'Wazi kwa :base (leo)',
                'difference'    => 'Tofauti',
                'number'        => 'Nambari',
                'date'          => 'Tarehe',
                'contact'       => 'Mhusika',
                'amount'        => 'Kiasi',
                'rate'          => 'Kiwango',
                'base-amount'   => 'Kwa :base',
                'status'        => 'Hali',
            ],
            'totals'           => [
                'total' => 'Jumla kwa :base',
            ],
            'today-note'       => 'Thamani ya leo inatumia kiwango cha karibuni kilichohifadhiwa. Sarafu isiyo na kiwango inaonyesha — na haijumuishwi kwenye jumla hiyo.',
            'exchange'         => [
                'gains'  => 'Faida',
                'losses' => 'Hasara',
                'net'    => 'Halisi',
            ],
            'documents-limit'  => 'Zinaonyeshwa hati :count za karibuni. Jumla zilizo juu zinahesabu hati zote.',
        ],
    ],
];
