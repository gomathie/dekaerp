<?php

return [
    'validation' => [
        'upload-failed' => 'Faili ya risiti haikuweza kuhifadhiwa. Tafadhali jaribu tena.',
    ],
    'relation-manager' => [
        'title' => 'Gharama',

        'fields' => [
            'date'           => 'Tarehe',
            'category'       => 'Kategoria',
            'payee'          => 'Amelipwa',
            'amount'         => 'Kiasi',
            'approved-total' => 'Imeidhinishwa',
            'state'          => 'Hali',
            'bill'           => 'Bili ya msambazaji',
            'not-billed'     => 'Bili haijatolewa',
        ],
    ],

    'margin' => [
        'heading' => 'Mapato na gharama',
        'revenue' => 'Mapato',
        'costs'   => 'Gharama',
        'margin'  => 'Faida',
        'helper'  => 'Tozo za mteja dhidi ya gharama zilizoidhinishwa. Gharama za rasimu na zilizokataliwa hazihesabiwi.',
    ],

    'actions' => [
        'post-bill' => [
            'label'          => 'Unda bili ya msambazaji',
            'heading'        => 'Unda rasimu za bili za wasambazaji?',
            'description'    => 'Kila gharama iliyoidhinishwa ambayo haijatozwa bili kwenye usafirishaji huu inakuwa rasimu ya bili, moja kwa kila mlipwaji. Hakuna kinachorekodiwa: idara ya fedha bado huzikagua na kuzirekodi.',
            'cannot-post'    => 'Gharama hizi haziwezi kutozwa bili bado',
            'draft-reminder' => 'Zimeundwa kama rasimu. Zikague na uzirekodi katika Uhasibu.',
            'notification'   => '{0}Hakuna kilichobaki cha kutoza bili.|{1}Rasimu moja ya bili ya msambazaji imeundwa.|[2,*]Rasimu :count za bili za wasambazaji zimeundwa.',
        ],
    ],
];
