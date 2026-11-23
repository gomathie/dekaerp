<?php

return [

    'title' => 'Модуль',

    'table' => [
        'version'             => 'Версия',
        'dependencies'        => 'Dependencies',
        'dependencies_suffix' => ' Dependencies',
    ],

    'status' => [
        'installed'     => 'Installed',
        'not_installed' => 'Не Installed',
    ],

    'filters' => [
        'installation_status' => 'Installation Статус',
        'all_plugins'         => 'Все Модули',
        'installed'           => 'Installed',
        'not_installed'       => 'Не Installed',
        'active_status'       => 'Активен Статус',
        'author'              => 'Author',
        'webkul'              => 'Webkul',
        'third_party'         => 'Third Party',
    ],

    'actions' => [
        'install' => [
            'title'       => 'Install',
            'heading'     => 'Install Модуль :name',
            'description' => "вы sure вы want до install ':name' модуль? Этот run migrations и seeders.",
            'submit'      => 'Install Модуль',
        ],

        'companies' => [
            'title'       => 'Компании',
            'heading'     => 'Который компании май использовать :name?',
            'description' => 'Installing модуль makes его код доступно до whole installation. Этот decides который компании май actually использовать это — grant это до компании whose subscription includes это. Removing компания hides модуль от everyone в это.',
            'field'       => 'Компании',

            'notification' => [
                'title' => 'Модуль availability обновлено',
                'body'  => ':name сейчас доступно до :count компания(ies).',
            ],
        ],
        'uninstall' => [
            'title'      => 'Uninstall',
            'heading'    => 'Uninstall Модуль',
            'submit'     => 'Uninstall Модуль',
        ],
    ],

    'notifications' => [
        'installed' => [
            'title' => 'Модуль Installed Successfully',
            'body'  => "':name' модуль есть installed.",
        ],
        'installing' => [
            'title' => 'Installing :name',
            'body'  => 'Этот takes минута или two. Вы получить уведомление когда это завершён, и можно оставить working meanwhile.',
        ],
        'installing-already' => [
            'title' => ':name уже installed',
        ],
        'installed-failed' => [
            'title' => 'Installation Не удалось',
        ],
        'uninstalled' => [
            'title' => 'Модуль Uninstalled Successfully',
            'body'  => "':name' модуль есть uninstalled.",
        ],

        'cache-rebuild-failed' => [
            'title' => 'Caches были cleared но не rebuilt',
            'body'  => 'модуль изменить itself finished. application сейчас running без cached config и маршруты, который slower но correct. Run "php artisan optimize" и чтение его output до find вне почему это не удалось; ошибка есть также reported.',
        ],
        'uninstalled-failed' => [
            'title' => 'Uninstallation Не удалось',
        ],
        'uninstalled-blocked' => [
            'title' => 'Нельзя Uninstall Модуль',
            'body'  => "':name' модуль есть installed dependents тот нужно быть uninstalled первый: :dependents.",
        ],
    ],

    'infolist' => [
        'section'  => [
            'plugin'       => ' Модуль Сведения',
            'dependencies' => 'Dependencies',
        ],
        'name'         => 'Модуль Название',
        'author'       => 'Author',
        'version'      => 'Версия',
        'dependencies' => 'Обязательно Модули',
        'dependents'   => 'Модули Тот Depend Вкл. Этот',
        'is_installed' => 'Installation Статус',
        'license'      => 'License',
        'summary'      => 'Описание',

        'dependencies-repeater' => [
            'title'        => 'Обязательно Модули',
            'name'         => 'Модуль Название',
            'is_installed' => 'Installed',
            'placeholder'  => 'Нет dependencies обязательно',
        ],

        'dependents-repeater' => [
            'title'        => 'Модули Тот Depend Вкл. Этот',
            'name'         => 'Модуль Название',
            'is_installed' => 'Installed',
            'placeholder'  => 'Нет dependents',
        ],

    ],

    'names' => [
        'accounting'     => 'Учёт',
        'accounts'       => 'Счета',
        'analytics'      => 'Аналитика',
        'barcode'        => 'Штрихкод',
        'blogs'          => 'Blogs',
        'chatter'        => 'Chatter',
        'contacts'       => 'Контакты',
        'employees'      => 'Сотрудники',
        'fields'         => 'Свой Поля',
        'full-calendar'  => 'Календарь',
        'inventories'    => 'Склад',
        'invoices'       => 'Счета',
        'maintenance'    => 'Обслуживание',
        'manufacturing'  => 'Производство',
        'partners'       => 'Контрагенты',
        'payments'       => 'Оплаты',
        'plugin-manager' => 'Модуль Руководитель',
        'products'       => 'Товары',
        'projects'       => 'Проекты',
        'purchases'      => 'Закупки',
        'recruitments'   => 'Подбор',
        'sales'          => 'Продажи',
        'security'       => 'Безопасность',
        'support'        => 'Support',
        'table-views'    => 'Таблица Views',
        'time-off'       => 'Отгул и отпуск',
        'timesheets'     => 'Табели',
        'website'        => 'Сайт',
    ],

    'summaries' => [
        'accounting'     => 'Manage план счетов, журналы, и financial проводки',
        'accounts'       => 'Core счёт management и financial настройки',
        'analytics'      => 'Отчёты и dashboards для business insights',
        'barcode'        => 'Штрихкод операции app для склад и производство',
        'blogs'          => 'Manage blogs',
        'chatter'        => 'Активность log, messaging, и follow-ups вкл. записи',
        'contacts'       => 'Контакт management для клиенты и поставщики',
        'employees'      => 'Сотрудники management',
        'fields'         => 'Добавить свой поля до resources',
        'full-calendar'  => 'Календарь views и event scheduling',
        'inventories'    => 'Склад и склад management',
        'invoices'       => 'Счёт generation и management',
        'maintenance'    => 'Обслуживание management',
        'manufacturing'  => 'Производство и рабочая среда management',
        'partners'       => 'Manage business контрагенты',
        'payments'       => 'Manage оплаты и transactions',
        'plugin-manager' => 'Модуль Руководитель для DEKA Central',
        'products'       => 'Товар catalog и variant management',
        'projects'       => 'Проект planning и management',
        'purchases'      => 'Procurement и заказ на закупку management',
        'recruitments'   => 'Кандидат tracking и hiring',
        'sales'          => 'Продажи pipeline и opportunity management',
        'security'       => 'Роли, права, и access control',
        'support'        => 'Клиент support и ticketing',
        'table-views'    => 'Saved и customizable таблица views',
        'time-off'       => 'Отпуск management и tracking',
        'timesheets'     => 'Сотрудник work час tracking',
        'website'        => 'Сайт для клиенты',
    ],

];
