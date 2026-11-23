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
            'needs-attention'                 => 'Baadhi sehemu need attention',            'cannot-remove-last-admin'        => 'Cannot remove the admin role from the last admin user.',
            'company-invalid'                 => 'One au zaidi imechaguliwa makampuni ni si halali.',
            'company-not-assigned'            => 'Wewe mei kabidhi watumiaji tu hadi makampuni amekabidhiwa hadi yako akaunti.',
            'company-required'                => 'Chagua saa kidogo one kampuni.',
            'first-user-must-be-admin'        => 'kwanza mtumiaji katika mfumo lazima kuwa amekabidhiwa msimamizi jukumu.',
            'role-invalid'                    => 'Chagua saa kidogo one halali jukumu.',
            'role-not-assignable'             => 'Wewe mei kabidhi tu non-protected majukumu whose ruhusa wewe possess.',
            'self-administration-forbidden'   => 'Wewe haiwezekani badilisha yako yake majukumu au kampuni assignments.',
            'target-administration-forbidden' => 'Tu Super Msimamizi mei modify protected msimamizi.',
        ],

        'sections' => [
            'general-information' => [
                'title'  => 'Jumla Taarifa',
                'fields' => [
                    'name'                         => 'Jina',
                    'email'                        => 'Barua pepe',
                    'username'                     => 'Username',
                    'username-helper'              => 'Si lazima. Lets hii person ingia na short jina instead ya barua pepe: letters, numbers, dot, dash au underscore, 3 hadi 50 characters, hakuna @.',
                    'password'                     => 'Nenosiri',
                    'password-confirmation'        => 'Nenosiri Confirmation',
                    'must-change-password'         => 'Require mpya nenosiri saa ifuatayo sign-katika',
                    'must-change-password-helper'  => 'Tumia hii wakati wewe weka nenosiri yourself na passed hii washa. Wao ita kuwa held washa yao wasifu ukurasa mpaka wao replace hii, na requirement clears kama hivi karibuni kama wao.',
                ],
            ],

            'permissions' => [
                'title'  => 'Ruhusa',
                'fields' => [
                    'roles'                                    => 'Majukumu',
                    'permissions'                              => 'Ruhusa',
                    'resource-permission'                      => 'Resource Ruhusa',
                    'resource-permission-self-change-disabled' => 'Wewe haiwezekani badilisha yako yake resource ruhusa. Ask another msimamizi hadi sasisha hii.',
                    'can-see-dashboard'                        => 'See dashibodi',
                    'can-see-dashboard-helper'                 => 'Zima hides Muhtasari na kila dashibodi kwa hii mtumiaji.',
                    'can-see-money-figures'                    => 'See money figures',
                    'can-see-money-figures-helper'             => 'Zima hides mauzo, ankara, ununuzi na uhasibu dashboards.',
                    'teams'                                    => 'Teams',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'  => 'Lugha & Hali',
                'fields' => [
                    'language' => 'Preferred Lugha',
                    'status'   => 'Hali',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multi Kampuni',
                'allowed-companies'           => 'Inaruhusiwa Makampuni',
                'default-company'             => 'Chaguo-msingi Kampuni',
                'default-company-not-allowed' => 'chaguo-msingi kampuni lazima kuwa one ya inaruhusiwa makampuni.',
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'avatar'              => 'Avatar',
            'name'                => 'Jina',
            'email'               => 'Barua pepe',
            'teams'               => 'Teams',
            'role'                => 'Jukumu',
            'resource-permission' => 'Resource Ruhusa',
            'default-company'     => 'Chaguo-msingi Kampuni',
            'allowed-company'     => 'Inaruhusiwa Kampuni',
            'created-by'          => 'Imeundwa Na',
            'created-at'          => 'Imeundwa',
            'updated-at'          => 'Imesasishwa',
        ],

        'filters' => [
            'resource-permission' => 'Resource Ruhusa',
            'teams'               => 'Teams',
            'roles'               => 'Majukumu',
            'default-company'     => 'Chaguo-msingi Kampuni',
            'allowed-companies'   => 'Inaruhusiwa Makampuni',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mtumiaji edited',
                    'body'  => 'mtumiaji ina edited successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mtumiaji imefutwa',
                    'body'  => 'mtumiaji ina imefutwa successfully.',
                    'error' => [
                        'title' => 'Mtumiaji Haiwezekani Kuwa Imefutwa',
                        'body'  => 'Hii ni chaguo-msingi mtumiaji au wewe haiwezekani futa yourself.',
                    ],
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Mtumiaji restored',
                    'body'  => 'mtumiaji ina restored successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Watumiaji restored',
                    'body'  => 'watumiaji ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Watumiaji imefutwa',
                    'body'  => 'watumiaji ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Watumiaji force imefutwa',
                    'body'  => 'watumiaji ina force imefutwa successfully.',
                    'error' => [
                        'title' => 'Mtumiaji could si kuwa imefutwa',
                        'body'  => 'mtumiaji haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                    ],
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Watumiaji imeundwa',
                    'body'  => 'watumiaji ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title'   => 'Jumla Taarifa',
                'entries' => [
                    'name'                  => 'Jina',
                    'email'                 => 'Barua pepe',
                    'password'              => 'Nenosiri',
                    'password-confirmation' => 'Nenosiri Confirmation',
                ],
            ],

            'permissions' => [
                'title'   => 'Ruhusa',
                'entries' => [
                    'roles'               => 'Majukumu',
                    'permissions'         => 'Ruhusa',
                    'resource-permission' => 'Resource Ruhusa',
                    'teams'               => 'Teams',
                ],
            ],

            'avatar' => [
                'title' => 'Avatar',
            ],

            'lang-and-status' => [
                'title'   => 'Lugha & Hali',
                'entries' => [
                    'language' => 'Preferred Lugha',
                    'status'   => 'Hali',
                ],
            ],

            'multi-company' => [
                'title'                       => 'Multi Kampuni',
                'allowed-companies'           => 'Inaruhusiwa Makampuni',
                'default-company'             => 'Chaguo-msingi Kampuni',
                'default-company-not-allowed' => 'chaguo-msingi kampuni lazima kuwa one ya inaruhusiwa makampuni.',
            ],
        ],
    ],
];
