<?php

return [
    'navigation'      => 'Ripoti',
    'export-complete' => 'Uhamishaji wa ununuzi uko tayari: safu :count.',

    'analysis' => [
        'title'      => 'Uchambuzi wa ununuzi',
        'subheading' => 'Oda za ununuzi zilizothibitishwa kwa sarafu ya kampuni. Panga kwa msambazaji, mnunuzi au mwezi ili kuona jumla.',
        'export'     => 'Hamisha kwenda Excel',
        'sum'        => 'Jumla',
        'columns'    => [
            'reference' => 'Oda',
            'date'      => 'Tarehe',
            'supplier'  => 'Msambazaji',
            'buyer'     => 'Mnunuzi',
            'untaxed'   => 'Bila kodi',
            'total'     => 'Jumla',
        ],
        'groups' => [
            'supplier' => 'Msambazaji',
            'buyer'    => 'Mnunuzi',
            'month'    => 'Mwezi',
        ],
        'filters' => [
            'from'  => 'Kuanzia',
            'until' => 'Hadi',
        ],
    ],

    'by-product' => [
        'title'      => 'Ununuzi kwa bidhaa',
        'subheading' => 'Mistari ya oda za ununuzi zilizothibitishwa kwa sarafu ya kampuni. Panga kwa bidhaa au kategoria ili kuona idadi iliyoagizwa na iliyopokelewa.',
        'export'     => 'Hamisha kwenda Excel',
        'sum'        => 'Jumla',
        'columns'    => [
            'product'  => 'Bidhaa',
            'category' => 'Kategoria',
            'order'    => 'Oda ya ununuzi',
            'date'     => 'Tarehe',
            'supplier' => 'Msambazaji',
            'ordered'  => 'Imeagizwa',
            'received' => 'Imepokelewa',
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
