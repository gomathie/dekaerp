<?php

return [
    'base-currency' => [
        'title'       => 'Base currency',
        'description' => 'Change the accounting currency of a company that already has entries. Super Admin only, and recorded in the activity log.',
        'action'      => 'Change base currency',
        'warning'     => 'This company has :count posted entries. They keep their own currency and rate, but reports from now on use the new base currency. The change, your name and your reason are recorded.',
        'changed'     => 'Base currency changed',
        'fields'      => [
            'currency' => 'New base currency',
            'reason'   => 'Reason',
        ],
    ],
];
