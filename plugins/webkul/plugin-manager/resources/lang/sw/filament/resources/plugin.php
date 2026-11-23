<?php

return [

    'title' => 'Programu-jalizi',

    'table' => [
        'version'             => 'Toleo',
        'dependencies'        => 'Dependencies',
        'dependencies_suffix' => ' Dependencies',
    ],

    'status' => [
        'installed'     => 'Installed',
        'not_installed' => 'Si Installed',
    ],

    'filters' => [
        'installation_status' => 'Installation Hali',
        'all_plugins'         => 'Zote Programu-jalizi',
        'installed'           => 'Installed',
        'not_installed'       => 'Si Installed',
        'active_status'       => 'Inatumika Hali',
        'author'              => 'Author',
        'webkul'              => 'Webkul',
        'third_party'         => 'Third Party',
    ],

    'actions' => [
        'install' => [
            'title'       => 'Install',
            'heading'     => 'Install Programu-jalizi :name',
            'description' => "Ni wewe sure wewe want hadi install ':name' programu-jalizi? Hii ita run migrations na seeders.",
            'submit'      => 'Install Programu-jalizi',
        ],

        'companies' => [
            'title'       => 'Makampuni',
            'heading'     => 'Ambayo makampuni mei tumia :name?',
            'description' => 'Installing programu-jalizi makes yake msimbo inapatikana hadi whole installation. Hii decides ambayo makampuni mei actually tumia hii — grant hii hadi makampuni whose subscription includes hii. Removing kampuni hides programu-jalizi kutoka everyone katika hii.',
            'field'       => 'Makampuni',

            'notification' => [
                'title' => 'Programu-jalizi availability imesasishwa',
                'body'  => ':name ni sasa inapatikana hadi :count kampuni(ies).',
            ],
        ],
        'uninstall' => [
            'title'      => 'Uninstall',
            'heading'    => 'Uninstall Programu-jalizi',
            'submit'     => 'Uninstall Programu-jalizi',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Programu-jalizi Installed Successfully',
            'body'  => "':name' programu-jalizi ina installed.",
        ],
        'installing' => [
            'title' => 'Installing :name',
            'body'  => 'Hii takes dakika au two. Wewe ita pata arifa wakati hii ni imekamilika, na unaweza weka working meanwhile.',
        ],
        'installing-already' => [
            'title' => ':name ni tayari installed',
        ],
        'installed-failed' => [
            'title' => 'Installation Imeshindwa',
        ],
        'uninstalled' => [
            'title' => 'Programu-jalizi Uninstalled Successfully',
            'body'  => "':name' programu-jalizi ina uninstalled.",
        ],

        'cache-rebuild-failed' => [
            'title' => 'Caches zilikuwa cleared lakini si rebuilt',
            'body'  => 'programu-jalizi badilisha itself finished. application ni sasa running bila cached config na njia, ambayo ni slower lakini correct. Run "php artisan optimize" na kusoma yake output hadi find nje kwa nini hii imeshindwa; hitilafu ina pia reported.',
        ],
        'uninstalled-failed' => [
            'title' => 'Uninstallation Imeshindwa',
        ],
        'uninstalled-blocked' => [
            'title' => 'Haiwezekani Uninstall Programu-jalizi',
            'body'  => "':name' programu-jalizi ina installed dependents hiyo lazima kuwa uninstalled kwanza: :dependents.",
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => ' Programu-jalizi Taarifa',
            'dependencies' => 'Dependencies',
        ],
        'name'         => 'Programu-jalizi Jina',
        'author'       => 'Author',
        'version'      => 'Toleo',
        'dependencies' => 'Inahitajika Programu-jalizi',
        'dependents'   => 'Programu-jalizi Hiyo Depend Washa Hii',
        'is_installed' => 'Installation Hali',
        'license'      => 'License',
        'summary'      => 'Maelezo',

        'dependencies-repeater' => [
            'title'        => 'Inahitajika Programu-jalizi',
            'name'         => 'Programu-jalizi Jina',
            'is_installed' => 'Installed',
            'placeholder'  => 'Hakuna dependencies inahitajika',
        ],

        'dependents-repeater' => [
            'title'        => 'Programu-jalizi Hiyo Depend Washa Hii',
            'name'         => 'Programu-jalizi Jina',
            'is_installed' => 'Installed',
            'placeholder'  => 'Hakuna dependents',
        ],

    ],

    'names' => [
        'accounting'     => 'Uhasibu',
        'accounts'       => 'Akaunti',
        'analytics'      => 'Uchambuzi',
        'barcode'        => 'Msimbo pau',
        'blogs'          => 'Blogs',
        'chatter'        => 'Chatter',
        'contacts'       => 'Mawasiliano',
        'employees'      => 'Wafanyakazi',
        'fields'         => 'Maalum Sehemu',
        'full-calendar'  => 'Kalenda',
        'inventories'    => 'Ghala',
        'invoices'       => 'Ankara',
        'maintenance'    => 'Matengenezo',
        'manufacturing'  => 'Uzalishaji',
        'partners'       => 'Washirika',
        'payments'       => 'Malipo',
        'plugin-manager' => 'Programu-jalizi Meneja',
        'products'       => 'Bidhaa',
        'projects'       => 'Miradi',
        'purchases'      => 'Ununuzi',
        'recruitments'   => 'Ajira',
        'sales'          => 'Mauzo',
        'security'       => 'Usalama',
        'support'        => 'Support',
        'table-views'    => 'Jedwali Views',
        'time-off'       => 'Likizo',
        'timesheets'     => 'Karatasi za muda',
        'website'        => 'Tovuti',
    ],

    'summaries' => [
        'accounting'     => 'Manage chati ya akaunti, majarida, na financial maingizo',
        'accounts'       => 'Core akaunti management na financial mipangilio',
        'analytics'      => 'Ripoti na dashboards kwa business insights',
        'barcode'        => 'Msimbo pau shughuli app kwa ghala na uzalishaji',
        'blogs'          => 'Manage blogs',
        'chatter'        => 'Shughuli log, messaging, na follow-ups washa rekodi',
        'contacts'       => 'Mwasiliani management kwa wateja na wasambazaji',
        'employees'      => 'Wafanyakazi management',
        'fields'         => 'Ongeza maalum sehemu hadi resources',
        'full-calendar'  => 'Kalenda views na event scheduling',
        'inventories'    => 'Ghala na ghala management',
        'invoices'       => 'Ankara generation na management',
        'maintenance'    => 'Matengenezo management',
        'manufacturing'  => 'Uzalishaji na uzalishaji management',
        'partners'       => 'Manage business washirika',
        'payments'       => 'Manage malipo na transactions',
        'plugin-manager' => 'Programu-jalizi Meneja kwa DEKA Central',
        'products'       => 'Bidhaa catalog na variant management',
        'projects'       => 'Mradi planning na management',
        'purchases'      => 'Procurement na agizo la ununuzi management',
        'recruitments'   => 'Mwombaji tracking na hiring',
        'sales'          => 'Mauzo pipeline na opportunity management',
        'security'       => 'Majukumu, ruhusa, na access control',
        'support'        => 'Mteja support na ticketing',
        'table-views'    => 'Saved na customizable jedwali views',
        'time-off'       => 'Likizo management na tracking',
        'timesheets'     => 'Mfanyakazi work saa tracking',
        'website'        => 'Tovuti kwa wateja',
    ],

];
