<?php

return [
    'navigation' => [
        'title' => 'Mahudhurio mipangilio',
    ],
    'title'      => 'Mahudhurio mipangilio',
    'subheading' => 'Mahudhurio kwa :company. Switch hii washa hapa, kisha wafanyakazi unaweza clock katika kutoka dashibodi.',
    'no-company' => 'Chagua kampuni hadi manage yake mahudhurio mipangilio.',
    'sections'   => [
        'status' => 'Hali',
        'rules'  => 'Rules',
    ],
    'status' => [
        'enabled'  => 'Washa kwa hii kampuni',
        'disabled' => 'Zima kwa hii kampuni',
    ],
    'fields' => [
        'auto-checkout'         => 'Kiotomatiki hundi-nje baada ya',
        'auto-checkout-help'    => 'forgotten hundi-nje ni imefungwa baada ya hii nyingi saa na flagged kwa review.',
        'capture-location'      => 'Mahali saa clock-katika',
        'capture-location-help' => 'Zima: nothing ni recorded. Si lazima: recorded wakati device shares hii. Inahitajika: clock-katika needs hii.',
        'late-tolerance'        => 'Late baada ya',
        'overtime-tolerance'    => 'Muda wa ziada baada ya',
    ],
    'units' => [
        'hours'   => 'saa',
        'minutes' => 'dakika',
    ],
    'actions' => [
        'save'   => 'Hifadhi',
        'enable' => [
            'label'        => 'Switch washa',
            'heading'      => 'Switch mahudhurio washa?',
            'description'  => 'Wafanyakazi ya hii kampuni na mfanyakazi rekodi unaweza clock katika na nje kutoka dashibodi. Managers see yao timu.',
            'notification' => 'Mahudhurio ni washa kwa hii kampuni.',
        ],
        'disable' => [
            'label'        => 'Switch zima',
            'heading'      => 'Switch mahudhurio zima?',
            'description'  => 'Clocking katika vituo na mahudhurio screens ni imefichwa. Existing rekodi ni kept.',
            'notification' => 'Mahudhurio ni zima kwa hii kampuni.',
        ],
    ],
    'notifications' => [
        'saved' => 'Mahudhurio mipangilio saved.',
    ],
];
