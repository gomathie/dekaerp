<?php

return [
    'navigation'      => 'Reporting',
    'export-complete' => 'Your sales export is ready: :count rows.',

    'analysis' => [
        'title'      => 'Sales analysis',
        'subheading' => 'Confirmed orders in company currency. Group by customer, salesperson or month to see totals.',
        'export'     => 'Export',
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
];
