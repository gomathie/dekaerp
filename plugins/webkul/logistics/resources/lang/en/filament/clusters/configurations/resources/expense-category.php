<?php

return [
    'navigation' => [
        'title' => 'Expense Categories',
    ],

    'form' => [
        'fields' => [
            'requires-receipt'         => 'Receipt required',
            'is-subcontracting'        => 'Subcontractor or carrier cost',
            'is-subcontracting-helper' => 'Costs in this category are paid to a vendor (carrier, agent) and billed per vendor.',
        ],
    ],

    'table' => [
        'columns' => [
            'requires-receipt'  => 'Receipt required',
            'is-subcontracting' => 'Subcontracting',
        ],
    ],
];
