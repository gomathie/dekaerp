<?php

return [
    'navigation'      => 'Ripoti',
    'export-complete' => 'Yako ununuzi hamisha ni ready: :count mistari.',

    'analysis' => [
        'title'      => 'Ununuzi analysis',
        'subheading' => 'Imethibitishwa agizo la ununuzis katika kampuni sarafu. Kundi na msambazaji, buyer au mwezi hadi see jumla.',
        'export'     => 'Hamisha',
        'sum'        => 'Jumla',
        'columns'    => [
            'reference' => 'Agizo',
            'date'      => 'Tarehe',
            'supplier'  => 'Msambazaji',
            'buyer'     => 'Buyer',
            'untaxed'   => 'Bila kodi',
            'total'     => 'Jumla',
        ],
        'groups' => [
            'supplier' => 'Msambazaji',
            'buyer'    => 'Buyer',
            'month'    => 'Mwezi',
        ],
        'filters' => [
            'from'  => 'Kutoka',
            'until' => 'Mpaka',
        ],
    ],

    'by-product' => [
        'title'      => 'Ununuzi na bidhaa',
        'subheading' => 'Mistari ya imethibitishwa agizo la ununuzis katika kampuni sarafu. Kundi na bidhaa au kategoria hadi see quantities ordered na imepokelewa.',
        'export'     => 'Hamisha',
        'sum'        => 'Jumla',
        'columns'    => [
            'product'  => 'Bidhaa',
            'category' => 'Kategoria',
            'order'    => 'Agizo la ununuzi',
            'date'     => 'Tarehe',
            'supplier' => 'Msambazaji',
            'ordered'  => 'Ordered',
            'received' => 'Imepokelewa',
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
