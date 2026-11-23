<?php

return [
    'breadcrumb' => 'Manage Watumiaji',
    'title'      => 'Manage Watumiaji',
    'group'      => 'Jumla',

    'navigation' => [
        'label' => 'Manage Watumiaji',
    ],

    'form' => [
        'enable-user-invitation' => [
            'label'       => 'Washa Mtumiaji Mwaliko',
            'helper-text' => 'Ruhusu watumiaji hadi alika nyingine watumiaji hadi application.',
        ],

        'enable-reset-password' => [
            'label'       => 'Washa Weka upya nenosiri',
            'helper-text' => 'Ruhusu watumiaji hadi weka upya yao nenosiri.',
        ],

        'default-role' => [
            'label'       => 'Chaguo-msingi Jukumu',
            'helper-text' => 'chaguo-msingi jukumu amekabidhiwa hadi mpya watumiaji.',
        ],
        'company-admin-business-role' => [
            'label'       => 'Kampuni Msimamizi Business Jukumu',
            'helper-text' => 'Given hadi kila mpya kampuni msimamizi alongside Kampuni Msimamizi, hivyo wao unaweza run yao kampuni. Platform majukumu ni si offered.',
        ],

        'default-company' => [
            'label'       => 'Chaguo-msingi Kampuni',
            'helper-text' => 'chaguo-msingi kampuni amekabidhiwa hadi mpya watumiaji.',
        ],
    ],
];
