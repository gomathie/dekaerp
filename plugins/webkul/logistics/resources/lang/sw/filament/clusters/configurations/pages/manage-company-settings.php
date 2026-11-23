<?php

return [
    'navigation' => [
        'title' => 'Mipangilio',
    ],

    'title'      => 'Usafirishaji Mipangilio',
    'subheading' => 'Mipangilio kwa :company. Usafirishaji ni switched washa na zima kwa kampuni.',
    'no-company' => 'Chagua kampuni kwanza: Usafirishaji mipangilio ni kept kwa kampuni.',

    'sections' => [
        'status'                   => 'Hali',
        'delivery'                 => 'Proof ya usafirishaji',
        'operations'               => 'Shughuli',
        'accounting'               => 'Uhasibu',
        'integrations'             => 'Telematics integrations',
        'integrations-description' => 'Connect GPS tracking providers hadi ingiza yako fleet na pull live gari data.',
    ],

    'status' => [
        'enabled'  => 'Usafirishaji ni imewashwa kwa hii kampuni',
        'disabled' => 'Usafirishaji ni si imewashwa kwa hii kampuni',
    ],

    'readiness' => [
        'label' => 'Readiness',
        'ok'    => 'Everything Usafirishaji needs ni katika place.',
    ],

    'fields' => [
        'require-pod-for-delivery'    => 'Require proof ya usafirishaji hadi mark shehena delivered',
        'require-pod-photo'           => 'Require photo na proof ya usafirishaji',
        'stop-link-ttl-hours'         => 'Kituo link validity',
        'stop-link-ttl-hours-help'    => 'Jinsi long proof-ya-usafirishaji link stays usable baada ya hii ni issued. Shorter ni safer: link ni credential, na hii travels katika URL.',
        'stop-link-rate-limit'        => 'Kituo link requests kwa dakika',
        'stop-link-rate-limit-help'   => 'Kwa link, kwa dakika. Likizo blank kwa chaguo-msingi. Raise hii tu ikiwa yako madereva ni turned away.',
        'capture-driver-on-pod'       => 'Rekodi dereva washa proofs ya usafirishaji',
        'capture-driver-on-pod-help'  => 'Takes dereva kutoka kituo’s safari, hivyo proof shows nani ilikuwa saa door.',
        'capture-recipient-id'        => 'Ask kwa mpokeaji’s Kitambulisho',
        'capture-recipient-id-help'   => 'Adds inahitajika Kitambulisho sehemu wakati proof ni captured. Tu collect hii wapi wewe ina sababu hadi.',
        'default-service-type'        => 'Chaguo-msingi service aina',
        'capacity-check'              => 'Gari capacity hundi',
        'overdue-grace-minutes'       => 'Imechelewa baada ya (dakika zilizopita inatarajiwa time)',
        'free-waiting-minutes'        => 'Free waiting time saa kituo (dakika)',
        'free-waiting-minutes-helper' => 'Beyond hii, waiting-time charge ni suggested. Hii ni kamwe added kiotomatiki.',
        'invoice-journal'             => 'Jarida kwa Ankara ya mtejas',
        'bill-journal'                => 'Jarida kwa bili ya msambazajis',
        'default-expense-account'     => 'Chaguo-msingi gharama akaunti',
        'expense-approval-required'   => 'Gharama need approval kabla ya billing',
        'telematics-provider'         => 'Telematics platform',
        'telematics-provider-helper'  => 'GPS tracking platform hii kampuni uses. Likizo tupu ikiwa hii si tumia one.',
        'telematics-server'           => 'Server URL',
        'telematics-server-helper'    => 'base URL ya yako telematics server.',
        'telematics-node'             => 'Node',
        'telematics-node-helper'      => 'PILOT tu. Likizo saa 1 unless yako akaunti uses another node.',
        'telematics-username'         => 'Username',
        'telematics-password'         => 'Nenosiri',
    ],

    'actions' => [
        'save' => 'Hifadhi',

        'enable' => [
            'label'                     => 'Washa Usafirishaji',
            'heading'                   => 'Washa Usafirishaji kwa hii kampuni?',
            'description'               => 'Usafirishaji menus na rekodi become inapatikana hadi watumiaji ya hii kampuni nani ina Usafirishaji ruhusa.',
            'description-with-problems' => 'Usafirishaji unaweza kuwa imewashwa, lakini baadhi features ita si work mpaka hizi ni isiyobadilika: :problems',
            'notification'              => 'Usafirishaji imewashwa',
        ],

        'disable' => [
            'label'        => 'Zima Usafirishaji',
            'heading'      => 'Zima Usafirishaji kwa hii kampuni?',
            'description'  => 'Usafirishaji ni imefichwa na hakuna mpya rekodi unaweza kuwa imeundwa kwa hii kampuni. Existing data ni kept, na enabling hii tena restores access.',
            'notification' => 'Usafirishaji imezimwa',
        ],

        'telematics-sync' => [
            'label'        => 'Sync PILOT fleet',
            'heading'      => 'Sync fleet kutoka PILOT?',
            'description'  => 'Magari imepatikana katika PILOT ita kuwa imeundwa au imesasishwa katika hii company\'s fleet. Existing magari hiyo ni si katika PILOT ni si touched.',
            'notification' => ':created imeundwa, :updated imesasishwa, :skipped skipped.',
            'error'        => 'PILOT sync imeshindwa',
        ],
    ],

    'notifications' => [
        'saved' => 'Mipangilio saved',
    ],
];
