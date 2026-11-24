<?php

return [
    'title' => 'Watumiaji',

    'navigation' => [
        'title' => 'Watumiaji',
    ],

    'global-search' => [
        'email' => 'Barua pepe',
    ],

    'form' => [
        'validation' => [
            'needs-attention'                 => 'Baadhi ya sehemu zinahitaji kuangaliwa',
            'cannot-remove-last-admin'        => 'Haiwezekani kuondoa jukumu la msimamizi kwa msimamizi wa mwisho.',
            'company-invalid'                 => 'Kampuni moja au zaidi zilizochaguliwa si halali.',
            'company-not-assigned'            => 'Unaweza kugawa watumiaji kwa kampuni zilizogawiwa akaunti yako pekee.',
            'company-required'                => 'Chagua angalau kampuni moja.',
            'first-user-must-be-admin'        => 'Mtumiaji wa kwanza kwenye mfumo lazima apewe jukumu la msimamizi.',
            'role-invalid'                    => 'Chagua angalau jukumu moja halali.',
            'role-not-assignable'             => 'Unaweza kugawa majukumu yasiyolindwa pekee ambayo ruhusa zake unazo.',
            'self-administration-forbidden'   => 'Huwezi kubadilisha majukumu yako mwenyewe wala kampuni ulizogawiwa.',
            'target-administration-forbidden' => 'Ni Super Admin pekee anayeweza kubadilisha msimamizi aliyelindwa.',
        ],

        'sections' => [
            'general-information' => [
                'title'  => 'Taarifa za jumla',
                'fields' => [
                    'name'                         => 'Jina',
                    'email'                        => 'Barua pepe',
                    'username'                     => 'Jina la mtumiaji',
                    'username-helper'              => 'Si lazima. Humruhusu mtu huyu kuingia kwa jina fupi badala ya barua pepe: herufi, nambari, nukta, kistari au kistari cha chini, herufi 3 hadi 50, bila @.',
                    'password'                     => 'Nenosiri',
                    'password-confirmation'        => 'Thibitisha nenosiri',
                    'must-change-password'         => 'Hitaji nenosiri jipya akiingia tena',
                    'must-change-password-helper'  => 'Tumia hili unapoweka nenosiri mwenyewe na kulikabidhi. Atabaki kwenye ukurasa wa wasifu wake hadi alibadilishe, na sharti hili huondoka mara tu atakapofanya hivyo.',
                ],
            ],

            'permissions' => [
                'title'  => 'Ruhusa',
                'fields' => [
                    'roles'                                    => 'Majukumu',
                    'permissions'                              => 'Ruhusa',
                    'resource-permission'                      => 'Ruhusa ya rekodi',
                    'resource-permission-self-change-disabled' => 'Huwezi kubadilisha ruhusa yako mwenyewe ya rekodi. Muombe msimamizi mwingine aisasishe.',
                    'can-see-dashboard'                        => 'Ona dashibodi',
                    'can-see-dashboard-helper'                 => 'Ikizimwa, Muhtasari na kila dashibodi hufichwa kwa mtumiaji huyu.',
                    'can-see-money-figures'                    => 'Ona takwimu za fedha',
                    'can-see-money-figures-helper'             => 'Ikizimwa, dashibodi za mauzo, ankara, ununuzi na uhasibu hufichwa.',
                    'teams'                                    => 'Timu',
                ],
            ],

            'avatar' => [
                'title' => 'Picha ya wasifu',
            ],

            'lang-and-status' => [
                'title'  => 'Lugha na hali',
                'fields' => [
                    'language' => 'Lugha inayopendelewa',
                    'status'   => 'Hali',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Kampuni nyingi',
                'allowed-companies'           => 'Kampuni zinazoruhusiwa',
                'default-company'             => 'Kampuni chaguomsingi',
                'default-company-not-allowed' => 'Kampuni chaguomsingi lazima iwe mojawapo ya kampuni zinazoruhusiwa.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Picha ya wasifu',
            'name'                => 'Jina',
            'email'               => 'Barua pepe',
            'teams'               => 'Timu',
            'role'                => 'Jukumu',
            'resource-permission' => 'Ruhusa ya rekodi',
            'default-company'     => 'Kampuni chaguomsingi',
            'allowed-company'     => 'Kampuni inayoruhusiwa',
            'created-by'          => 'Imeundwa na',
            'created-at'          => 'Iliundwa',
            'updated-at'          => 'Ilisasishwa',
        ],

        'filters' => [
            'resource-permission' => 'Ruhusa ya rekodi',
            'teams'               => 'Timu',
            'roles'               => 'Majukumu',
            'default-company'     => 'Kampuni chaguomsingi',
            'allowed-companies'   => 'Kampuni zinazoruhusiwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mtumiaji amehaririwa',
                    'body'  => 'Mtumiaji amehaririwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mtumiaji amefutwa',
                    'body'  => 'Mtumiaji amefutwa kwa mafanikio.',
                    'error' => [
                        'title' => 'Mtumiaji hawezi kufutwa',
                        'body'  => 'Huyu ni mtumiaji chaguomsingi, au huwezi kujifuta mwenyewe.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Mtumiaji amerejeshwa',
                    'body'  => 'Mtumiaji amerejeshwa kwa mafanikio.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Watumiaji wamerejeshwa',
                    'body'  => 'Watumiaji wamerejeshwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Watumiaji wamefutwa',
                    'body'  => 'Watumiaji wamefutwa kwa mafanikio.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Watumiaji wamefutwa kabisa',
                    'body'  => 'Watumiaji wamefutwa kabisa kwa mafanikio.',
                    'error' => [
                        'title' => 'Mtumiaji hakuweza kufutwa',
                        'body'  => 'Mtumiaji hawezi kufutwa kwa sababu anatumika kwa sasa.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Watumiaji wameundwa',
                    'body'  => 'Watumiaji wameundwa kwa mafanikio.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Taarifa za jumla',
                'entries' => [
                    'name'                  => 'Jina',
                    'email'                 => 'Barua pepe',
                    'password'              => 'Nenosiri',
                    'password-confirmation' => 'Thibitisha nenosiri',
                ],
            ],

            'permissions' => [
                'title'   => 'Ruhusa',
                'entries' => [
                    'roles'               => 'Majukumu',
                    'permissions'         => 'Ruhusa',
                    'resource-permission' => 'Ruhusa ya rekodi',
                    'teams'               => 'Timu',
                ],
            ],

            'avatar' => [
                'title' => 'Picha ya wasifu',
            ],

            'lang-and-status' => [
                'title'   => 'Lugha na hali',
                'entries' => [
                    'language' => 'Lugha inayopendelewa',
                    'status'   => 'Hali',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Kampuni nyingi',
                'allowed-companies'           => 'Kampuni zinazoruhusiwa',
                'default-company'             => 'Kampuni chaguomsingi',
                'default-company-not-allowed' => 'Kampuni chaguomsingi lazima iwe mojawapo ya kampuni zinazoruhusiwa.',
            ],
        ],
    ],
];
