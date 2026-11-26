<?php

return [
    'title' => 'Gharama',

    'navigation' => [
        'title' => 'Gharama',
    ],

    'form' => [
        'fields' => [
            'number'          => 'Namba',
            'date'            => 'Tarehe',
            'amount'          => 'Kiasi',
            'currency'        => 'Sarafu',
            'expense-account' => 'Akaunti ya gharama',
            'paid-from'       => 'Imelipwa kutoka',
            'vendor'          => 'Msambazaji',
            'vendor-name'     => 'Jina la msambazaji',
            'tax'             => 'Kodi',
            'tax-amount'      => 'Kiasi cha kodi',
            'reference'       => 'Kumbukumbu',
            'note'            => 'Maelezo',
            'receipt'         => 'Risiti',
            'entry'           => 'Ingizo la jarida',
            'reason'          => 'Sababu ya mabadiliko',
        ],

        'hints' => [
            'currency'        => 'Sarafu nyingine inatumia kiwango cha siku, kama kwenye ankara.',
            'expense-account' => 'Akaunti za gharama za kampuni hii pekee. Iliyotumika mwisho inapendekezwa kwanza.',
            'paid-from'       => 'Akaunti zote za benki na fedha taslimu za kampuni.',
            'vendor'          => 'Si lazima. Andika jina jipya kuongeza msambazaji.',
            'tax'             => 'Si lazima. Tupu: hakuna mstari wa kodi.',
            'no-tax'          => 'Hakuna kodi',
            'receipt'         => 'Picha au PDF, MB 10 zaidi. Kwenye simu unaweza kupiga picha.',
            'reason'          => 'Gharama hii tayari iko kwenye vitabu. Eleza kwa nini inabadilika.',
        ],

        'tax-included' => ':tax (imejumuishwa)',

        'summary' => [
            'empty' => 'Weka kiasi ili kuona kitakachorekodiwa.',
            'posts' => 'Ukihifadhi, :total kitatoka kwenye akaunti iliyolipa: :net kwa akaunti ya gharama na :tax kodi.',
        ],
    ],

    'table' => [
        'columns' => [
            'date'            => 'Tarehe',
            'expense-account' => 'Akaunti ya gharama',
            'paid-from'       => 'Imelipwa kutoka',
            'vendor'          => 'Msambazaji',
            'reference'       => 'Kumbukumbu',
            'amount'          => 'Kiasi',
            'receipt'         => 'Risiti',
        ],

        'total'  => 'Jumla',
        'export' => 'Hamisha',

        'filters' => [
            'period'          => 'Kipindi',
            'from'            => 'Kuanzia',
            'until'           => 'Hadi',
            'paid-from'       => 'Imelipwa kutoka',
            'expense-account' => 'Akaunti ya gharama',
            'vendor'          => 'Msambazaji',
            'with-receipt'    => 'Yenye risiti',
        ],
    ],

    'actions' => [
        'new'          => 'Gharama mpya',
        'save'         => 'Hifadhi',
        'save-and-new' => 'Hifadhi na mpya',

        'delete' => [
            'label'       => 'Futa',
            'description' => 'Ingizo la gharama linaghairiwa, hivyo kwenye vitabu pesa inarudi kwenye akaunti iliyolipa.',
            'done'        => 'Gharama imefutwa',
        ],
    ],

    'notifications' => [
        'created' => 'Gharama imehifadhiwa na kurekodiwa',
        'saved'   => 'Gharama imesasishwa',
        'refused' => 'Gharama haikuhifadhiwa',
        'failed'  => 'Hitilafu imetokea. Hakuna kilichobadilishwa.',
    ],

    'export' => [
        'columns' => [
            'number'          => 'Namba',
            'date'            => 'Tarehe',
            'expense-account' => 'Akaunti ya gharama',
            'paid-from'       => 'Imelipwa kutoka',
            'vendor'          => 'Msambazaji',
            'reference'       => 'Kumbukumbu',
            'currency'        => 'Sarafu',
            'amount'          => 'Kiasi',
            'tax'             => 'Kodi',
            'amount-company'  => 'Kiasi kwa sarafu ya kampuni',
            'note'            => 'Maelezo',
        ],

        'notification' => [
            'completed' => 'Uhamishaji wa gharama umekamilika na safu :count zimehamishwa.',
            'failed'    => 'Safu :count hazikuweza kuhamishwa.',
        ],
    ],
];
