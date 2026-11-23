<?php

return [
    'navigation' => [
        'label' => 'Msaada',
    ],

    'title'      => 'Msaada',
    'heading'    => 'Msaada & Resources',
    'subheading' => 'Everything wewe need hadi pata wengi nje ya :app.',

    'guides' => [
        'group' => 'Miongozo',

        'superadmin' => [
            'title'       => 'Msimamizi mkuu Mwongozo',
            'description' => 'Administer :app installation itself: moduli, makampuni, msimamizi majukumu, upgrades na matengenezo.',
            'button'      => 'Wazi Msimamizi mkuu Mwongozo',
        ],

        'admin' => [
            'title'       => 'Msimamizi Mwongozo',
            'description' => 'Weka juu :app kwa yako kampuni, teams, moduli na controls kabla ya kila siku work begins.',
            'button'      => 'Msimamizi setup checklist',
            'sections'    => [
                [
                    'title' => 'Weka juu kampuni',
                    'items' => [
                        '0' => 'Unda au review makampuni, maghala, sarafu, kodi na kifedha mipangilio.',
                        '1' => 'Kabidhi watumiaji hadi makampuni wao unaweza access na thibitisha inatumika kampuni switcher.',
                        '2' => 'Configure sequences, masharti ya malipo, bidhaa, uniti ya measure na hati violezo kabla ya transactions begin.',
                    ],
                ],
                [
                    'title' => 'Control access',
                    'items' => [
                        '0' => 'Unda majukumu kwa fedha, mauzo, ghala, Rasilimali watu, mradi na management teams.',
                        '1' => 'Grant module ruhusa tu hadi watumiaji nani need wao.',
                        '2' => 'Review maalum sehemu, imports, exports na API tokens kabla ya giving broad access.',
                    ],
                ],
                [
                    'title' => 'Operate na maintain',
                    'items' => [
                        '0' => 'Install tu moduli business uses, kisha ficha au ondoa unused moduli.',
                        '1' => 'Monitor discussions, viambatisho, approvals na rekodi ownership wakati wa rollout.',
                        '2' => 'Weka barua pepe, PDF, backup na sasisha mipangilio aligned na deployment policy.',
                    ],
                ],
            ],
        ],

        'user' => [
            'title'          => 'Mtumiaji Mwongozo',
            'description'    => 'Tumia built-katika mtumiaji mwongozo kwa DEKA walkthroughs na module-kumbukumbu kurasa kwa kila siku work katika :app.',
            'button'         => 'Wazi Mtumiaji Mwongozo',
            'full_heading'   => 'Kamili Mtumiaji Mwongozo',
            'count'          => '{0} Hakuna kurasa za mwongozo|{1} Ukurasa 1 wa mwongozo|[2,*] Kurasa :count za mwongozo',
            'category_count' => '{0} Hakuna kurasa|{1} Ukurasa 1|[2,*] Kurasa :count',
            'callouts'       => [
                'tip'       => 'Tip',
                'note'      => 'Maelezo',
                'important' => 'Important',
            ],
            'sections'    => [
                [
                    'title' => 'Getting started',
                    'items' => [
                        '0' => 'Platform Muhtasari & Navigation',
                        '1' => 'Multi-Kampuni Setup & Switcher',
                    ],
                ],
                [
                    'title' => 'Core workflows',
                    'items' => [
                        '0' => 'Sell: Mauzo & Invoicing',
                        '1' => 'Buy: Purchasing & Uhasibu',
                        '2' => 'Fanya & Hamisha: Ghala & Uzalishaji',
                        '3' => 'Run: Miradi, Rasilimali watu & Matengenezo',
                        '4' => 'Jukwaa, API na seva yako',
                    ],
                ],
                [
                    'title' => 'Module kumbukumbu: 12 moduli / 69 kurasa',
                    'items' => [
                        '0' => 'Ankara, Mauzo, Ununuzi, Ghala, Uzalishaji, Matengenezo',
                        '1' => 'Mawasiliano, Mradi, Wafanyakazi, Ajira, Likizo',
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'group' => 'Services',

        'cloud' => [
            'title'       => 'Cloud Hosting',
            'description' => 'Gharama-effective, managed cloud hosting — deploy yako ERP katika dakika, fully optimised, secure na scalable.',
            'button'      => 'See Pricing',
        ],

        'support' => [
            'title'       => 'Support & Matengenezo',
            'description' => 'Dedicated technical support na ongoing matengenezo plans hadi weka yako ERP secure, imesasishwa na running smoothly.',
            'button'      => 'Angalia Maelezo',
        ],

        'paid' => [
            'title'       => 'Imelipwa Services',
            'description' => 'Expert msaada kwa module development, customisation, data migration, toleo upgrades na bespoke integrations.',
            'button'      => 'Pata Started',
        ],
    ],

    'resources' => [
        'group' => 'Resources & Documentation',

        'extensions' => [
            'title'       => 'Moduli',
            'description' => 'Browse official na community ongeza-ons hadi extend :app na mpya moduli na features.',
            'button'      => 'Browse Moduli',
        ],

        'docs' => [
            'title'       => 'Dev Docs',
            'description' => 'Developer miongozo na references hadi msaada wewe build programu-jalizi, customise moduli na extend platform.',
            'button'      => 'Kusoma Docs',
        ],

        'guide' => [
            'title'       => 'Mtumiaji Mwongozo',
            'description' => 'Step-na-step miongozo covering setup, usanidi na everyday tumia ya kila ERP module.',
            'button'      => 'Wazi Mwongozo',
        ],

        'website' => [
            'title'       => 'Bidhaa Site',
            'description' => 'Module overviews, toleo maelezo na everything :app unaweza, washa bidhaa tovuti.',
            'button'      => 'Visit Tovuti',
        ],
    ],

    'empty' => 'Msaada links ina si weka juu kwa hii :app installation bado. Mwasiliani yako mfumo msimamizi kwa assistance.',

    'contact' => [
        'title'       => 'Bado need mkono?',
        'description' => 'Talk hadi yetu timu kuhusu hosting, implementation, maalum development au anything vinginevyo.',
        'live_chat'   => 'Live Chat',
        'button'      => 'Mwasiliani Us',
    ],
];
