<?php

return [
    'navigation' => [
        'title' => 'Mipangilio ya mahudhurio',
    ],
    'title'      => 'Mipangilio ya mahudhurio',
    'subheading' => 'Mahudhurio ya :company. Yawashe hapa, kisha wafanyakazi wataweza kuingia kazini kutoka dashibodi.',
    'no-company' => 'Chagua kampuni ili kusimamia mipangilio yake ya mahudhurio.',
    'sections'   => [
        'status' => 'Hali',
        'rules'  => 'Kanuni',
    ],
    'status' => [
        'enabled'  => 'Imewashwa kwa kampuni hii',
        'disabled' => 'Imezimwa kwa kampuni hii',
    ],
    'fields' => [
        'auto-checkout'         => 'Kutoka kiotomatiki baada ya',
        'auto-checkout-help'    => 'Kutoka kulikosahaulika hufungwa baada ya saa hizi na kuwekwa alama ya kukaguliwa.',
        'capture-location'      => 'Eneo wakati wa kuingia',
        'capture-location-help' => 'Imezimwa: hakuna kinachorekodiwa. Si lazima: hurekodiwa kifaa kikilishiriki. Lazima: kuingia kunalihitaji.',
        'late-tolerance'        => 'Kuchelewa baada ya',
        'overtime-tolerance'    => 'Muda wa ziada baada ya',
    ],
    'units' => [
        'hours'   => 'saa',
        'minutes' => 'dakika',
    ],
    'actions' => [
        'save'   => 'Hifadhi',
        'enable' => [
            'label'        => 'Washa',
            'heading'      => 'Washa mahudhurio?',
            'description'  => 'Wafanyakazi wa kampuni hii wenye rekodi ya mfanyakazi wanaweza kuingia na kutoka kazini kutoka dashibodi. Wasimamizi huona timu yao.',
            'notification' => 'Mahudhurio yamewashwa kwa kampuni hii.',
        ],
        'disable' => [
            'label'        => 'Zima',
            'heading'      => 'Zima mahudhurio?',
            'description'  => 'Kuingia kazini kunasimama na skrini za mahudhurio zinafichwa. Rekodi zilizopo zinabaki.',
            'notification' => 'Mahudhurio yamezimwa kwa kampuni hii.',
        ],
    ],
    'notifications' => [
        'saved' => 'Mipangilio ya mahudhurio imehifadhiwa.',
    ],
];
