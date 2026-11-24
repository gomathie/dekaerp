<?php

return [
    'title' => 'Watumiaji na uingiaji',

    'table' => [
        'columns' => [
            'name'                 => 'Jina',
            'login'                => 'Kuingia (barua pepe)',
            'company-admin'        => 'Msimamizi wa kampuni',
            'active'               => 'Hai',
            'must-change-password' => 'Lazima abadilishe nenosiri',
        ],

        'empty-state' => [
            'heading'     => 'Bado hakuna watumiaji',
            'description' => 'Alika au unda msimamizi katika sehemu ya Msimamizi hapo juu.',
        ],

        'actions' => [
            'copy-invitation-link' => [
                'label'      => 'Nakili kiungo cha mwaliko',
                'invitation' => 'Mwaliko unaosubiri',
                'link'       => 'Kiungo cha mwaliko',
            ],
            'show-password' => [
                'label'             => 'Onyesha nenosiri',
                'modal-heading'     => 'Onyesha nenosiri la :email',
                'modal-description' => 'Weka nenosiri lako mwenyewe ili kuendelea. Utazamaji huu unarekodiwa kwenye kumbukumbu ya ukaguzi.',
                'submit'            => 'Onyesha nenosiri',
                'shown'             => 'Nenosiri la :email',

                'form' => [
                    'password' => 'Nenosiri lako',
                ],
            ],
            'set-password' => [
                'label'             => 'Weka nenosiri',
                'modal-heading'     => 'Weka nenosiri la :email',
                'modal-description' => 'Manenosiri yaliyopo hayawezi kuonyeshwa, kwa sababu yamehifadhiwa kwa usimbaji wa njia moja. Weka jipya na umpe mtumiaji wewe mwenyewe: halitumwi kwa barua pepe.',

                'form' => [
                    'password'             => 'Nenosiri jipya',
                    'must-change-password' => 'Lazima abadilishe nenosiri hili akiingia tena',
                ],
            ],
        ],
    ],
];
