<?php

return [
    'title'      => 'Ankara inayojirudia',
    'navigation' => 'Ankara zinazojirudia',

    'sections' => [
        'details' => 'Ratiba',
        'lines'   => 'Mistari ya ankara',
        'history' => 'Historia ya ankara',
    ],

    'fields' => [
        'name'         => 'Jina la kiolezo',
        'customer'     => 'Mteja',
        'journal'      => 'Daftari la mauzo',
        'currency'     => 'Sarafu',
        'responsible'  => 'Mhusika',
        'payment-term' => 'Masharti ya malipo',
        'next-date'    => 'Tarehe ya ankara inayofuata',
        'frequency'    => 'Rudia',
        'ends-on'      => 'Tarehe ya mwisho',
        'auto-email'   => 'Thibitisha na tuma kwa barua pepe kiotomatiki',
        'notes'        => 'Maelezo',
        'product'      => 'Bidhaa au huduma',
        'description'  => 'Maelezo ya mstari',
        'quantity'     => 'Idadi',
        'price'        => 'Bei ya kipimo',
        'discount'     => 'Punguzo (%)',
        'taxes'        => 'Kodi',
        'status'       => 'Hali',
        'last-issue'   => 'Tatizo la mwisho',
        'invoice'      => 'Ankara',
        'period-start' => 'Mwanzo wa kipindi',
        'period-end'   => 'Mwisho wa kipindi',
    ],

    'frequencies' => [
        'weekly'    => 'Kila wiki',
        'monthly'   => 'Kila mwezi',
        'quarterly' => 'Kila robo mwaka',
        'yearly'    => 'Kila mwaka',
    ],

    'actions' => [
        'create'       => 'Ankara mpya inayojirudia',
        'add-line'     => 'Ongeza mstari',
        'generate-now' => 'Unda ankara inayofuata sasa',
        'pause'        => 'Simamisha kwa muda',
        'resume'       => 'Endelea',
        'stop'         => 'Simamisha',
        'history'      => 'Historia',
    ],

    'errors' => [
        'inactive-customer' => 'Mteja huyu hayupo hai. Sasisha mteja kabla ya kuunda ankara.',
        'customer-email'    => 'Ongeza barua pepe ya mteja huyu kabla ya kuwasha utumaji wa kiotomatiki.',
        'journal'           => 'Chagua daftari la mauzo linalotumika la kampuni hii.',
        'currency'          => 'Chagua sarafu inayopatikana.',
        'lines'             => 'Ongeza angalau mstari mmoja wa ankara.',
        'locked-period'     => 'Tarehe ya ankara inayofuata iko katika kipindi cha hesabu kilichofungwa.',
        'frequency'         => 'Chagua marudio yanayoruhusiwa.',
        'company-mismatch'  => 'Thamani hii lazima iwe ya kampuni ya kiolezo.',
        'responsible'       => 'Chagua mtumiaji wa kampuni hii.',
        'state'             => 'Ankara hii inayojirudia haiwezi kuhamia hali hiyo.',
        'end-date'          => 'Tarehe ya mwisho lazima iwe sawa au baada ya tarehe ya ankara inayofuata.',
        'email-failed'      => 'Ankara :number imeundwa, lakini barua pepe haikuweza kutumwa. Fungua ankara na uitume tena.',
    ],

    'notifications' => [
        'created' => [
            'title' => 'Ankara inayojirudia iko tayari kukaguliwa',
            'body'  => 'Rasimu ya ankara :number imeundwa. Ikague na uitume kwa mteja.',
            'view'  => 'Fungua ankara',
        ],
    ],
];
