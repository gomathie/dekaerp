<?php

return [
    'base-currency' => [
        'title'       => 'Sarafu ya msingi',
        'description' => 'Badilisha sarafu ya uhasibu ya kampuni ambayo tayari ina maingizo. Ni kwa Msimamizi Mkuu pekee, na hurekodiwa kwenye kumbukumbu za shughuli.',
        'action'      => 'Badilisha sarafu ya msingi',
        'warning'     => 'Kampuni hii ina maingizo :count yaliyorekodiwa. Yatabaki na sarafu na kiwango chake, lakini ripoti kuanzia sasa zitatumia sarafu mpya ya msingi. Mabadiliko, jina lako na sababu yako hurekodiwa.',
        'changed'     => 'Sarafu ya msingi imebadilishwa',
        'fields'      => [
            'currency' => 'Sarafu mpya ya msingi',
            'reason'   => 'Sababu',
        ],
    ],

    'sale-currency' => [
        'title'       => 'Swali la sarafu kwenye ankara mpya',
        'description' => 'Kwa kampuni zinazouza kwa sarafu zaidi ya moja.',
        'field'       => 'Uliza kila mauzo ni kwa sarafu gani',
        'helper'      => 'Ankara mpya huuliza kwanza sarafu na, kwa sarafu ya kigeni, huthibitisha kiwango cha ubadilishaji cha siku hiyo. Huulizwa tu ikiwa kampuni ina sarafu zaidi ya moja.',
    ],
];
