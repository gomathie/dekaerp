<?php

return [
    'navigation' => [
        'title' => 'Overview',
    ],

    'title' => 'Overview',

    'sections' => [
        'sales'     => 'Sales',
        'purchases' => 'Purchases',
        'invoices'  => 'Invoices & bills',
    ],

    'filters' => [
        'sections'       => 'Show',
        'all-sections'   => 'All sections',
        'date-range'     => 'Date range',
        'product'        => 'Product',
        'all-products'   => 'All products',
        'category'       => 'Category',
        'all-categories' => 'All categories',
    ],

    'headline' => [
        'heading'         => 'At a glance',
        'sales'           => 'Sales',
        'purchases'       => 'Purchases',
        'invoices'        => 'Invoices',
        'revenue'         => 'Revenue',
        'orders'          => '{0} No orders|{1} 1 order|[2,*] :count orders',
        'open-quotations' => 'Open quotations',
        'spend'           => 'Purchase spend',
        'open-rfqs'       => 'Open RFQs',
        'to-collect'      => 'To collect',
        'to-pay'          => 'To pay',
        'overdue'         => '{0} None overdue|{1} 1 overdue|[2,*] :count overdue',
    ],

    'attention' => [
        'heading'          => 'Needs attention',
        'empty'            => 'All clear. Nothing is waiting on you.',
        'overdue-invoices' => '{1} 1 invoice overdue|[2,*] :count invoices overdue',
        'overdue-bills'    => '{1} 1 bill overdue|[2,*] :count bills overdue',
        'bills-due-soon'   => '{1} 1 bill due this week|[2,*] :count bills due this week',
        'old-quotations'   => '{1} 1 quotation waiting over 7 days|[2,*] :count quotations waiting over 7 days',
        'rfqs-to-approve'  => '{1} 1 purchase to approve|[2,*] :count purchases to approve',
    ],

    'actions' => [
        'new-invoice'   => 'New invoice',
        'new-bill'      => 'New bill',
        'new-quotation' => 'New quotation',
        'new-rfq'       => 'New RFQ',
    ],

    'cash' => [
        'heading'     => 'Money in and out',
        'description' => 'Paid invoices and paid bills per day',
        'in'          => 'Money in',
        'out'         => 'Money out',
    ],

    'trend' => ':change vs previous period',

    'modules' => [
        'heading' => 'Across the company',
        'open'    => 'Open',
    ],

    'board' => [
        'updated'     => 'Updated :time',
        'orders'      => 'Orders',
        'conversion'  => 'Quote → order',
        'by-supplier' => 'Spend by supplier',
        'other'       => 'Other',
        'paid'        => 'Paid',
        'open'        => 'Open',
        'overdue'     => 'Overdue',
        'show'        => 'Show',
        'tab-overdue' => 'Overdue',
        'tab-soon'    => 'Due this week',
        'tab-open'    => 'All open',
        'nothing'     => 'Nothing here.',
        'customer'    => 'Customer',
        'due'         => 'Due',
        'no-due-date' => 'No due date',
        'due-today'   => 'Due today',
        'due-on'      => 'Due :date',
        'days-late'   => '{1} 1 day late|[2,*] :count days late',
    ],

    'cash-compare' => [
        'previous'    => 'Previous period',
        'this-period' => 'This period',
        'vs-previous' => 'Vs previous period',
    ],
];
