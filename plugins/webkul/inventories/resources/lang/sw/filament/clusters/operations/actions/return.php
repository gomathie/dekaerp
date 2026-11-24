<?php

return [
    'label' => 'Rudisha',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Bidhaa',
                'quantity'                => 'Idadi',
                'uom'                     => 'Kipimo',
                'excess-quantity-tooltip' => 'Idadi ya kurudisha ni kubwa kuliko idadi iliyoshughulikiwa katika operesheni ya awali.',
            ],
        ],
    ],

    'notification' => [
        'no-products' => [
            'body' => 'Hakuna bidhaa za kurudisha (mistari iliyokamilika na ambayo bado haijarudishwa kikamilifu pekee ndiyo inaweza kurudishwa).',
        ],
        'no-quantities' => [
            'body' => 'Tafadhali weka angalau idadi moja isiyo sifuri.',
        ],
    ],
];
