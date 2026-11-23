<?php

return [
    'post-action-validate' => [
        'customer-required'    => 'Tafadhali provide halali Mteja hadi proceed na Ankara ya mteja validation.',
        'vendor-required'      => 'Tafadhali provide halali Msambazaji hadi proceed na Bili ya msambazaji validation.',
        'bank-archived'        => 'imechaguliwa Mshirika Benki attached hadi hii ankara ni imehifadhiwa.',
        'negative-amount'      => 'Ankara unaweza si kuwa imethibitishwa na negative jumla kiasi.',
        'date-required'        => 'Tafadhali provide halali Bili/Rudisho Tarehe hadi proceed na Bili/Rudisho validation.',
        'currency-archived'    => 'Wewe haiwezekani thibitisha ankara na imehifadhiwa sarafu.',
        'account-deprecated'   => 'One au zaidi mistari katika hii ankara ni kwa kutumia deprecated akaunti.',
        'lines-required'       => 'Tafadhali ongeza saa kidogo one mstari hadi ankara.',
        'draft-state-required' => 'Tu ankara katika rasimu hali unaweza kuwa imethibitishwa.',
        'journal-archived'     => 'Wewe haiwezekani thibitisha ankara na imehifadhiwa jarida.',
        'unbalanced'           => 'Hii uingizaji wa jarida ni unbalanced na haiwezekani kuwa imewekwa. Jumla debiti lazima equal jumla krediti.',
    ],

    'change-reason' => [
        'required' => 'Give sababu kwa changing imethibitishwa hati.',
        'field'    => 'Sababu kwa badilisha',
        'helper'   => 'Saved katika hati historia, kwa mfano "Mteja asked kwa 10% punguzo".',
        'reset'    => 'Weka upya hadi rasimu. Sababu: :reason',
        'cancelled' => 'Imeghairiwa. Sababu: :reason',
        'api'      => 'Imebadilishwa kupitia API',
    ],

    'lock-date' => [
        'locked'        => 'Maingizo dated washa au kabla ya :date ni imefungwa. Ask yako msimamizi hadi hamisha lock tarehe kwanza.',
        'not-allowed'   => 'Tu Kampuni Msimamizi au accountant na unlock ruhusa unaweza badilisha lock tarehe.',
        'future'        => 'lock tarehe haiwezekani kuwa katika zijazo.',
        'action'        => 'Lock tarehe',
        'field'         => 'Lock maingizo juu hadi',
        'helper'        => 'Imewekwa maingizo dated washa au kabla ya hii tarehe haiwezekani kuwa imebadilishwa. Likizo tupu hadi unlock zote periods.',
        'current'       => 'Currently imefungwa juu hadi :date.',
        'none'          => 'Hakuna kipindi ni imefungwa.',
        'saved'         => 'Lock tarehe saved',
    ],

    'documents' => [
        'titles' => [
            'invoice'     => 'Ankara Kitambulisho #:name',
            'bill'        => 'Bili ya msambazaji Kitambulisho #:name',
            'refund'      => 'Rudisho Kitambulisho #:name',
            'credit-note' => 'Noti ya mkopo Kitambulisho #:name',
        ],

        'labels' => [
            'invoice-date'          => 'Tarehe ya ankara',
            'bill-date'             => 'Tarehe',
            'refund-date'           => 'Rudisho Tarehe',
            'credit-note-date'      => 'Noti ya mkopo Tarehe',
            'source'                => 'Chanzo',
            'due-date'              => 'Tarehe ya malipo',
            'product'               => 'Bidhaa',
            'quantity'              => 'Kiasi',
            'unit'                  => 'Uniti',
            'unit-price'            => 'Bei ya uniti',
            'subtotal'              => 'Jumla ndogo',
            'tax'                   => 'Kodi',
            'discount'              => 'Punguzo',
            'grand-total'           => 'Grand Jumla',
            'payment-information'   => 'Malipo Taarifa',
            'payment-communication' => 'Malipo Mawasiliano',
            'account-details'       => 'washa hii akaunti maelezo:',
        ],
    ],
];
