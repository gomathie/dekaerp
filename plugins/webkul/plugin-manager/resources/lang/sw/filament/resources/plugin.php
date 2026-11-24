<?php

return [

    'title' => 'Programu-jalizi',

    'table' => [
        'version'             => 'Toleo',
        'dependencies'        => 'Utegemezi',
        'dependencies_suffix' => ' Utegemezi',
    ],

    'status' => [
        'installed'     => 'Imesakinishwa',
        'not_installed' => 'Haijasakinishwa',
    ],

    'filters' => [
        'installation_status' => 'Hali ya usakinishaji',
        'all_plugins'         => 'Programu-jalizi zote',
        'installed'           => 'Imesakinishwa',
        'not_installed'       => 'Haijasakinishwa',
        'active_status'       => 'Hali ya utendaji',
        'author'              => 'Mwandishi',
        'webkul'              => 'Webkul',
        'third_party'         => 'Mhusika wa tatu',
    ],

    'actions' => [
        'install' => [
            'title'       => 'Sakinisha',
            'heading'     => 'Sakinisha programu-jalizi :name',
            'description' => 'Una uhakika unataka kusakinisha programu-jalizi \':name\'? Hii itaendesha migrations na seeders.',
            'submit'      => 'Sakinisha programu-jalizi',
        ],

        'companies' => [
            'title'       => 'Kampuni',
            'heading'     => 'Kampuni zipi zinaweza kutumia :name?',
            'description' => 'Kusakinisha programu-jalizi kunafanya msimbo wake upatikane kwa mfumo mzima. Hapa unaamua kampuni zipi zinaweza kuitumia hasa: ipe kampuni ambazo usajili wao unaijumuisha. Ukiondoa kampuni, programu-jalizi itafichwa kwa kila mtu ndani yake.',
            'field'       => 'Kampuni',

            'notification' => [
                'title' => 'Upatikanaji wa programu-jalizi umesasishwa',
                'body'  => ':name sasa inapatikana kwa kampuni :count.',
            ],
        ],
        'uninstall' => [
            'title'      => 'Ondoa',
            'heading'    => 'Ondoa programu-jalizi',
            'submit'     => 'Ondoa programu-jalizi',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Programu-jalizi imesakinishwa',
            'body'  => 'Programu-jalizi \':name\' imesakinishwa.',
        ],
        'installing' => [
            'title' => 'Inasakinisha :name',
            'body'  => 'Hii huchukua dakika moja au mbili. Utapata arifa itakapokamilika, na unaweza kuendelea kufanya kazi wakati huo.',
        ],
        'installing-already' => [
            'title' => ':name tayari inasakinishwa',
        ],
        'installed-failed' => [
            'title' => 'Usakinishaji umeshindwa',
        ],
        'uninstalled' => [
            'title' => 'Programu-jalizi imeondolewa',
            'body'  => 'Programu-jalizi \':name\' imeondolewa.',
        ],

        'cache-rebuild-failed' => [
            'title' => 'Akiba imefutwa lakini haijajengwa upya',
            'body'  => 'Mabadiliko ya programu-jalizi yenyewe yamekamilika. Programu sasa inaendeshwa bila akiba ya usanidi na njia, jambo ambalo ni polepole zaidi lakini sahihi. Endesha "php artisan optimize" na usome matokeo yake ili kujua kwa nini ilishindwa; hitilafu pia imeripotiwa.',
        ],
        'uninstalled-failed' => [
            'title' => 'Uondoaji umeshindwa',
        ],
        'uninstalled-blocked' => [
            'title' => 'Haiwezekani kuondoa programu-jalizi',
            'body'  => 'Programu-jalizi \':name\' ina programu-jalizi tegemezi zilizosakinishwa ambazo lazima ziondolewe kwanza: :dependents.',
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => ' Taarifa za programu-jalizi',
            'dependencies' => 'Utegemezi',
        ],
        'name'         => 'Jina la programu-jalizi',
        'author'       => 'Mwandishi',
        'version'      => 'Toleo',
        'dependencies' => 'Programu-jalizi zinazohitajika',
        'dependents'   => 'Programu-jalizi zinazotegemea hii',
        'is_installed' => 'Hali ya usakinishaji',
        'license'      => 'Leseni',
        'summary'      => 'Maelezo',

        'dependencies-repeater' => [
            'title'        => 'Programu-jalizi zinazohitajika',
            'name'         => 'Jina la programu-jalizi',
            'is_installed' => 'Imesakinishwa',
            'placeholder'  => 'Hakuna utegemezi unaohitajika',
        ],

        'dependents-repeater' => [
            'title'        => 'Programu-jalizi zinazotegemea hii',
            'name'         => 'Jina la programu-jalizi',
            'is_installed' => 'Imesakinishwa',
            'placeholder'  => 'Hakuna tegemezi',
        ],

    ],

    'names' => [
        'accounting'     => 'Uhasibu',
        'accounts'       => 'Akaunti',
        'analytics'      => 'Uchanganuzi',
        'barcode'        => 'Msimbopau',
        'blogs'          => 'Blogu',
        'chatter'        => 'Mazungumzo',
        'contacts'       => 'Wahusika',
        'employees'      => 'Wafanyakazi',
        'fields'         => 'Sehemu maalum',
        'full-calendar'  => 'Kalenda',
        'inventories'    => 'Bidhaa ghalani',
        'invoices'       => 'Ankara',
        'maintenance'    => 'Matengenezo',
        'manufacturing'  => 'Uzalishaji',
        'partners'       => 'Washirika',
        'payments'       => 'Malipo',
        'plugin-manager' => 'Msimamizi wa programu-jalizi',
        'products'       => 'Bidhaa',
        'projects'       => 'Miradi',
        'purchases'      => 'Ununuzi',
        'recruitments'   => 'Uajiri',
        'sales'          => 'Mauzo',
        'security'       => 'Usalama',
        'support'        => 'Msaada',
        'table-views'    => 'Mionekano ya jedwali',
        'time-off'       => 'Likizo',
        'timesheets'     => 'Rejista za muda',
        'website'        => 'Tovuti',
    ],

    'summaries' => [
        'accounting'     => 'Simamia chati ya akaunti, majarida na maingizo ya fedha',
        'accounts'       => 'Usimamizi wa msingi wa akaunti na mipangilio ya fedha',
        'analytics'      => 'Ripoti na dashibodi kwa maarifa ya biashara',
        'barcode'        => 'Programu ya msimbopau kwa bidhaa ghalani na uzalishaji',
        'blogs'          => 'Simamia blogu',
        'chatter'        => 'Kumbukumbu za shughuli, ujumbe na ufuatiliaji wa rekodi',
        'contacts'       => 'Usimamizi wa wahusika kwa wateja na wasambazaji',
        'employees'      => 'Usimamizi wa wafanyakazi',
        'fields'         => 'Ongeza sehemu maalum kwenye rasilimali',
        'full-calendar'  => 'Mionekano ya kalenda na upangaji wa matukio',
        'inventories'    => 'Usimamizi wa bidhaa ghalani na maghala',
        'invoices'       => 'Kutengeneza na kusimamia ankara',
        'maintenance'    => 'Usimamizi wa matengenezo',
        'manufacturing'  => 'Usimamizi wa uzalishaji',
        'partners'       => 'Simamia washirika wa biashara',
        'payments'       => 'Simamia malipo na miamala',
        'plugin-manager' => 'Msimamizi wa programu-jalizi wa DEKA Central',
        'products'       => 'Katalogi ya bidhaa na usimamizi wa aina',
        'projects'       => 'Upangaji na usimamizi wa miradi',
        'purchases'      => 'Manunuzi na usimamizi wa oda za ununuzi',
        'recruitments'   => 'Ufuatiliaji wa waombaji na uajiri',
        'sales'          => 'Mfumo wa mauzo na usimamizi wa fursa',
        'security'       => 'Majukumu, ruhusa na udhibiti wa ufikiaji',
        'support'        => 'Msaada kwa wateja na tiketi',
        'table-views'    => 'Mionekano ya jedwali iliyohifadhiwa na inayoweza kubadilishwa',
        'time-off'       => 'Usimamizi na ufuatiliaji wa likizo',
        'timesheets'     => 'Ufuatiliaji wa saa za kazi za wafanyakazi',
        'website'        => 'Tovuti kwa wateja',
    ],

];
