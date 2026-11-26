<?php

return [
    'title'      => 'Recurring invoice',
    'navigation' => 'Recurring invoices',

    'sections' => [
        'details' => 'Schedule',
        'lines'   => 'Invoice lines',
        'history' => 'Invoice history',
    ],

    'fields' => [
        'name'         => 'Template name',
        'customer'     => 'Customer',
        'journal'      => 'Sales journal',
        'currency'     => 'Currency',
        'responsible'  => 'Responsible',
        'payment-term' => 'Payment terms',
        'next-date'    => 'Next invoice date',
        'frequency'    => 'Repeat',
        'ends-on'      => 'End date',
        'auto-email'   => 'Confirm and email automatically',
        'notes'        => 'Notes',
        'product'      => 'Product or service',
        'description'  => 'Description',
        'quantity'     => 'Quantity',
        'price'        => 'Unit price',
        'discount'     => 'Discount (%)',
        'taxes'        => 'Taxes',
        'status'       => 'Status',
        'last-issue'   => 'Last issue',
        'invoice'      => 'Invoice',
        'period-start' => 'Period start',
        'period-end'   => 'Period end',
    ],

    'frequencies' => [
        'weekly'    => 'Weekly',
        'monthly'   => 'Monthly',
        'quarterly' => 'Quarterly',
        'yearly'    => 'Yearly',
    ],

    'actions' => [
        'create'       => 'New recurring invoice',
        'add-line'     => 'Add line',
        'generate-now' => 'Create the next invoice now',
        'pause'        => 'Pause',
        'resume'       => 'Resume',
        'stop'         => 'Stop',
        'history'      => 'History',
    ],

    'errors' => [
        'inactive-customer' => 'This customer is inactive. Update the customer before generating an invoice.',
        'customer-email'    => 'Add an email address to this customer before enabling automatic email.',
        'journal'           => 'Choose an active sales journal for this company.',
        'currency'          => 'Choose an available currency.',
        'lines'             => 'Add at least one invoice line.',
        'locked-period'     => 'The next invoice date is in a locked accounting period.',
        'frequency'         => 'Choose a supported repeat interval.',
        'company-mismatch'  => 'This value must belong to the template company.',
        'responsible'       => 'Choose a user who belongs to this company.',
        'state'             => 'This recurring invoice cannot move to that status.',
        'end-date'          => 'The end date must be on or after the next invoice date.',
        'email-failed'      => 'Invoice :number was created, but the email could not be sent. Open the invoice and send it again.',
    ],

    'notifications' => [
        'created' => [
            'title' => 'Recurring invoice is ready to review',
            'body'  => 'Draft invoice :number was created. Review it and send it to the customer.',
            'view'  => 'Open invoice',
        ],
    ],
];
