<?php

return [
    'navigation'      => 'Ripoti',
    'export-complete' => 'Yako mauzo hamisha ni ready: :count mistari.',

    'analysis' => [
        'title'      => 'Mauzo analysis',
        'subheading' => 'Imethibitishwa maagizo na ankara imefanywa bila agizo, katika kampuni sarafu. Kundi na mteja, salesperson au mwezi hadi see jumla.',
        'export'     => 'Hamisha',
        'sum'        => 'Jumla',
        'columns'    => [
            'reference'   => 'Agizo',
            'date'        => 'Tarehe',
            'customer'    => 'Mteja',
            'salesperson' => 'Salesperson',
            'untaxed'     => 'Bila kodi',
            'total'       => 'Jumla',
        ],
        'groups' => [
            'customer'    => 'Mteja',
            'salesperson' => 'Salesperson',
            'month'       => 'Mwezi',
        ],
        'filters' => [
            'from'  => 'Kutoka',
            'until' => 'Mpaka',
        ],
    ],

    'by-product' => [
        'title'      => 'Mauzo na bidhaa',
        'subheading' => 'Mistari ya imethibitishwa maagizo na ya ankara imefanywa bila agizo, katika kampuni sarafu. Kundi na bidhaa au kategoria hadi see quantities na jumla.',
        'export'     => 'Hamisha',
        'sum'        => 'Jumla',
        'columns'    => [
            'product'  => 'Bidhaa',
            'category' => 'Kategoria',
            'order'    => 'Agizo au ankara',
            'date'     => 'Tarehe',
            'customer' => 'Mteja',
            'quantity' => 'Kiasi',
            'untaxed'  => 'Bila kodi',
        ],
        'groups' => [
            'product'  => 'Bidhaa',
            'category' => 'Kategoria',
        ],
        'filters' => [
            'from'  => 'Kutoka',
            'until' => 'Mpaka',
        ],
    ],
];
