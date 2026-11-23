<?php

return [
    'navigation' => [
        'title' => 'Gharama Kategoria',
    ],

    'form' => [
        'fields' => [
            'requires-receipt'         => 'Stakabadhi inahitajika',
            'is-subcontracting'        => 'Subcontractor au carrier gharama',
            'is-subcontracting-helper' => 'Gharama katika hii kategoria ni imelipwa hadi msambazaji (carrier, agent) na billed kwa msambazaji.',
        ],
    ],

    'table' => [
        'columns' => [
            'requires-receipt'  => 'Stakabadhi inahitajika',
            'is-subcontracting' => 'Subcontracting',
        ],
    ],
];
