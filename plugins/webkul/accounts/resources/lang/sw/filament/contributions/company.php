<?php

return [
    'base-currency' => [
        'title'       => 'Base sarafu',
        'description' => 'Badilisha uhasibu sarafu ya kampuni hiyo tayari ina maingizo. Super Msimamizi tu, na recorded katika shughuli log.',
        'action'      => 'Badilisha base sarafu',
        'warning'     => 'Hii kampuni ina :count imewekwa maingizo. Wao weka yao yake sarafu na kiwango, lakini ripoti kutoka sasa washa tumia mpya base sarafu. badilisha, yako jina na yako sababu ni recorded.',
        'changed'     => 'Base sarafu imebadilishwa',
        'fields'      => [
            'currency' => 'Mpya base sarafu',
            'reason'   => 'Sababu',
        ],
    ],

    'sale-currency' => [
        'title'       => 'Swali la sarafu kwenye ankara mpya',
        'description' => 'Kwa kampuni zinazouza kwa sarafu zaidi ya moja.',
        'field'       => 'Uliza kila mauzo yamefanyika kwa sarafu gani',
        'helper'      => 'Ankara mpya kwanza inauliza sarafu na, kwa sarafu ya kigeni, inathibitisha kiwango cha ubadilishaji cha siku. Huulizwa tu kampuni ikiwa na sarafu zaidi ya moja.',
    ],
];
