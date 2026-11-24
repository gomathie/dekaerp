<?php

return [
    'navigation' => [
        'title' => 'Muhtasari',
    ],

    'title' => 'Muhtasari',

    'sections' => [
        'sales'     => 'Mauzo',
        'purchases' => 'Ununuzi',
        'invoices'  => 'Ankara & bili',
    ],

    'filters' => [
        'sections'       => 'Onyesha',
        'all-sections'   => 'Zote sehemu',
        'date-range'     => 'Kipindi cha tarehe',
        'product'        => 'Bidhaa',
        'all-products'   => 'Zote bidhaa',
        'category'       => 'Kategoria',
        'all-categories' => 'Zote kategoria',
    ],

    'headline' => [
        'heading'         => 'Saa glance',
        'sales'           => 'Mauzo',
        'purchases'       => 'Ununuzi',
        'invoices'        => 'Ankara',
        'revenue'         => 'Mapato',
        'orders'          => '{0} Hakuna mauzo|{1} 1 mauzo|[2,*] :count mauzo',
        'purchase-orders' => '{0} Hakuna maagizo|{1} 1 agizo|[2,*] :count maagizo',
        'open-quotations' => 'Wazi nukuu',
        'spend'           => 'Ununuzi spend',
        'open-rfqs'       => 'Wazi RFQs',
        'to-receive'      => 'Hadi receive',
        'to-receive-hint' => 'Imethibitishwa maagizo si bado fully imepokelewa',
        'empty-period'    => 'Nothing katika hii kipindi. za hivi punde ni dated :date; chagua longer kipindi juu ya.',
        'to-collect'      => 'Hadi collect',
        'to-pay'          => 'Hadi pay',
        'overdue'         => '{0} Hakuna imechelewa|{1} 1 imechelewa|[2,*] :count imechelewa',
    ],

    'attention' => [
        'heading'          => 'Needs attention',
        'empty'            => 'Zote futa. Nothing ni waiting washa wewe.',
        'overdue-invoices' => '{1} 1 ankara imechelewa|[2,*] :count ankara imechelewa',
        'overdue-bills'    => '{1} 1 bili imechelewa|[2,*] :count bili imechelewa',
        'bills-due-soon'   => '{1} 1 bili due hii wiki|[2,*] :count bili due hii wiki',
        'old-quotations'   => '{1} 1 nukuu waiting zaidi ya 7 siku|[2,*] :count nukuu waiting zaidi ya 7 siku',
        'rfqs-to-approve'  => '{1} 1 ununuzi hadi idhinisha|[2,*] :count ununuzi hadi idhinisha',
    ],

    'actions' => [
        'new-invoice'   => 'Ankara mpya',
        'new-bill'      => 'Mpya bili',
        'new-quotation' => 'Mpya nukuu',
        'new-rfq'       => 'Mpya Ombi la bei',
    ],

    'cash' => [
        'heading'     => 'Pesa zinazoingia na nje',
        'in'          => 'Pesa zinazoingia',
        'out'         => 'Pesa zinazotoka',
    ],

    'trend' => ':change vs iliyotangulia kipindi',

    'period' => [
        'label'        => 'Kipindi',
        'this-month'   => 'Hii mwezi',
        'last-30-days' => 'Mwisho 30 siku',
        'year-to-date' => 'Mwaka hadi tarehe',
        'last-year'    => 'Mwisho mwaka',
        'custom'       => 'Maalum',
        'compared'     => ':period, compared na :previous',
        'custom-hint'  => 'Pick yoyote nyingine dates chini ya Vichujio.',
    ],

    'modules' => [
        'heading' => 'Katika kampuni',
        'open'    => 'Wazi',
    ],

    'board' => [
        'updated'     => 'Imesasishwa :time',
        'orders'      => 'Mauzo',
        'conversion'  => 'Quote → agizo',
        'by-supplier' => 'Spend na msambazaji',
        'other'       => 'Nyingine',
        'paid'        => 'Imelipwa',
        'open'        => 'Wazi',
        'overdue'     => 'Imechelewa',
        'show'        => 'Onyesha',
        'tab-overdue' => 'Imechelewa',
        'tab-soon'    => 'Due hii wiki',
        'tab-open'    => 'Zote wazi',
        'nothing'     => 'Nothing hapa.',
        'customer'    => 'Mteja',
        'due'         => 'Due',
        'no-due-date' => 'Hakuna Tarehe ya malipo',
        'due-today'   => 'Due leo',
        'due-on'      => 'Due :date',
        'days-late'   => '{1} 1 siku late|[2,*] :count siku late',
    ],
];
