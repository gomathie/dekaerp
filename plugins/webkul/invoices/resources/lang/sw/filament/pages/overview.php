<?php

return [
    'navigation' => [
        'title' => 'Muhtasari',
    ],

    'title' => 'Muhtasari',

    'sections' => [
        'sales'     => 'Mauzo',
        'purchases' => 'Ununuzi',
        'invoices'  => 'Ankara na bili',
    ],

    'filters' => [
        'sections'       => 'Onyesha',
        'all-sections'   => 'Sehemu zote',
        'date-range'     => 'Kipindi',
        'product'        => 'Bidhaa',
        'all-products'   => 'Bidhaa zote',
        'category'       => 'Kategoria',
        'all-categories' => 'Kategoria zote',
    ],

    'headline' => [
        'heading'         => 'Kwa ufupi',
        'sales'           => 'Mauzo',
        'purchases'       => 'Ununuzi',
        'invoices'        => 'Ankara',
        'revenue'         => 'Mapato',
        'orders'          => '{0} Hakuna mauzo|{1} Mauzo 1|[2,*] Mauzo :count',
        'purchase-orders' => '{0} Hakuna oda|{1} Oda 1|[2,*] Oda :count',
        'open-quotations' => 'Nukuu za bei zilizo wazi',
        'spend'           => 'Matumizi ya ununuzi',
        'open-rfqs'       => 'Maombi ya nukuu yaliyo wazi',
        'to-receive'      => 'Za kupokelewa',
        'to-receive-hint' => 'Oda zilizothibitishwa ambazo hazijapokelewa kikamilifu',
        'empty-period'    => 'Hakuna kitu katika kipindi hiki. Cha karibuni ni cha tarehe :date; chagua kipindi kirefu zaidi hapo juu.',
        'to-collect'      => 'Za kukusanya',
        'to-pay'          => 'Za kulipa',
        'overdue'         => '{0} Hakuna iliyochelewa|{1} 1 imechelewa|[2,*] :count zimechelewa',
    ],

    'attention' => [
        'heading'          => 'Inahitaji uangalizi',
        'empty'            => 'Kila kitu kiko sawa. Hakuna kinachokusubiri.',
        'overdue-invoices' => '{1} Ankara 1 imechelewa|[2,*] Ankara :count zimechelewa',
        'overdue-bills'    => '{1} Bili 1 imechelewa|[2,*] Bili :count zimechelewa',
        'bills-due-soon'   => '{1} Bili 1 inadaiwa wiki hii|[2,*] Bili :count zinadaiwa wiki hii',
        'old-quotations'   => '{1} Nukuu 1 inasubiri zaidi ya siku 7|[2,*] Nukuu :count zinasubiri zaidi ya siku 7',
        'rfqs-to-approve'  => '{1} Ununuzi 1 unasubiri idhini|[2,*] Ununuzi :count unasubiri idhini',
        'draft-invoices'   => '{1} Rasimu 1 ya ankara inasubiri kuthibitishwa|[2,*] Rasimu :count za ankara zinasubiri kuthibitishwa',
        'draft-invoices-amount' => '{1} Rasimu 1 ya ankara inasubiri kuthibitishwa (:amount)|[2,*] Rasimu :count za ankara zinasubiri kuthibitishwa (:amount)',
    ],

    'actions' => [
        'new-invoice'   => 'Ankara mpya',
        'new-bill'      => 'Bili mpya',
        'new-quotation' => 'Nukuu mpya',
        'new-rfq'       => 'Ombi jipya la nukuu',
    ],

    'cash' => [
        'heading'     => 'Pesa zinazoingia na kutoka',
        'in'          => 'Pesa zinazoingia',
        'out'         => 'Pesa zinazotoka',
    ],

    'trend' => ':change ikilinganishwa na kipindi kilichopita',

    'modules' => [
        'heading' => 'Katika kampuni nzima',
        'open'    => 'Fungua',
    ],

    'board' => [
        'updated'     => 'Imesasishwa :time',
        'orders'      => 'Mauzo',
        'conversion'  => 'Nukuu → oda',
        'by-supplier' => 'Matumizi kwa msambazaji',
        'other'       => 'Nyingine',
        'paid'        => 'Imelipwa',
        'open'        => 'Wazi',
        'overdue'     => 'Imechelewa',
        'show'        => 'Onyesha',
        'tab-overdue' => 'Zilizochelewa',
        'tab-soon'    => 'Zinadaiwa wiki hii',
        'tab-open'    => 'Zote zilizo wazi',
        'nothing'     => 'Hakuna kitu hapa.',
        'customer'    => 'Mteja',
        'due'         => 'Tarehe ya mwisho',
        'no-due-date' => 'Hakuna tarehe ya mwisho',
        'due-today'   => 'Inadaiwa leo',
        'due-on'      => 'Inadaiwa :date',
        'days-late'   => '{1} Imechelewa siku 1|[2,*] Imechelewa siku :count',
    ],
];
