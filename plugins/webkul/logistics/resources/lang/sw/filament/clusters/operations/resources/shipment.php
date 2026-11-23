<?php

return [
    'navigation' => [
        'title' => 'Shehena',
    ],

    'global-search' => [
        'customer'  => 'Mteja',
        'state'     => 'Hali',
        'reference' => 'Kumbukumbu ya mteja',
    ],

    'form' => [
        'tabs' => [
            'general'       => 'Jumla',
            'route'         => 'Njia & vituo',
            'cargo'         => 'Cargo',
            'assignment'    => 'Assignment',
            'confidential'  => 'Confidential',
        ],

        'fields' => [
            'number'                 => 'Nambari ya shehena',
            'number-placeholder'     => 'Amekabidhiwa wakati shehena ni saved',
            'company'                => 'Kampuni',
            'customer'               => 'Mteja',
            'customer-reference'     => 'Kumbukumbu ya mteja',
            'sale-order'             => 'Kutoka agizo la mauzo',
            'sale-order-helper'      => 'Copies sarafu na Kumbukumbu ya mteja kutoka agizo la mauzo ya hii mteja.',
            'service-type'           => 'Service aina',
            'transport-mode'         => 'Transport mode',
            'priority'               => 'Kipaumbele',
            'currency'               => 'Sarafu',
            'origin'                 => 'Asili',
            'destination'            => 'Unakoenda',
            'pickup-address'         => 'Pickup anwani',
            'delivery-address'       => 'Usafirishaji anwani',
            'planned-pickup-at'      => 'Imepangwa pickup',
            'expected-delivery-at'   => 'Inatarajiwa usafirishaji',
            'stops'                  => 'Vituo',
            'stop-type'              => 'Aina',
            'stop-address'           => 'Anwani',
            'stop-contact'           => 'Mwasiliani',
            'stop-phone'             => 'Simu',
            'stop-planned-arrival'   => 'Imepangwa arrival',
            'stop-instructions'      => 'Instructions',
            'declared-value'         => 'Declared thamani',
            'is-fragile'             => 'Fragile',
            'is-hazardous'           => 'Hazardous',
            'is-hazardous-helper'    => 'Flag tu. Dangerous-goods paperwork ni si handled hapa.',
            'cargo-lines'            => 'Cargo',
            'line-description'       => 'Maelezo',
            'line-package-type'      => 'Kifurushi aina',
            'line-quantity'          => 'Kiasi',
            'line-weight'            => 'Uzito',
            'line-volume'            => 'Ujazo',
            'instructions'           => 'Special instructions',
            'dispatcher'             => 'Dispatcher',
            'carrier'                => 'Carrier',
            'carrier-helper'         => 'Kwa subcontracted transport. Carrier gharama ni recorded kama gharama.',
            'carrier-reference'      => 'Carrier kumbukumbu',
            'waybill-no'                      => 'Nambari ya hati ya usafirishaji',
            'line-type'                       => 'Mstari aina',
            'line-product'                    => 'Bidhaa',
            'is-confidential'                 => 'Confidential shehena',
            'is-confidential-helper'          => 'Conceals cargo maelezo washa hati ya usafirishaji na driver\'s usafirishaji fomu.',
            'confidential-description'        => 'Cargo maelezo kwa hati ya usafirishaji',
            'confidential-description-helper' => 'Hii text appears washa hati ya usafirishaji katika place ya kamili cargo orodha.',
            'consignor'                       => 'Imetumwa kwa niaba ya',
            'consignor-helper'                => 'kampuni whose goods ni transported (3PL / white-lebo).',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'Nambari',
            'customer'             => 'Mteja',
            'state'                => 'Hali',
            'planned-pickup-at'    => 'Imepangwa pickup',
            'expected-delivery-at' => 'Inatarajiwa usafirishaji',
            'service-type'         => 'Service',
            'packages'             => 'Vifurushi',
            'dispatcher'           => 'Dispatcher',
            'company'              => 'Kampuni',
        ],

        'filters' => [
            'state'         => 'Hali',
            'customer'      => 'Mteja',
            'service-type'  => 'Service aina',
            'pickup-from'   => 'Pickup kutoka',
            'pickup-until'  => 'Pickup mpaka',
        ],
    ],

    'infolist' => [
        'summary'            => 'Shehena',
        'route'              => 'Njia',
        'cargo'              => 'Cargo',
        'assignment'         => 'Assignment',
        'actual-pickup-at'   => 'Halisi pickup',
        'actual-delivery-at' => 'Halisi usafirishaji',
        'total-weight'       => 'Jumla uzito',
        'total-volume'       => 'Jumla ujazo',
    ],

    'timeline' => [
        'title'   => 'Timeline',
        'columns' => [
            'occurred-at' => 'Wakati',
            'type'        => 'Event',
            'source'      => 'Chanzo',
            'user'        => 'Na',
            'location'    => 'Mahali',
            'notes'       => 'Maelezo',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'Thibitisha',
            'heading'      => 'Thibitisha hii shehena?',
            'notification' => 'Shehena imethibitishwa',
        ],

        'hold' => [
            'label'        => 'Put washa hold',
            'heading'      => 'Put hii shehena washa hold?',
            'reason'       => 'Sababu',
            'notification' => 'Shehena put washa hold',
        ],

        'release' => [
            'label'        => 'Toleo',
            'heading'      => 'Toleo hii shehena?',
            'description'  => 'shehena goes rudi hadi Imethibitishwa na unaweza kuwa amekabidhiwa hadi safari tena.',
            'notification' => 'Shehena released',
        ],

        'cancel' => [
            'label'        => 'Ghairi shehena',
            'heading'      => 'Ghairi hii shehena?',
            'description'  => 'imeghairiwa shehena haiwezekani kuwa reopened. Yake charges stay kwa kumbukumbu.',
            'reason'       => 'Sababu',
            'notification' => 'Shehena imeghairiwa',
        ],
    ],
];
