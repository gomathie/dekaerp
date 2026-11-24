<?php

return [
    'navigation' => [
        'title' => 'Usafirishaji',
    ],

    'global-search' => [
        'customer'  => 'Mteja',
        'state'     => 'Hali',
        'reference' => 'Marejeleo ya mteja',
    ],

    'form' => [
        'tabs' => [
            'general'       => 'Jumla',
            'route'         => 'Njia na vituo',
            'cargo'         => 'Mzigo',
            'assignment'    => 'Ugawaji',
            'confidential'  => 'Siri',
        ],

        'fields' => [
            'number'                 => 'Namba ya usafirishaji',
            'number-placeholder'     => 'Hutolewa usafirishaji unapohifadhiwa',
            'company'                => 'Kampuni',
            'customer'               => 'Mteja',
            'customer-reference'     => 'Marejeleo ya mteja',
            'sale-order'             => 'Kutoka oda ya mauzo',
            'sale-order-helper'      => 'Hunakili sarafu na marejeleo ya mteja kutoka oda ya mauzo ya mteja huyu.',
            'service-type'           => 'Aina ya huduma',
            'transport-mode'         => 'Njia ya usafiri',
            'priority'               => 'Kipaumbele',
            'currency'               => 'Sarafu',
            'origin'                 => 'Mahali pa kuanzia',
            'destination'            => 'Mahali pa kufikia',
            'pickup-address'         => 'Anwani ya uchukuaji',
            'delivery-address'       => 'Anwani ya uwasilishaji',
            'planned-pickup-at'      => 'Uchukuaji uliopangwa',
            'expected-delivery-at'   => 'Uwasilishaji unaotarajiwa',
            'stops'                  => 'Vituo',
            'stop-type'              => 'Aina',
            'stop-address'           => 'Anwani',
            'stop-contact'           => 'Mhusika',
            'stop-phone'             => 'Simu',
            'stop-planned-arrival'   => 'Kufika kulikopangwa',
            'stop-instructions'      => 'Maelekezo',
            'declared-value'         => 'Thamani iliyotangazwa',
            'is-fragile'             => 'Kinavunjika',
            'is-hazardous'           => 'Hatari',
            'is-hazardous-helper'    => 'Alama tu. Nyaraka za bidhaa hatari hazishughulikiwi hapa.',
            'cargo-lines'            => 'Mzigo',
            'line-description'       => 'Maelezo',
            'line-package-type'      => 'Aina ya kifurushi',
            'line-quantity'          => 'Idadi',
            'line-weight'            => 'Uzito',
            'line-volume'            => 'Ujazo',
            'instructions'           => 'Maelekezo maalum',
            'dispatcher'             => 'Mratibu',
            'carrier'                => 'Msafirishaji',
            'carrier-helper'         => 'Kwa usafiri wa ukandarasi mdogo. Gharama za msafirishaji hurekodiwa kama gharama.',
            'carrier-reference'      => 'Marejeleo ya msafirishaji',
            'waybill-no'                      => 'Namba ya hati ya usafirishaji',
            'line-type'                       => 'Aina ya mstari',
            'line-product'                    => 'Bidhaa',
            'is-confidential'                 => 'Usafirishaji wa siri',
            'is-confidential-helper'          => 'Huficha maelezo ya mzigo kwenye hati ya usafirishaji na fomu ya uwasilishaji ya dereva.',
            'confidential-description'        => 'Maelezo ya mzigo kwa hati ya usafirishaji',
            'confidential-description-helper' => 'Maandishi haya yanaonekana kwenye hati ya usafirishaji badala ya orodha kamili ya mzigo.',
            'consignor'                       => 'Imetumwa kwa niaba ya',
            'consignor-helper'                => 'Kampuni ambayo bidhaa zake zinasafirishwa (3PL / white-label).',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'Namba',
            'customer'             => 'Mteja',
            'state'                => 'Hali',
            'planned-pickup-at'    => 'Uchukuaji uliopangwa',
            'expected-delivery-at' => 'Uwasilishaji unaotarajiwa',
            'service-type'         => 'Huduma',
            'packages'             => 'Vifurushi',
            'dispatcher'           => 'Mratibu',
            'company'              => 'Kampuni',
        ],

        'filters' => [
            'state'         => 'Hali',
            'customer'      => 'Mteja',
            'service-type'  => 'Aina ya huduma',
            'pickup-from'   => 'Uchukuaji kuanzia',
            'pickup-until'  => 'Uchukuaji hadi',
        ],
    ],

    'infolist' => [
        'summary'            => 'Usafirishaji',
        'route'              => 'Njia',
        'cargo'              => 'Mzigo',
        'assignment'         => 'Ugawaji',
        'actual-pickup-at'   => 'Uchukuaji halisi',
        'actual-delivery-at' => 'Uwasilishaji halisi',
        'total-weight'       => 'Uzito jumla',
        'total-volume'       => 'Ujazo jumla',
    ],

    'timeline' => [
        'title'   => 'Mfuatano wa matukio',
        'columns' => [
            'occurred-at' => 'Lini',
            'type'        => 'Tukio',
            'source'      => 'Chanzo',
            'user'        => 'Na',
            'location'    => 'Mahali',
            'notes'       => 'Vidokezo',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'Thibitisha',
            'heading'      => 'Thibitisha usafirishaji huu?',
            'notification' => 'Usafirishaji umethibitishwa',
        ],

        'hold' => [
            'label'        => 'Simamisha',
            'heading'      => 'Simamisha usafirishaji huu?',
            'reason'       => 'Sababu',
            'notification' => 'Usafirishaji umesimamishwa',
        ],

        'release' => [
            'label'        => 'Achilia',
            'heading'      => 'Achilia usafirishaji huu?',
            'description'  => 'Usafirishaji unarudi kuwa Imethibitishwa na unaweza kupangiwa safari tena.',
            'notification' => 'Usafirishaji umeachiliwa',
        ],

        'cancel' => [
            'label'        => 'Ghairi usafirishaji',
            'heading'      => 'Ghairi usafirishaji huu?',
            'description'  => 'Usafirishaji ulioghairiwa hauwezi kufunguliwa tena. Tozo zake zinabaki kwa marejeleo.',
            'reason'       => 'Sababu',
            'notification' => 'Usafirishaji umeghairiwa',
        ],
    ],
];
