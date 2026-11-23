<?php

return [
    'title'                   => 'Wasifu',
    'heading'                 => 'Wasifu',
    'subheading'              => 'Manage yako akaunti mipangilio na preferences.',
    'information_section'     => 'Wasifu Taarifa',
    'information_description' => "Sasisha yako account's wasifu taarifa na barua pepe anwani.",

    'notification' => [
        'success' => [
            'title' => 'Wasifu Imesasishwa',
            'body'  => 'Yako wasifu ina imesasishwa successfully.',
        ],

        'error' => [
            'title' => 'Wasifu Sasisha Imeshindwa',
            'body'  => 'ilikuwa hitilafu updating yako wasifu.',
        ],

        'validation-error' => [
            'title' => 'Validation Hitilafu',
        ],
    ],

    'actions' => [
        'save' => 'Hifadhi Changes',
    ],

    'fields' => [
        'avatar'          => 'Wasifu Photo',
        'name'            => 'Jina',
        'email'           => 'Barua pepe',
        'language'        => 'Preferred Lugha',
        'language_helper' => 'msimamizi interface ita kuwa shown katika hii lugha.',
    ],

    'password' => [
        'section'     => 'Sasisha Nenosiri',
        'description' => 'Ensure yako akaunti ni kwa kutumia long, random nenosiri hadi stay secure.',
        'current'     => 'Sasa Nenosiri',
        'new'         => 'Mpya Nenosiri',
        'confirm'     => 'Thibitisha Nenosiri',
        'helper'      => 'Lazima kuwa saa kidogo 8 characters long.',

        'errors' => [
            'current-required'  => 'Sasa nenosiri ni inahitajika.',
            'current-incorrect' => 'sasa nenosiri ni incorrect. Tafadhali jaribu tena.',
            'same-as-current'   => 'mpya nenosiri lazima kuwa different kutoka yako sasa nenosiri.',
        ],

        'current-helper' => 'Ingiza yako sasa nenosiri hadi verify yako identity.',

        'must-change' => [
            'title' => 'Chagua yako yake nenosiri',
            'body'  => 'Yako msimamizi weka hii nenosiri kwa wewe. Wazi "Sasisha Nenosiri" na chagua yako yake hadi endelea.',
        ],

        'signed-out' => 'Yako nenosiri ina imesasishwa. Ingia tena na yako mpya nenosiri.',

        'notification' => [
            'success' => [
                'title' => 'Nenosiri Imesasishwa',
                'body'  => 'Yako nenosiri ina imesasishwa successfully.',
            ],

            'error' => [
                'title' => 'Nenosiri Sasisha Imeshindwa',
                'body'  => 'ilikuwa hitilafu updating yako nenosiri.',
            ],
        ],
    ],
];
