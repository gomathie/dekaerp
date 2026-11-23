<?php

return [
    'validation' => [
        'upload-failed' => 'stakabadhi faili could si kuwa stored. Tafadhali jaribu tena.',
    ],
    'relation-manager' => [
        'title' => 'Gharama',

        'fields' => [
            'date'           => 'Tarehe',
            'category'       => 'Kategoria',
            'payee'          => 'Imelipwa hadi',
            'amount'         => 'Kiasi',
            'approved-total' => 'Imeidhinishwa',
            'state'          => 'Hali',
            'bill'           => 'Bili ya msambazaji',
            'not-billed'     => 'Si billed',
        ],
    ],

    'margin' => [
        'heading' => 'Mapato na gharama',
        'revenue' => 'Mapato',
        'costs'   => 'Gharama',
        'margin'  => 'Pembe',
        'helper'  => 'Billable charges dhidi ya imeidhinishwa gharama. Rasimu na imekataliwa gharama ni si counted.',
    ],

    'actions' => [
        'post-bill' => [
            'label'          => 'Unda bili ya msambazaji',
            'heading'        => 'Unda rasimu bili ya msambazajis?',
            'description'    => 'Kila imeidhinishwa, unbilled gharama washa hii shehena becomes rasimu bili, one kwa payee. Nothing ni imewekwa: fedha bado reviews na posts wao.',
            'cannot-post'    => 'Hizi gharama haiwezekani kuwa billed bado',
            'draft-reminder' => 'Imeundwa kama drafts. Review na post wao katika Uhasibu.',
            'notification'   => '{0}Nothing ilikuwa iliyobaki hadi bili.|{1}One rasimu bili ya msambazaji imeundwa.|[2,*]:count rasimu bili ya msambazajis imeundwa.',
        ],
    ],
];
