<?php

return [
    'navigation' => [
        'title' => 'Отчёты',
    ],
    'common' => [
        'from-to'                  => ':report — с :from по :to',
        'expand-all'               => 'Развернуть всё',
        'collapse-all'             => 'Свернуть всё',
        'account'                  => 'Счёт',
        'date'                     => 'Дата',
        'communication'            => 'Назначение',
        'partner'                  => 'Контакт',
        'journal'                  => 'Журнал',
        'invoice-date'             => 'Дата счёта',
        'due-date'                 => 'Срок оплаты',
        'debit'                    => 'Дебет',
        'credit'                   => 'Кредит',
        'balance'                  => 'Сальдо',
        'total'                    => 'Итого',
        'opening-balance'          => 'Входящее сальдо',
        'initial-balance'          => 'Начальное сальдо',
        'end-balance'              => 'Конечное сальдо',
        'not-due'                  => 'Срок не наступил',
        'no-data'                  => 'Нет данных',
        'no-accounts-transactions' => 'Нет счетов с операциями за этот период',
    ],
    'pages' => [
        'balance-sheet' => [
            'navigation' => [
                'title' => 'Бухгалтерский баланс',
                'group' => 'Финансовые отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт в PDF',
            ],
            'filters' => [
                'date-range' => 'Период',
                'journals'   => 'Журналы',
            ],
            'content' => [
                'sections' => [
                    'assets' => [
                        'title'       => 'АКТИВЫ',
                        'total-label' => 'Итого АКТИВЫ',
                        'subsections' => [
                            'current-assets' => [
                                'title'       => 'Оборотные активы',
                                'total-label' => 'Итого оборотные активы',
                            ],
                            'fixed-assets' => [
                                'title'       => 'Основные средства',
                                'total-label' => 'Итого основные средства',
                            ],
                            'non-current-assets' => [
                                'title'       => 'Внеоборотные активы',
                                'total-label' => 'Итого внеоборотные активы',
                            ],
                        ],
                    ],
                    'liabilities' => [
                        'title'       => 'ОБЯЗАТЕЛЬСТВА',
                        'total-label' => 'Итого ОБЯЗАТЕЛЬСТВА',
                        'subsections' => [
                            'current-liabilities' => [
                                'title'       => 'Краткосрочные обязательства',
                                'total-label' => 'Итого краткосрочные обязательства',
                            ],
                            'non-current-liabilities' => [
                                'title'       => 'Долгосрочные обязательства',
                                'total-label' => 'Итого долгосрочные обязательства',
                            ],
                        ],
                    ],
                    'equity' => [
                        'title'       => 'КАПИТАЛ',
                        'total-label' => 'Итого КАПИТАЛ',
                        'subsections' => [
                            'unallocated-earnings' => [
                                'title'          => 'Нераспределённый результат',
                                'current-year'   => 'Нераспределённый результат текущего года',
                                'previous-years' => 'Нераспределённый результат прошлых лет',
                                'total-label'    => 'Итого нераспределённый результат',
                            ],
                            'retained-earnings' => [
                                'title'       => 'Нераспределённая прибыль',
                                'total-label' => 'Итого нераспределённая прибыль',
                            ],
                        ],
                    ],
                ],
                'grand-total-label' => 'ОБЯЗАТЕЛЬСТВА + КАПИТАЛ',
            ],
        ],
        'profit-loss' => [
            'navigation' => [
                'title' => 'Прибыли и убытки',
                'group' => 'Финансовые отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт в PDF',
            ],
            'filters' => [
                'date-range' => 'Период',
                'journals'   => 'Журналы',
            ],
            'content' => [
                'sections' => [
                    'revenue' => [
                        'title'         => 'ВЫРУЧКА',
                        'total-label'   => 'Итого выручка',
                        'empty-message' => 'Нет счетов выручки с операциями за этот период',
                    ],
                    'expenses' => [
                        'title'         => 'РАСХОДЫ',
                        'total-label'   => 'Итого расходы',
                        'empty-message' => 'Нет счетов расходов с операциями за этот период',
                    ],
                ],
            ],
        ],
        'general-ledger' => [
            'navigation' => [
                'title' => 'Главная книга',
                'group' => 'Аудиторские отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт в PDF',
            ],
            'filters' => [
                'date-range' => 'Период',
                'journals'   => 'Журналы',
            ],
        ],
        'tax-summary' => [
            'navigation' => [
                'title' => 'Сводка по налогам',
                'group' => 'Аудиторские отчёты',
            ],
            'actions'  => ['export-excel' => 'Экспорт в Excel'],
            'filters'  => ['date-range' => 'Период'],
            'columns'  => ['tax' => 'Налог', 'rate' => 'Ставка', 'base' => 'Налогооблагаемая сумма', 'amount' => 'Сумма налога'],
            'sections' => ['sales' => 'С продаж (начислено)', 'purchases' => 'С закупок (уплачено)'],
            'totals'   => [
                'collected'       => 'Всего начислено',
                'paid'            => 'Всего уплачено',
                'net'             => 'Сальдо',
                'net-payable'     => 'Налог к уплате',
                'net-reclaimable' => 'Налог к возмещению',
            ],
            'empty' => 'За этот период налогов нет.',
            'note'  => 'По проведённым счетам клиентам и поставщикам, кредит-нотам и возвратам с датой в этом периоде. Кредит-ноты и возвраты уменьшают итоги. Перед подачей отчётности сверьтесь с бухгалтером.',
        ],
        'cash-flow' => [
            'navigation' => [
                'title' => 'Движение денежных средств',
                'group' => 'Финансовые отчёты',
            ],
            'actions'  => ['export-excel' => 'Экспорт в Excel'],
            'filters'  => ['date-range' => 'Период'],
            'columns'  => ['account' => 'От кого или кому', 'in' => 'Поступления', 'out' => 'Списания', 'net' => 'Сальдо'],
            'sections' => ['operating' => 'Операционная деятельность', 'investing' => 'Инвестиционная деятельность', 'financing' => 'Финансовая деятельность'],
            'totals'   => [
                'opening' => 'Денежные средства на начало периода',
                'section' => 'Чистый денежный поток',
                'net'     => 'Чистое изменение денежных средств',
                'closing' => 'Денежные средства на конец периода',
            ],
            'other' => 'Прочее',
            'empty' => 'За этот период движения денежных средств нет.',
            'note'  => 'По проведённым проводкам на банковских и кассовых счетах. Каждая проводка показана под корреспондирующим счётом. Переводы между вашими банковскими и кассовыми счетами не учитываются.',
        ],
        'trial-balance' => [
            'navigation' => [
                'title' => 'Оборотно-сальдовая ведомость',
                'group' => 'Аудиторские отчёты',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт в PDF',
            ],
            'filters' => [
                'date-range' => 'Период',
                'journals'   => 'Журналы',
            ],
        ],
        'partner-ledger' => [
            'navigation' => [
                'title' => 'Книга расчётов с контактами',
                'group' => 'Отчёты по контактам',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт в PDF',
            ],
            'filters' => [
                'date-range' => 'Период',
                'partners'   => 'Контакты',
                'journals'   => 'Журналы',
            ],
        ],
        'aged-receivable' => [
            'navigation' => [
                'title' => 'Дебиторская задолженность по срокам',
                'group' => 'Отчёты по контактам',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт в PDF',
            ],
            'filters' => [
                'as-of'         => 'На дату',
                'based-on'      => 'По',
                'period-length' => 'Длина периода (дни)',
                'journals'      => 'Журналы',
                'partners'      => 'Контакты',
                'entries'       => 'Проводки',
                'options'       => [
                    'due-date'       => 'Срок оплаты',
                    'invoice-date'   => 'Дата счёта',
                    'days-30'        => '30 дней',
                    'days-60'        => '60 дней',
                    'days-90'        => '90 дней',
                    'posted-entries' => 'Проведённые проводки',
                    'all-entries'    => 'Все проводки',
                ],
            ],
        ],
        'aged-payable' => [
            'navigation' => [
                'title' => 'Кредиторская задолженность по срокам',
                'group' => 'Отчёты по контактам',
            ],
            'actions' => [
                'export-excel' => 'Экспорт в Excel',
                'export-pdf'   => 'Экспорт в PDF',
            ],
            'filters' => [
                'as-of'         => 'На дату',
                'based-on'      => 'По',
                'period-length' => 'Длина периода (дни)',
                'journals'      => 'Журналы',
                'partners'      => 'Контакты',
                'entries'       => 'Проводки',
                'options'       => [
                    'due-date'       => 'Срок оплаты',
                    'invoice-date'   => 'Дата счёта',
                    'days-30'        => '30 дней',
                    'days-60'        => '60 дней',
                    'days-90'        => '90 дней',
                    'posted-entries' => 'Проведённые проводки',
                    'all-entries'    => 'Все проводки',
                ],
            ],
        ],
        'multi-currency' => [
            'navigation'       => [
                'title' => 'Мультивалютность',
                'group' => 'Аудиторские отчёты',
            ],
            'actions'          => [
                'export-excel' => 'Экспорт в Excel',
            ],
            'filters'          => [
                'date-range'     => 'Период',
                'side'           => 'Документы',
                'sales'          => 'Продажи (счета и кредит-ноты)',
                'purchases'      => 'Закупки (счета поставщиков и возвраты)',
                'currency'       => 'Валюта',
                'all-currencies' => 'Все валюты',
            ],
            'note'             => 'Суммы в :base пересчитаны по курсу на дату проведения каждого документа, если в столбце не указан сегодняшний курс.',
            'empty'            => 'За этот период нет проведённых счетов клиентам или поставщикам.',
            'documents-count'  => ':count документ|:count документа|:count документов',
            'company-currency' => 'Валюта компании',
            'figures'          => [
                'invoiced' => 'Выставлено',
                'received' => 'Получено',
                'open'     => 'Не оплачено',
            ],
            'average-rate'     => 'Средний курс: :rate',
            'sections'         => [
                'by-currency' => 'По валютам',
                'exchange'    => 'Проведённые курсовые разницы',
                'documents'   => 'Документы',
            ],
            'columns'          => [
                'currency'      => 'Валюта',
                'invoiced'      => 'Выставлено',
                'average-rate'  => 'Средний курс',
                'invoiced-base' => 'Выставлено в :base',
                'open'          => 'Не оплачено',
                'open-base'     => 'Не оплачено в :base (по курсу проведения)',
                'open-today'    => 'Не оплачено в :base (по курсу на сегодня)',
                'difference'    => 'Разница',
                'number'        => 'Номер',
                'date'          => 'Дата',
                'contact'       => 'Контакт',
                'amount'        => 'Сумма',
                'rate'          => 'Ставка',
                'base-amount'   => 'В :base',
                'status'        => 'Статус',
            ],
            'totals'           => [
                'total' => 'Итого в :base',
            ],
            'today-note'       => 'Сегодняшняя стоимость рассчитана по последнему сохранённому курсу. Для валюты без курса показан прочерк — и она не входит в этот итог.',
            'exchange'         => [
                'gains'  => 'Прибыль',
                'losses' => 'Убытки',
                'net'    => 'Сальдо',
            ],
            'documents-limit'  => 'Показаны последние документы (:count). Итоги выше учитывают все документы.',
        ],
    ],
];
