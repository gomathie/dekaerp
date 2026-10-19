<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'Waiting time at :stop (:minutes min over the free allowance)',
    ],

    'charges' => [
        'title'  => 'Charges',
        'fields' => [
            'product'            => 'Service',
            'description'        => 'Description',
            'quantity'           => 'Quantity',
            'price-unit'         => 'Unit price',
            'discount'           => 'Discount (%)',
            'taxes'              => 'Taxes',
            'is-billable'        => 'Billable',
            'is-billable-helper' => 'Only billable charges are picked up when an invoice is raised.',
            'invoiced'           => 'Invoiced',
        ],
    ],

    'invoices' => [
        'title'   => 'Invoices',
        'columns' => [
            'number'        => 'Invoice',
            'state'         => 'Status',
            'payment-state' => 'Payment',
            'date'          => 'Date',
            'total'         => 'Total',
        ],
        'actions' => [
            'open' => 'Open in Accounting',
        ],
    ],

    'unbilled' => [
        'title'   => 'Unbilled charges',
        'columns' => [
            'shipment'    => 'Shipment',
            'customer'    => 'Customer',
            'description' => 'Charge',
            'quantity'    => 'Qty',
            'subtotal'    => 'Amount',
            'total'       => 'Total',
        ],
        'filters' => [
            'currency' => 'Currency',
            'shipment' => 'Shipment',
        ],
    ],

    'actions' => [
        'create-invoice' => [
            'label'              => 'Create invoice',
            'heading'            => 'Invoice this shipment?',
            'description'        => 'Every billable charge that has not been invoiced yet becomes a line on a new customer invoice. Charges already invoiced are left alone.',
            'notification'       => 'Invoice created.',
            'notification-body'  => 'Invoice :invoice is in Accounting, as a draft you can review before posting.',
            'nothing-to-invoice' => 'Nothing to invoice',
        ],
    ],
];
