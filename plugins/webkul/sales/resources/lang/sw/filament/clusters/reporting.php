<?php

return [
    'navigation'      => 'Ripoti',
    'export-complete' => 'Uhamishaji wa mauzo uko tayari: safu :count.',

    'analysis' => [
        'title'      => 'Uchambuzi wa mauzo',
        'subheading' => 'Oda zilizothibitishwa na ankara zisizo na oda, kwa sarafu ya kampuni. Panga kwa mteja, muuzaji au mwezi kuona jumla.',
        'export'     => 'Hamisha',
        'sum'        => 'Jumla',
        'columns'    => [
            'reference'   => 'Oda',
            'date'        => 'Tarehe',
            'customer'    => 'Mteja',
            'salesperson' => 'Muuzaji',
            'untaxed'     => 'Bila kodi',
            'total'       => 'Jumla',
        ],
        'groups' => [
            'customer'    => 'Mteja',
            'salesperson' => 'Muuzaji',
            'month'       => 'Mwezi',
        ],
        'filters' => [
            'from'  => 'Kuanzia',
            'until' => 'Hadi',
        ],
    ],

    'by-product' => [
        'title'      => 'Mauzo kwa bidhaa',
        'subheading' => 'Mistari ya oda zilizothibitishwa na ya ankara zisizo na oda, kwa sarafu ya kampuni. Panga kwa bidhaa au kategoria kuona idadi na jumla.',
        'export'     => 'Hamisha',
        'sum'        => 'Jumla',
        'columns'    => [
            'product'  => 'Bidhaa',
            'category' => 'Kategoria',
            'order'    => 'Oda au ankara',
            'date'     => 'Tarehe',
            'customer' => 'Mteja',
            'quantity' => 'Idadi',
            'untaxed'  => 'Bila kodi',
        ],
        'groups' => [
            'product'  => 'Bidhaa',
            'category' => 'Kategoria',
        ],
        'filters' => [
            'from'  => 'Kuanzia',
            'until' => 'Hadi',
        ],
    ],
];
