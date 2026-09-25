<?php

return [
    'relation-manager' => [
        'title' => 'Costs',

        'fields' => [
            'date'           => 'Date',
            'category'       => 'Category',
            'payee'          => 'Paid to',
            'amount'         => 'Amount',
            'approved-total' => 'Approved',
            'state'          => 'Status',
            'bill'           => 'Vendor bill',
            'not-billed'     => 'Not billed',
        ],
    ],

    'margin' => [
        'heading' => 'Revenue and cost',
        'revenue' => 'Revenue',
        'costs'   => 'Costs',
        'margin'  => 'Margin',
        'helper'  => 'Billable charges against approved costs. Draft and rejected costs are not counted.',
    ],

    'actions' => [
        'post-bill' => [
            'label'          => 'Create vendor bill',
            'heading'        => 'Create draft vendor bills?',
            'description'    => 'Every approved, unbilled cost on this shipment becomes a draft bill, one per payee. Nothing is posted: finance still reviews and posts them.',
            'cannot-post'    => 'These costs cannot be billed yet',
            'draft-reminder' => 'Created as drafts. Review and post them in Accounting.',
            'notification'   => '{0}Nothing was left to bill.|{1}One draft vendor bill created.|[2,*]:count draft vendor bills created.',
        ],
    ],
];
