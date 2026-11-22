<?php

return [
    'title' => 'Manage Quotation & Order',

    'breadcrumb' => 'Manage Quotation & Order',

    'navigation' => [
        'title' => 'Manage Quotation & Order',
    ],

    'form' => [
        'fields' => [
            'validity-suffix'             => 'days',
            'validity'                    => 'Default Quotation Validity',
            'validity-help'               => 'The default number of days a quotation is valid for.',
            'lock-confirm-sales'          => 'Lock Confirm Sales',
            'lock-confirm-sales-help'     => 'If enabled, the sales order will be locked after confirmation.',
            'goods-leave-on-invoice'      => 'Customers take the goods when invoiced',
            'goods-leave-on-invoice-help' => 'Default for invoices that create a sales order. On: the delivery is recorded as done when the invoice is confirmed. Off: the delivery is left for the warehouse to validate.',
        ],
    ],
];
