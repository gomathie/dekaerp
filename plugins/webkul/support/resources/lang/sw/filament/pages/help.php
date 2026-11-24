<?php

return [
    'navigation' => [
        'label' => 'Msaada',
    ],

    'title'      => 'Msaada',
    'heading'    => 'Msaada na rasilimali',
    'subheading' => 'Kila unachohitaji ili kunufaika kikamilifu na :app.',

    'guides' => [
        'group' => 'Miongozo',

        'superadmin' => [
            'title'       => 'Mwongozo wa msimamizi mkuu',
            'description' => 'Simamia usakinishaji wa :app wenyewe: moduli, kampuni, majukumu ya wasimamizi, masasisho na matengenezo.',
            'button'      => 'Fungua mwongozo wa msimamizi mkuu',
        ],

        'admin' => [
            'title'       => 'Mwongozo wa msimamizi',
            'description' => 'Sanidi :app kwa kampuni yako, timu, moduli na udhibiti kabla kazi za kila siku hazijaanza.',
            'button'      => 'Orodha ya ukaguzi wa usanidi',
            'sections'    => [
                [
                    'title' => 'Sanidi kampuni',
                    'items' => [
                        '0' => 'Unda au kagua kampuni, maghala, sarafu, kodi na mipangilio ya mwaka wa fedha.',
                        '1' => 'Wape watumiaji kampuni wanazoweza kufikia na uthibitishe kibadilishaji cha kampuni hai.',
                        '2' => 'Sanidi mfuatano wa namba, masharti ya malipo, bidhaa, vipimo na violezo vya hati kabla miamala haijaanza.',
                    ],
                ],
                [
                    'title' => 'Dhibiti ufikiaji',
                    'items' => [
                        '0' => 'Unda majukumu kwa timu za fedha, mauzo, hisa, rasilimali watu, miradi na uongozi.',
                        '1' => 'Toa ruhusa za moduli kwa watumiaji wanaozihitaji tu.',
                        '2' => 'Kagua sehemu maalum, uingizaji, uhamishaji na tokeni za API kabla ya kutoa ufikiaji mpana.',
                    ],
                ],
                [
                    'title' => 'Endesha na tunza',
                    'items' => [
                        '0' => 'Sakinisha moduli zinazotumiwa na biashara tu, kisha ficha au ondoa zisizotumika.',
                        '1' => 'Fuatilia majadiliano, viambatisho, idhini na umiliki wa rekodi wakati wa kuanza kutumia mfumo.',
                        '2' => 'Weka mipangilio ya barua pepe, PDF, nakala rudufu na masasisho sawa na sera ya usambazaji.',
                    ],
                ],
            ],
        ],

        'user' => [
            'title'          => 'Mwongozo wa mtumiaji',
            'description'    => 'Tumia mwongozo wa mtumiaji uliojengewa ndani kwa maelekezo ya DEKA na kurasa za marejeo ya moduli kwa kazi za kila siku kwenye :app.',
            'button'         => 'Fungua mwongozo wa mtumiaji',
            'full_heading'   => 'Mwongozo kamili wa mtumiaji',
            'count'          => '{0} Hakuna kurasa za mwongozo|{1} Ukurasa 1 wa mwongozo|[2,*] Kurasa :count za mwongozo',
            'category_count' => '{0} Hakuna kurasa|{1} Ukurasa 1|[2,*] Kurasa :count',
            'callouts'       => [
                'tip'       => 'Kidokezo',
                'note'      => 'Kumbuka',
                'important' => 'Muhimu',
            ],
            'sections'    => [
                [
                    'title' => 'Kuanza',
                    'items' => [
                        '0' => 'Muhtasari wa jukwaa na urambazaji',
                        '1' => 'Kampuni nyingi na kibadilishaji',
                    ],
                ],
                [
                    'title' => 'Michakato mikuu',
                    'items' => [
                        '0' => 'Uza: mauzo na ankara',
                        '1' => 'Nunua: ununuzi na uhasibu',
                        '2' => 'Tengeneza na sogeza: hisa na uzalishaji',
                        '3' => 'Endesha: miradi, rasilimali watu na matengenezo',
                        '4' => 'Jukwaa, API na kujipangishia',
                    ],
                ],
                [
                    'title' => 'Marejeo ya moduli: moduli 12 / kurasa 69',
                    'items' => [
                        '0' => 'Ankara, mauzo, ununuzi, hisa, uzalishaji, matengenezo',
                        '1' => 'Wahusika, miradi, wafanyakazi, uajiri, likizo',
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'group' => 'Huduma',

        'cloud' => [
            'title'       => 'Upangishaji wa wingu',
            'description' => 'Upangishaji wa wingu unaosimamiwa kwa gharama nafuu: weka ERP yako kwa dakika chache, ikiwa imeboreshwa, salama na inayoweza kukua.',
            'button'      => 'Tazama bei',
        ],

        'support' => [
            'title'       => 'Usaidizi na matengenezo',
            'description' => 'Usaidizi maalum wa kiufundi na mipango ya matengenezo endelevu ili ERP yako ibaki salama, ya kisasa na ifanye kazi vizuri.',
            'button'      => 'Tazama maelezo',
        ],

        'paid' => [
            'title'       => 'Huduma za kulipia',
            'description' => 'Msaada wa wataalamu katika kutengeneza moduli, ubinafsishaji, uhamishaji wa data, kusasisha matoleo na miunganisho maalum.',
            'button'      => 'Anza',
        ],
    ],

    'resources' => [
        'group' => 'Rasilimali na nyaraka',

        'extensions' => [
            'title'       => 'Moduli',
            'description' => 'Vinjari nyongeza rasmi na za jamii ili kupanua :app kwa moduli na vipengele vipya.',
            'button'      => 'Vinjari moduli',
        ],

        'docs' => [
            'title'       => 'Nyaraka za wasanidi',
            'description' => 'Miongozo na marejeo ya wasanidi ya kukusaidia kutengeneza programu-jalizi, kubinafsisha moduli na kupanua jukwaa.',
            'button'      => 'Soma nyaraka',
        ],

        'guide' => [
            'title'       => 'Mwongozo wa mtumiaji',
            'description' => 'Miongozo ya hatua kwa hatua kuhusu usakinishaji, usanidi na matumizi ya kila siku ya kila moduli ya ERP.',
            'button'      => 'Fungua mwongozo',
        ],

        'website' => [
            'title'       => 'Tovuti ya bidhaa',
            'description' => 'Muhtasari wa moduli, maelezo ya matoleo na kila kitu :app inachoweza kufanya, kwenye tovuti ya bidhaa.',
            'button'      => 'Tembelea tovuti',
        ],
    ],

    'empty' => 'Viungo vya msaada bado havijawekwa kwa usakinishaji huu wa :app. Wasiliana na msimamizi wa mfumo wako kwa msaada.',

    'contact' => [
        'title'       => 'Bado unahitaji msaada?',
        'description' => 'Zungumza na timu yetu kuhusu upangishaji, utekelezaji, utengenezaji maalum au jambo lingine lolote.',
        'live_chat'   => 'Gumzo la moja kwa moja',
        'button'      => 'Wasiliana nasi',
    ],
];
