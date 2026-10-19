<?php

return [
    'fields' => [
        'name'           => 'Name',
        'code'           => 'Code',
        'is-active'      => 'Active',
        'company'        => 'Company',
        'all-companies'  => 'All companies (shared)',
        'company-helper' => 'Leave empty to share this with every company. Only users who see all companies can create or change shared entries.',
    ],

    'columns' => [
        'name'          => 'Name',
        'code'          => 'Code',
        'is-active'     => 'Active',
        'company'       => 'Company',
        'all-companies' => 'All companies',
    ],

    'filters' => [
        'is-active' => 'Active',
        'company'   => 'Company',
    ],
];
