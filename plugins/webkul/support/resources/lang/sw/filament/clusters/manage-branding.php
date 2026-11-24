<?php

return [
    'breadcrumb' => 'Chapa',
    'title'      => 'Chapa',
    'group'      => 'Jumla',

    'navigation' => [
        'label' => 'Chapa',
    ],

    'form' => [
        'sections' => [
            'logo' => [
                'title'       => 'Nembo na aikoni ya tovuti',
                'description' => 'Badilisha nembo, aikoni ya tovuti na urefu wa nembo vinavyotumika kwenye paneli za msimamizi na za wateja. Acha sehemu wazi ili kubaki na chaguo-msingi.',
            ],
            'colors' => [
                'title'       => 'Rangi',
                'description' => 'Badilisha rangi za mandhari zinazotumika kwenye paneli za msimamizi na za wateja. Acha rangi wazi ili kubaki na chaguo-msingi.',
            ],
        ],
        'fields' => [
            'light-logo'         => 'Nembo angavu',
            'light-logo-helper'  => 'Huonyeshwa kwenye mandharinyuma angavu. Inachukua nafasi ya nembo chaguo-msingi.',
            'dark-logo'          => 'Nembo nyeusi',
            'dark-logo-helper'   => 'Huonyeshwa hali ya giza ikiwa imewashwa.',
            'favicon'            => 'Aikoni ya tovuti',
            'favicon-helper'     => 'Aikoni ya kichupo cha kivinjari.',
            'logo-height'        => 'Urefu wa nembo',
            'logo-height-helper' => 'Thamani ya urefu ya CSS, mf. 2rem au 40px.',
            'primary-color'      => 'Msingi',
            'gray-color'         => 'Kijivu',
            'danger-color'       => 'Hatari',
            'info-color'         => 'Taarifa',
            'success-color'      => 'Mafanikio',
            'warning-color'      => 'Onyo',
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Rejesha chaguo-msingi',
        ],
    ],
];
