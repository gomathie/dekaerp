<?php

return [
    'navigation'      => 'Reporting',
    'export-complete' => 'Your sales export is ready: :count rows.',

    'analysis' => [
        'title'      => 'Sales analysis',
        'subheading' => 'Confirmed orders and invoices made without an order, in company currency. Group by customer, salesperson or month to see totals.',
        'export'     => 'Export to Excel',
        'sum'        => 'Total',
        'columns'    => [
            'reference'   => 'Order',
            'date'        => 'Date',
            'customer'    => 'Customer',
            'salesperson' => 'Salesperson',
            'untaxed'     => 'Untaxed',
            'total'       => 'Total',
        ],
        'groups' => [
            'customer'    => 'Customer',
            'salesperson' => 'Salesperson',
            'month'       => 'Month',
        ],
        'filters' => [
            'from'  => 'From',
            'until' => 'Until',
        ],
    ],

    'by-product' => [
        'title'      => 'Sales by product',
        'subheading' => 'Lines of confirmed orders and of invoices made without an order, in company currency. Group by product or category to see quantities and totals.',
        'export'     => 'Export to Excel',
        'sum'        => 'Total',
        'columns'    => [
            'product'  => 'Product',
            'category' => 'Category',
            'order'    => 'Order or invoice',
            'date'     => 'Date',
            'customer' => 'Customer',
            'quantity' => 'Quantity',
            'untaxed'  => 'Untaxed',
            'none'     => 'No product',
        ],
        'groups' => [
            'product'  => 'Product',
            'category' => 'Category',
        ],
        'filters' => [
            'from'  => 'From',
            'until' => 'Until',
        ],
    ],
];
