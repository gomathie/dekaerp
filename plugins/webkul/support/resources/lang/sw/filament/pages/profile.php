<?php

return [
    'title'                   => 'Wasifu',
    'heading'                 => 'Wasifu',
    'subheading'              => 'Simamia mipangilio na mapendeleo ya akaunti yako.',
    'information_section'     => 'Taarifa za wasifu',
    'information_description' => 'Sasisha taarifa za wasifu wa akaunti yako na anwani ya barua pepe.',

    'notification' => [
        'success' => [
            'title' => 'Wasifu umesasishwa',
            'body'  => 'Wasifu wako umesasishwa kikamilifu.',
        ],

        'error' => [
            'title' => 'Imeshindwa kusasisha wasifu',
            'body'  => 'Hitilafu imetokea wakati wa kusasisha wasifu wako.',
        ],

        'validation-error' => [
            'title' => 'Hitilafu ya uthibitishaji',
        ],
    ],

    'actions' => [
        'save' => 'Hifadhi mabadiliko',
    ],

    'fields' => [
        'avatar'          => 'Picha ya wasifu',
        'name'            => 'Jina',
        'email'           => 'Barua pepe',
        'language'        => 'Lugha unayopendelea',
        'language_helper' => 'Kiolesura cha msimamizi kitaonyeshwa kwa lugha hii.',
    ],

    'password' => [
        'section'     => 'Badilisha nenosiri',
        'description' => 'Hakikisha akaunti yako inatumia nenosiri refu la nasibu ili kubaki salama.',
        'current'     => 'Nenosiri la sasa',
        'new'         => 'Nenosiri jipya',
        'confirm'     => 'Thibitisha nenosiri',
        'helper'      => 'Lazima liwe na angalau herufi 8.',

        'errors' => [
            'current-required'  => 'Nenosiri la sasa linahitajika.',
            'current-incorrect' => 'Nenosiri la sasa si sahihi. Tafadhali jaribu tena.',
            'same-as-current'   => 'Nenosiri jipya lazima liwe tofauti na nenosiri lako la sasa.',
        ],

        'current-helper' => 'Weka nenosiri lako la sasa ili kuthibitisha utambulisho wako.',

        'must-change' => [
            'title' => 'Chagua nenosiri lako mwenyewe',
            'body'  => 'Msimamizi wako amekuwekea nenosiri hili. Fungua "Badilisha nenosiri" na uchague lako mwenyewe ili kuendelea.',
        ],

        'signed-out' => 'Nenosiri lako limebadilishwa. Ingia tena kwa nenosiri lako jipya.',

        'notification' => [
            'success' => [
                'title' => 'Nenosiri limebadilishwa',
                'body'  => 'Nenosiri lako limebadilishwa kikamilifu.',
            ],

            'error' => [
                'title' => 'Imeshindwa kubadilisha nenosiri',
                'body'  => 'Hitilafu imetokea wakati wa kubadilisha nenosiri lako.',
            ],
        ],
    ],
];
