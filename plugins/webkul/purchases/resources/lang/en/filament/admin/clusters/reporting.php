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
];
