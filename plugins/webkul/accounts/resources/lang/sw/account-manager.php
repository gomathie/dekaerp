<?php

return [
    'post-action-validate' => [
        'customer-required'    => 'Weka mteja halali ili kuthibitisha ankara ya mteja.',
        'vendor-required'      => 'Weka msambazaji halali ili kuthibitisha bili ya msambazaji.',
        'bank-archived'        => 'Akaunti ya benki ya mhusika iliyoambatishwa kwenye ankara hii imewekwa kumbukumbu.',
        'negative-amount'      => 'Ankara haiwezi kuthibitishwa ikiwa na jumla hasi.',
        'date-required'        => 'Weka tarehe halali ya bili au marejesho ili kuithibitisha.',
        'currency-archived'    => 'Huwezi kuthibitisha ankara yenye sarafu iliyowekwa kumbukumbu.',
        'account-deprecated'   => 'Mstari mmoja au zaidi wa ankara hii unatumia akaunti zilizositishwa.',
        'lines-required'       => 'Ongeza angalau mstari mmoja kwenye ankara.',
        'draft-state-required' => 'Ankara zilizo katika hali ya rasimu pekee ndizo zinaweza kuthibitishwa.',
        'journal-archived'     => 'Huwezi kuthibitisha ankara yenye jarida lililowekwa kumbukumbu.',
        'unbalanced'           => 'Ingizo hili la jarida halijasawazishwa na haliwezi kurekodiwa. Jumla ya debiti lazima ilingane na jumla ya krediti.',
    ],

    'change-reason' => [
        'required' => 'Toa sababu ya kubadilisha hati iliyothibitishwa.',
        'field'    => 'Sababu ya mabadiliko',
        'helper'   => 'Huhifadhiwa kwenye historia ya hati, kwa mfano "Mteja aliomba punguzo la 10%".',
        'reset'    => 'Imerudishwa kuwa rasimu. Sababu: :reason',
        'cancelled' => 'Imeghairiwa. Sababu: :reason',
        'api'      => 'Imebadilishwa kupitia API',
    ],

    'lock-date' => [
        'locked'        => 'Maingizo ya tarehe :date au kabla yake yamefungwa. Mwombe msimamizi wako asogeze tarehe ya kufunga kwanza.',
        'not-allowed'   => 'Msimamizi wa kampuni au mhasibu mwenye ruhusa ya kufungua pekee ndiye anaweza kubadilisha tarehe ya kufunga.',
        'future'        => 'Tarehe ya kufunga haiwezi kuwa ya baadaye.',
        'action'        => 'Tarehe ya kufunga',
        'field'         => 'Funga maingizo hadi',
        'helper'        => 'Maingizo yaliyorekodiwa ya tarehe hii au kabla yake hayawezi kubadilishwa. Acha wazi ili kufungua vipindi vyote.',
        'current'       => 'Kwa sasa imefungwa hadi :date.',
        'none'          => 'Hakuna kipindi kilichofungwa.',
        'saved'         => 'Tarehe ya kufunga imehifadhiwa',
    ],

    'documents' => [
        'titles' => [
            'invoice'     => 'Ankara Na. :name',
            'bill'        => 'Bili ya msambazaji Na. :name',
            'refund'      => 'Marejesho Na. :name',
            'credit-note' => 'Hati ya mkopo Na. :name',
        ],

        'labels' => [
            'invoice-date'          => 'Tarehe ya ankara',
            'bill-date'             => 'Tarehe',
            'refund-date'           => 'Tarehe ya marejesho',
            'credit-note-date'      => 'Tarehe ya hati ya mkopo',
            'source'                => 'Chanzo',
            'due-date'              => 'Tarehe ya mwisho',
            'product'               => 'Bidhaa',
            'quantity'              => 'Idadi',
            'unit'                  => 'Kipimo',
            'unit-price'            => 'Bei ya kipimo',
            'subtotal'              => 'Jumla ndogo',
            'tax'                   => 'Kodi',
            'discount'              => 'Punguzo',
            'grand-total'           => 'Jumla kuu',
            'payment-information'   => 'Taarifa za malipo',
            'payment-communication' => 'Maelezo ya malipo',
            'account-details'       => 'kwenye akaunti hii:',
        ],
    ],
];
