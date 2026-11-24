<?php

return [
    'navigation' => [
        'title' => 'Ulinganisho wa benki',
    ],

    'import' => [
        'label'       => 'Leta taarifa',
        'description' => 'Pakia faili la CSV kutoka benki yako. Toa nambari ya kila safu wima, ukianza na 1 upande wa kushoto.',
        'journal'     => 'Akaunti ya benki',
        'file'        => 'Faili la taarifa (CSV)',
        'name'        => 'Jina la taarifa',
        'date-format' => 'Muundo wa tarehe',
        'has-header'  => 'Mstari wa kwanza ni kichwa',
        'amount-help' => 'Au acha wazi na utoe safu wima za pesa zilizoingia na zilizotoka.',
        'done'        => 'Mistari :count imeletwa.',

        'columns' => [
            'date'      => 'Safu wima ya tarehe',
            'label'     => 'Safu wima ya maelezo',
            'amount'    => 'Safu wima ya kiasi',
            'money_in'  => 'Safu wima ya pesa zilizoingia',
            'money_out' => 'Safu wima ya pesa zilizotoka',
            'partner'   => 'Safu wima ya mteja au msambazaji',
            'reference' => 'Safu wima ya kumbukumbu',
        ],
    ],

    'columns' => [
        'date'    => 'Tarehe',
        'label'   => 'Maelezo',
        'partner' => 'Mteja au msambazaji',
        'journal' => 'Akaunti ya benki',
        'amount'  => 'Kiasi',
        'status'  => 'Hali',
        'payment' => 'Malipo',
    ],

    'status' => [
        'reconciled'   => 'Imelinganishwa',
        'to-reconcile' => 'Ya kulinganisha',
    ],

    'match' => [
        'label'       => 'Linganisha malipo',
        'description' => 'Malipo kwenye akaunti hii ya benki ya kiasi hichohicho ambayo bado yanasubiri benki.',
        'payment'     => 'Malipo',
        'none'        => 'Hakuna malipo yanayolingana. Kwanza sajili malipo kwenye ankara, au rekodi mstari kwenye akaunti.',
        'done'        => 'Mstari umelinganishwa. Malipo sasa yamelipwa.',
    ],

    'book' => [
        'label'       => 'Rekodi kwenye akaunti',
        'description' => 'Kwa ada za benki, riba au uhamisho: hurekodi mstari dhidi ya akaunti utakayochagua.',
        'account'     => 'Akaunti',
        'entry-label' => 'Lebo',
        'done'        => 'Mstari umerekodiwa.',
    ],

    'empty' => [
        'heading'     => 'Hakuna cha kulinganisha',
        'description' => 'Leta taarifa kutoka benki yako ili kuanza.',
    ],
];
