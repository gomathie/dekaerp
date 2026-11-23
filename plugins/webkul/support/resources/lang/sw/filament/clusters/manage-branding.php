<?php

return [
    'breadcrumb' => 'Branding',
    'title'      => 'Branding',
    'group'      => 'Jumla',

    'navigation' => [
        'label' => 'Branding',
    ],

    'form' => [
        'sections' => [
            'logo' => [
                'title'       => 'Nembo & Favicon',
                'description' => 'Override logos, favicon na nembo urefu inatumika katika msimamizi na mteja panels. Likizo sehemu tupu hadi weka chaguo-msingi.',
            ],
            'colors' => [
                'title'       => 'Colors',
                'description' => 'Override mandhari colors inatumika katika msimamizi na mteja panels. Likizo rangi tupu hadi weka chaguo-msingi.',
            ],
        ],
        'fields' => [
            'light-logo'         => 'Nyepesi Nembo',
            'light-logo-helper'  => 'Shown washa nyepesi backgrounds. Replaces chaguo-msingi nembo.',
            'dark-logo'          => 'Nyeusi Nembo',
            'dark-logo-helper'   => 'Shown wakati nyeusi mode ni imewashwa.',
            'favicon'            => 'Favicon',
            'favicon-helper'     => 'Browser kichupo ikon.',
            'logo-height'        => 'Nembo Urefu',
            'logo-height-helper' => 'CSS urefu thamani, e.g. 2rem au 40px.',
            'primary-color'      => 'Primary',
            'gray-color'         => 'Gray',
            'danger-color'       => 'Danger',
            'info-color'         => 'Info',
            'success-color'      => 'Imefanikiwa',
            'warning-color'      => 'Onyo',
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Weka upya hadi chaguo-msingi',
        ],
    ],
];
