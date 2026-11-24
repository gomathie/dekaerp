<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => 'Baadhi ya oda za kazi tayari zimekamilika, kwa hivyo huwezi kuondoa oda hii ya uzalishaji kwenye mpango. Itakuwa hasara kupoteza maendeleo yote hayo, sivyo?',
            'work-orders-already-started' => 'Baadhi ya oda za kazi tayari zimeanza, kwa hivyo huwezi kuondoa oda hii ya uzalishaji kwenye mpango. Itakuwa hasara kupoteza maendeleo yote hayo, sivyo?',
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'Ufuatiliaji wa muda: :name',
        'no-performance-productivity-loss' => 'Unahitaji kuweka angalau hasara moja ya tija isiyo kwenye kumbukumbu katika kategoria \'Utendaji\'. Iunde kwenye mipangilio.',
    ],

    'work-center' => [
        'already-unblocked' => 'Tayari imeondolewa kizuizi.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'Tafadhali ondoa kizuizi kwenye kituo cha kazi ili kuanza oda ya kazi.',
        'already-done-or-cancelled'  => 'Huwezi kuanza oda ya kazi ambayo tayari imekamilika au imeghairiwa',
        'no-calendar-on-work-center' => 'Hakuna kalenda iliyowekwa kwenye kituo cha kazi :name.',
        'no-productivity-loss'       => 'Unahitaji kuweka angalau hasara moja ya tija katika kategoria \'Tija\'. Iunde kwenye mipangilio.',
        'no-performance-loss'        => 'Unahitaji kuweka angalau hasara moja ya tija katika kategoria \'Utendaji\'. Iunde kwenye mipangilio.',
        'impossible-to-plan'         => 'Haiwezekani kupanga oda ya kazi. Tafadhali angalia upatikanaji wa kituo cha kazi.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'Huwezi kuwa na :product kama bidhaa iliyokamilika na pia katika bidhaa za ziada',
        'missing-lot-serial-number'                => 'Unahitaji kuweka namba ya kundi/mfululizo kwa bidhaa na "kuzitumia": :missing_products',
        'serial-number-already-produced'           => 'Namba hii ya mfululizo kwa bidhaa :product tayari imezalishwa',
        'byproduct-serial-number-already-produced' => 'Namba ya mfululizo :number iliyotumika kwa bidhaa ya ziada :product tayari imezalishwa',
        'component-serial-number-consumed'         => 'Namba ya mfululizo :number iliyotumika kwa kipengele :component tayari imetumika',
        'components-availability'                  => [
            'available'     => 'Inapatikana',
            'not-available' => 'Haipatikani',
            'expected'      => 'Inatarajiwa :date',
        ],
    ],
];
