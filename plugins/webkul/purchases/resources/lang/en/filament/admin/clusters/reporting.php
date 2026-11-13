<?php

return [
    'navigation'      => 'Reporting',
    'export-complete' => 'Your purchase export is ready: :count rows.',

    'analysis' => [
        'title'      => 'Purchase analysis',
        'subheading' => 'Confirmed purchase orders in company currency. Group by supplier, buyer or month to see totals.',
        'export'     => 'Export',
        'sum'        => 'Total',
        'columns'    => [
            'reference' => 'Order',
            'date'      => 'Date',
            'supplier'  => 'Supplier',
            'buyer'     => 'Buyer',
            'untaxed'   => 'Untaxed',
            'total'     => 'Total',
        ],
        'groups' => [
            'supplier' => 'Supplier',
            'buyer'    => 'Buyer',
            'month'    => 'Month',
        ],
        'filters' => [
            'from'  => 'From',
            'until' => 'Until',
        ],
    ],

    'by-product' => [
        'title'      => 'Purchases by product',
        'subheading' => 'Lines of confirmed purchase orders in company currency. Group by product or category to see quantities ordered and received.',
        'export'     => 'Export',
        'sum'        => 'Total',
        'columns'    => [
            'product'  => 'Product',
            'category' => 'Category',
            'order'    => 'Purchase order',
            'date'     => 'Date',
            'supplier' => 'Supplier',
            'ordered'  => 'Ordered',
            'received' => 'Received',
            'untaxed'  => 'Untaxed',
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
