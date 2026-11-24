<?php

return [
    'unknown'             => 'Unknown',
    'other'               => 'Other',
    'share'               => 'Share',
    'status'              => 'Status',
    'delivery-status'     => 'Delivery Status',
    'billing'             => 'Billing',
    'sales-value'         => 'Confirmed orders and invoices made without an order',
    'stats'               => [
        'heading'            => 'Key Metrics',
        'total-quotations'   => 'Total Quotations',
        'total-sales-orders' => 'Total Sales',
        'total-revenue'      => 'Total Revenue',
        'average-revenue'    => 'Average Revenue',
        'increase'           => 'increase',
        'decrease'           => 'decrease',
    ],

    'order-activity'      => [
        'heading'     => 'Order activity',
        'description' => 'Orders confirmed in the period, by where they are now',
        'to-deliver'  => 'To deliver',
        'delivered'   => 'Delivered',
        'to-invoice'  => 'To invoice',
        'invoiced'    => 'Invoiced',
        'share'       => ':percent% of :total orders',
    ],

    'monthly-sales'       => [
        'heading'  => 'Sales over time',
        'revenue'  => 'Revenue',
        'previous' => 'Compared period',
    ],

    'revenue-by-category' => [
        'heading' => 'Revenue by Category',
        'revenue' => 'Revenue',
    ],

    'sales-persons'       => [
        'heading' => 'Sales Person Performance',
        'revenue' => 'Revenue',
    ],

    'quotation-outcome'   => [
        'heading'     => 'Quotations',
        'description' => 'What became of the quotations made in the period',
        'won'         => 'Became orders',
        'open'        => 'Still open',
        'cancelled'   => 'Cancelled',
        'count'       => 'Quotations',
    ],

    'top-products'        => [
        'heading' => 'Top Products',
        'columns' => [
            'name'     => 'Product',
            'quantity' => 'Quantity Sold',
            'revenue'  => 'Revenue',
        ],
    ],

    'top-customers'       => [
        'heading' => 'Top Customers',
        'columns' => [
            'name'    => 'Customer',
            'orders'  => 'Sales',
            'revenue' => 'Revenue',
        ],
    ],

    'top-sales-orders'    => [
        'heading' => 'Top Sales',
        'columns' => [
            'reference'      => 'Order Number',
            'customer'       => 'Customer',
            'amount'         => 'Total Amount',
            'invoice-status' => 'Invoice Status',
        ],
    ],

    'top-quotations'      => [
        'heading' => 'Top Quotations',
        'columns' => [
            'reference' => 'Reference',
            'customer'  => 'Customer',
            'amount'    => 'Amount',
        ],
    ],
];
