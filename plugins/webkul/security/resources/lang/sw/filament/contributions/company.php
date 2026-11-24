<?php

return [
    'administrator' => [
        'title'       => 'Msimamizi',
        'description' => 'Mtu anayesimamia kampuni hii na kudhibiti watumiaji wake.',
        'none'        => 'Kampuni hii bado haina msimamizi.',
        'pending'     => 'mwaliko umetumwa, bado haujakubaliwa',
        'inactive'    => 'amezimwa',
        'manage'      => 'Dhibiti mtumiaji huyu',

        'status' => [
            'active'   => 'Hai',
            'inactive' => 'Amezimwa',
        ],

        'set-password' => [
            'label'       => 'Weka nenosiri',
            'heading'     => 'Weka nenosiri jipya la :email',
            'description' => 'Halitumwi kwa barua pepe: mpe wewe mwenyewe.',
        ],

        'add' => [
            'label'                => 'Ongeza msimamizi',
            'heading'              => 'Ongeza msimamizi mpya',
            'submit'               => 'Ongeza',
            'method'               => 'Apate ufikiaji vipi?',
            'invite'               => 'Tuma mwaliko',
            'invite-description'   => 'Atapokea barua pepe na kuchagua nenosiri lake mwenyewe.',
            'password'             => 'Weka nenosiri sasa',
            'password-description' => 'Kwa wakati barua pepe haifiki au mteja anapiga simu. Pia huweka upya nenosiri la mtu ambaye tayari yuko kwenye kampuni hii.',
        ],

        'invitation' => [
            'waiting'          => 'Mwaliko umetumwa, bado haujakubaliwa',
            'pending'          => 'Inasubiri',
            'expires'          => 'Inasubiri hadi :date',
            'resend'           => 'Tuma tena',
            'copy-link'        => 'Nakili kiungo',
            'copy-heading'     => 'Kiungo cha kuingia cha :email',
            'copy-description' => 'Yeyote mwenye kiungo hiki anaweza kusanidi akaunti hii ya msimamizi. Mtumie yeye tu, kupitia njia unayoiamini.',
            'link'             => 'Kiungo',
        ],

        'fields' => [
            'email'                => 'Barua pepe ya msimamizi',
            'email-helper'         => 'Bila nenosiri, mwaliko hutumwa kwa anwani hii naye huchagua nenosiri lake mwenyewe. Iache tupu ili kualika barua pepe ya kampuni.',
            'email-helper-edit'    => 'Weka anwani ili kualika msimamizi mpya. Ongeza nenosiri ili kuunda akaunti yake mara moja, au kuweka upya nenosiri la msimamizi wa kampuni hii. Iache tupu ili usibadilishe chochote.',
            'password'             => 'Nenosiri la msimamizi',
            'password-helper'      => 'Si lazima. Huweka nenosiri sasa badala ya kutuma mwaliko, au huliweka upya kwa msimamizi wa kampuni hii. Halitumwi kamwe kwa barua pepe: mpe wewe mwenyewe.',
            'must-change-password' => 'Lazima abadilishe nenosiri hili akiingia tena',
            'name'                 => 'Jina la msimamizi',
        ],
    ],

    'currencies' => [
        'title'       => 'Sarafu',
        'description' => 'Sarafu ambazo kampuni hii inaweza kutumia kwenye ankara, bili, nukuu za bei na oda za ununuzi, mbali na sarafu yake chaguomsingi. Iache tupu ili kuruhusu kila sarafu hai.',
        'fields'      => [
            'permitted' => 'Sarafu za ziada',
            'available' => 'Zinapatikana kwenye hati sasa: :list',
        ],
    ],

    'account' => [
        'title'       => 'Aina ya akaunti',
        'description' => 'Kama kampuni hii iko kwenye akaunti ya majaribio au ya kibiashara. Ni wafanyakazi wa jukwaa pekee wanaoweza kuchagua hili, na tu wakati kampuni inaundwa.',

        'fields' => [
            'type'         => 'Aina ya akaunti',
            'commercial'   => 'Ya kibiashara',
            'test'         => 'Ya majaribio',
            'days'         => 'Siku za majaribio',
            'days-helper'  => 'Idadi ya siku za majaribio. Unaweza kuongeza siku baadaye. Kipindi cha majaribio hakitozwi ankara.',
        ],
    ],
];
