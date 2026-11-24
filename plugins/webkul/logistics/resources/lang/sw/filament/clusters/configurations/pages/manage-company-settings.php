<?php

return [
    'navigation' => [
        'title' => 'Mipangilio',
    ],

    'title'      => 'Mipangilio ya Lojistiki',
    'subheading' => 'Mipangilio ya :company. Lojistiki huwashwa na kuzimwa kwa kila kampuni.',
    'no-company' => 'Chagua kampuni kwanza: mipangilio ya Lojistiki huhifadhiwa kwa kila kampuni.',

    'sections' => [
        'status'                   => 'Hali',
        'delivery'                 => 'Uthibitisho wa uwasilishaji',
        'operations'               => 'Shughuli',
        'accounting'               => 'Uhasibu',
        'integrations'             => 'Miunganisho ya telematiki',
        'integrations-description' => 'Unganisha watoa huduma wa ufuatiliaji wa GPS ili kuingiza magari yako na kupata data ya magari moja kwa moja.',
    ],

    'status' => [
        'enabled'  => 'Lojistiki imewashwa kwa kampuni hii',
        'disabled' => 'Lojistiki haijawashwa kwa kampuni hii',
    ],

    'readiness' => [
        'label' => 'Utayari',
        'ok'    => 'Kila kitu ambacho Lojistiki inahitaji kipo tayari.',
    ],

    'fields' => [
        'require-pod-for-delivery'    => 'Hitaji uthibitisho wa uwasilishaji ili kuweka usafirishaji umewasilishwa',
        'require-pod-photo'           => 'Hitaji picha pamoja na uthibitisho wa uwasilishaji',
        'stop-link-ttl-hours'         => 'Muda wa kiungo cha kituo',
        'stop-link-ttl-hours-help'    => 'Muda ambao kiungo cha uthibitisho wa uwasilishaji kinatumika baada ya kutolewa. Mfupi ni salama zaidi: kiungo ni kitambulisho cha kuingia, na kinasafiri ndani ya URL.',
        'stop-link-rate-limit'        => 'Maombi ya kiungo cha kituo kwa dakika',
        'stop-link-rate-limit-help'   => 'Kwa kila kiungo, kwa dakika. Acha wazi kutumia chaguo-msingi. Ongeza tu ikiwa madereva wako wanakataliwa.',
        'capture-driver-on-pod'       => 'Rekodi dereva kwenye uthibitisho wa uwasilishaji',
        'capture-driver-on-pod-help'  => 'Huchukua dereva kutoka safari ya kituo, ili uthibitisho uonyeshe nani alikuwa mlangoni.',
        'capture-recipient-id'        => 'Omba kitambulisho cha mpokeaji',
        'capture-recipient-id-help'   => 'Huongeza sehemu ya lazima ya kitambulisho wakati uthibitisho unarekodiwa. Kusanya taarifa hii pale tu una sababu.',
        'default-service-type'        => 'Aina ya huduma chaguo-msingi',
        'capacity-check'              => 'Ukaguzi wa uwezo wa gari',
        'overdue-grace-minutes'       => 'Imechelewa baada ya (dakika baada ya muda unaotarajiwa)',
        'free-waiting-minutes'        => 'Muda wa kusubiri bila malipo kituoni (dakika)',
        'free-waiting-minutes-helper' => 'Zaidi ya hapo, tozo ya kusubiri inapendekezwa. Haiongezwi kamwe kiotomatiki.',
        'invoice-journal'             => 'Jarida la ankara za wateja',
        'bill-journal'                => 'Jarida la bili za wasambazaji',
        'default-expense-account'     => 'Akaunti chaguo-msingi ya gharama',
        'expense-approval-required'   => 'Gharama zinahitaji idhini kabla ya kutozwa bili',
        'telematics-provider'         => 'Jukwaa la telematiki',
        'telematics-provider-helper'  => 'Jukwaa la ufuatiliaji wa GPS ambalo kampuni hii inatumia. Acha wazi ikiwa haitumii.',
        'telematics-server'           => 'URL ya seva',
        'telematics-server-helper'    => 'URL ya msingi ya seva yako ya telematiki.',
        'telematics-node'             => 'Nodi',
        'telematics-node-helper'      => 'PILOT pekee. Acha 1 isipokuwa akaunti yako inatumia nodi nyingine.',
        'telematics-username'         => 'Jina la mtumiaji',
        'telematics-password'         => 'Nenosiri',
    ],

    'actions' => [
        'save' => 'Hifadhi',

        'enable' => [
            'label'                     => 'Washa Lojistiki',
            'heading'                   => 'Washa Lojistiki kwa kampuni hii?',
            'description'               => 'Menyu na rekodi za Lojistiki zitapatikana kwa watumiaji wa kampuni hii walio na ruhusa za Lojistiki.',
            'description-with-problems' => 'Lojistiki inaweza kuwashwa, lakini baadhi ya vipengele havitafanya kazi hadi haya yarekebishwe: :problems',
            'notification'              => 'Lojistiki imewashwa',
        ],

        'disable' => [
            'label'        => 'Zima Lojistiki',
            'heading'      => 'Zima Lojistiki kwa kampuni hii?',
            'description'  => 'Lojistiki imefichwa na hakuna rekodi mpya zinazoweza kuundwa kwa kampuni hii. Data iliyopo inahifadhiwa, na kuiwasha tena kunarejesha ufikiaji.',
            'notification' => 'Lojistiki imezimwa',
        ],

        'telematics-sync' => [
            'label'        => 'Sawazisha magari ya PILOT',
            'heading'      => 'Sawazisha magari kutoka PILOT?',
            'description'  => 'Magari yaliyopatikana katika PILOT yataundwa au kusasishwa katika magari ya kampuni hii. Magari yaliyopo ambayo hayamo katika PILOT hayataguswa.',
            'notification' => 'Yameundwa :created, yamesasishwa :updated, yamerukwa :skipped.',
            'error'        => 'Usawazishaji wa PILOT umeshindwa',
        ],
    ],

    'notifications' => [
        'saved' => 'Mipangilio imehifadhiwa',
    ],
];
