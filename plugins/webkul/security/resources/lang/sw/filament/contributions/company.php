<?php

return [
    'administrator' => [
        'title'       => 'Msimamizi',
        'description' => 'person nani administers hii kampuni na manages yake yake watumiaji.',
        'none'        => 'Hakuna msimamizi bado kwa hii kampuni.',
        'pending'     => 'mwaliko imetumwa, si accepted bado',
        'inactive'    => 'deactivated',
        'manage'      => 'Manage hii mtumiaji',

        'status' => [
            'active'   => 'Inatumika',
            'inactive' => 'Deactivated',
        ],

        'set-password' => [
            'label'       => 'Weka nenosiri',
            'heading'     => 'Weka mpya nenosiri kwa :email',
            'description' => 'Hii ni si emailed: give hii hadi wao yourself.',
        ],

        'add' => [
            'label'                => 'Ongeza msimamizi',
            'heading'              => 'Ongeza msimamizi',
            'submit'               => 'Ongeza',
            'method'               => 'Jinsi unapaswa wao pata access?',
            'invite'               => 'Send mwaliko',
            'invite-description'   => 'Wao pata barua pepe na chagua yao yake nenosiri.',
            'password'             => 'Weka nenosiri sasa',
            'password-description' => 'Kwa wakati barua pepe si arrive au client phones katika. Pia resets nenosiri ya someone tayari katika hii kampuni.',
        ],

        'invitation' => [
            'waiting'          => 'Mwaliko imetumwa, si accepted bado',
            'pending'          => 'Inasubiri',
            'expires'          => 'Inasubiri mpaka :date',
            'resend'           => 'Resend',
            'copy-link'        => 'Nakili link',
            'copy-heading'     => 'Sign-katika link kwa :email',
            'copy-description' => 'Anyone na hii link unaweza weka juu hii msimamizi akaunti. Send hii tu hadi wao, zaidi ya channel wewe trust.',
            'link'             => 'Link',
        ],

        'fields' => [
            'email'                => 'Msimamizi barua pepe',
            'email-helper'         => 'Bila nenosiri, mwaliko ni imetumwa hadi hii anwani na wao chagua yao yake nenosiri. Likizo hii blank hadi alika kampuni barua pepe.',
            'email-helper-edit'    => 'Ingiza anwani hadi alika mpya msimamizi. Ongeza nenosiri hadi unda yao akaunti straight away, au hadi weka upya nenosiri ya hii kampuni’s msimamizi. Likizo blank hadi badilisha nothing.',
            'password'             => 'Msimamizi nenosiri',
            'password-helper'      => 'Si lazima. Sets nenosiri sasa instead ya sending mwaliko, au resets hii kwa hii kampuni’s msimamizi. Hii ni kamwe emailed: give hii hadi wao yourself.',
            'must-change-password' => 'Lazima badilisha hii nenosiri saa ifuatayo sign-katika',
            'name'                 => 'Msimamizi jina',
        ],
    ],

    'currencies' => [
        'title'       => 'Sarafu',
        'description' => 'Sarafu hii kampuni mei tumia washa ankara, bili, nukuu na agizo la ununuzis, besides yake chaguo-msingi sarafu. Likizo tupu hadi ruhusu kila inatumika sarafu.',
        'fields'      => [
            'permitted' => 'Additional sarafu',
            'available' => 'Zinazopatikana kwenye nyaraka sasa: :list',
        ],
    ],

    'account' => [
        'title'       => 'Akaunti aina',
        'description' => 'Whether hii kampuni ni washa trial au commercial akaunti. Tu installation staff unaweza chagua hii, na tu wakati kampuni ni imeundwa.',

        'fields' => [
            'type'         => 'Akaunti aina',
            'commercial'   => 'Commercial',
            'test'         => 'Jaribio',
            'days'         => 'Trial siku',
            'days-helper'  => 'Jinsi nyingi siku trial lasts. Wewe unaweza ongeza zaidi siku later. trial ni si invoiced.',
        ],
    ],
];
