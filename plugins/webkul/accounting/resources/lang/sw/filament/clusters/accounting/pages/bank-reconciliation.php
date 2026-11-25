<?php

return [
    'navigation' => [
        'title' => 'Ulinganisho wa benki',
    ],

    'import' => [
        'label'                => 'Leta taarifa',
        'description'          => 'Pakia faili la CSV kutoka benki yako. Toa nambari ya kila safu wima, ukianza na 1 upande wa kushoto. Kila akaunti ya benki inakumbuka safu wima zake kwa wakati ujao; benki ikihamisha safu wima, inapatikana tena kwa kichwa chake.',
        'journal'              => 'Akaunti ya benki',
        'file'                 => 'Faili la taarifa (CSV)',
        'name'                 => 'Jina la taarifa',
        'date-format'          => 'Muundo wa tarehe',
        'has-header'           => 'Mstari wa kwanza ni kichwa',
        'amount-help'          => 'Au acha wazi na utoe safu wima za pesa zilizoingia na zilizotoka.',
        'balance-help'         => 'Si lazima: salio linaloendelea. Thamani yake ya siku ya mwisho ndiyo salio la kufunga.',
        'closing-balance'      => 'Salio la kufunga kwenye taarifa',
        'closing-balance-help' => 'Si lazima. Bila hilo wala safu wima ya salio, salio la kufunga ni la taarifa iliyotangulia pamoja na mistari hii.',
        'done'                 => 'Mistari :count imeletwa.',

        'columns' => [
            'date'      => 'Safu wima ya tarehe',
            'label'     => 'Safu wima ya maelezo',
            'amount'    => 'Safu wima ya kiasi',
            'money_in'  => 'Safu wima ya pesa zilizoingia',
            'money_out' => 'Safu wima ya pesa zilizotoka',
            'partner'   => 'Safu wima ya mteja au msambazaji',
            'reference' => 'Safu wima ya kumbukumbu',
            'balance'   => 'Safu wima ya salio',
        ],
    ],

    'match-all' => [
        'label'       => 'Linganisha zote zilizopendekezwa (:count)',
        'heading'     => 'Linganisha mistari yote iliyopendekezwa?',
        'description' => '{0} Hakuna mstari wenye malipo moja tu yaliyopendekezwa sasa.|{1} Mstari :count una malipo moja tu yaliyopendekezwa ya kiasi hichohicho. Utalinganishwa nayo. Mistari mingine inabaki kama ilivyo.|[2,*] Mistari :count ina malipo moja tu yaliyopendekezwa ya kiasi hichohicho. Kila mmoja utalinganishwa na malipo yake. Mistari mingine inabaki kama ilivyo.',
        'submit'      => 'Linganisha',
        'done'        => '{0} Hakuna mstari uliolinganishwa.|{1} Mstari :count umelinganishwa.|[2,*] Mistari :count imelinganishwa.',
        'left'        => '{1} Mstari :count umebaki wazi: kipindi chake kimefungwa au ulibadilika wakati huo.|[2,*] Mistari :count imebaki wazi: kipindi chake kimefungwa au ilibadilika wakati huo.',
    ],

    'balances' => [
        'heading'      => 'Ukaguzi wa salio kwa kila akaunti',
        'statement'    => 'Salio la taarifa',
        'book'         => 'Salio la vitabu',
        'difference'   => 'Tofauti',
        'up-to'        => 'Taarifa hadi :date',
        'no-statement' => 'Bado hakuna taarifa iliyoletwa',
        'explain'      => 'Salio la taarifa: salio la kufunga la taarifa ya mwisho iliyoletwa. Salio la vitabu: kilichopo kwenye akaunti katika leja; malipo yanahesabiwa yakilinganishwa na mstari wa taarifa.',

        'state' => [
            'clean'      => 'Safi',
            'to-match'   => '{1} :count ya kulinganisha|[2,*] :count ya kulinganisha',
            'difference' => 'Tofauti ya kukagua',
            'none'       => 'Hakuna taarifa',
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
