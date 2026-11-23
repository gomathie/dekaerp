<?php

return [
    'navigation' => [
        'label' => 'Справка',
    ],

    'title'      => 'Справка',
    'heading'    => 'Справка & Resources',
    'subheading' => 'Everything вы need до получить большинство вне :app.',

    'guides' => [
        'group' => 'Руководства',

        'superadmin' => [
            'title'       => 'Суперадминистратор Руководство',
            'description' => 'Administer :app installation itself: модули, компании, администратор роли, upgrades и обслуживание.',
            'button'      => 'Открыт Суперадминистратор Руководство',
        ],

        'admin' => [
            'title'       => 'Администратор Руководство',
            'description' => 'Задать вверх :app для ваш компания, teams, модули и controls до ежедневно work begins.',
            'button'      => 'Администратор setup checklist',
            'sections'    => [
                [
                    'title' => 'Задать вверх компания',
                    'items' => [
                        '0' => 'Создать или review компании, склады, валюты, налоги и финансовый настройки.',
                        '1' => 'Назначить пользователи до компании они можно access и подтвердить активен компания switcher.',
                        '2' => 'Configure sequences, условия оплаты, товары, единицы measure и документ шаблоны до transactions begin.',
                    ],
                ],
                [
                    'title' => 'Control access',
                    'items' => [
                        '0' => 'Создать роли для финансы, продажи, склад, Кадры, проект и management teams.',
                        '1' => 'Grant module права только до пользователи кто need их.',
                        '2' => 'Review свой поля, imports, exports и API tokens до giving broad access.',
                    ],
                ],
                [
                    'title' => 'Operate и maintain',
                    'items' => [
                        '0' => 'Install только модули business uses, затем скрыть или убрать unused модули.',
                        '1' => 'Monitor discussions, вложения, approvals и запись ownership в течение rollout.',
                        '2' => 'Оставить эл. почта, PDF, backup и обновить настройки aligned с deployment policy.',
                    ],
                ],
            ],
        ],

        'user' => [
            'title'          => 'Пользователь Руководство',
            'description'    => 'Использовать built-в пользователь руководство для DEKA walkthroughs и module-ссылка страницы для ежедневно work в :app.',
            'button'         => 'Открыт Пользователь Руководство',
            'full_heading'   => 'Полный Пользователь Руководство',
            'count'          => '{0} Нет страниц руководства|{1} 1 страница руководства|[2,*] :count страниц руководства',
            'category_count' => '{0} Нет страниц|{1} 1 страница|[2,*] :count страниц',
            'callouts'       => [
                'tip'       => 'Tip',
                'note'      => 'Заметка',
                'important' => 'Important',
            ],
            'sections'    => [
                [
                    'title' => 'Getting started',
                    'items' => [
                        '0' => 'Platform Обзор & Navigation',
                        '1' => 'Multi-Компания Setup & Switcher',
                    ],
                ],
                [
                    'title' => 'Core workflows',
                    'items' => [
                        '0' => 'Sell: Продажи & Invoicing',
                        '1' => 'Buy: Purchasing & Учёт',
                        '2' => 'Сделать & Переместить: Склад & Рабочая среда',
                        '3' => 'Run: Проекты, Кадры & Обслуживание',
                        '4' => 'Платформа, API и свой сервер',
                    ],
                ],
                [
                    'title' => 'Module ссылка: 12 модули / 69 страницы',
                    'items' => [
                        '0' => 'Счета, Продажи, Закупка, Склад, Производство, Обслуживание',
                        '1' => 'Контакты, Проект, Сотрудники, Подбор, Отгул и отпуск',
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'group' => 'Services',

        'cloud' => [
            'title'       => 'Cloud Hosting',
            'description' => 'Стоимость-effective, managed cloud hosting — deploy ваш ERP в минуты, fully optimised, secure и scalable.',
            'button'      => 'See Pricing',
        ],

        'support' => [
            'title'       => 'Support & Обслуживание',
            'description' => 'Dedicated technical support и ongoing обслуживание plans до оставить ваш ERP secure, обновлено и running smoothly.',
            'button'      => 'Просмотр Подробности',
        ],

        'paid' => [
            'title'       => 'Оплачен Services',
            'description' => 'Expert справка для module development, customisation, data migration, версия upgrades и bespoke integrations.',
            'button'      => 'Получить Started',
        ],
    ],

    'resources' => [
        'group' => 'Resources & Documentation',

        'extensions' => [
            'title'       => 'Модули',
            'description' => 'Browse official и community добавить-ons до extend :app с новое модули и features.',
            'button'      => 'Browse Модули',
        ],

        'docs' => [
            'title'       => 'Dev Docs',
            'description' => 'Developer руководства и references до справка вы build модули, customise модули и extend platform.',
            'button'      => 'Чтение Docs',
        ],

        'guide' => [
            'title'       => 'Пользователь Руководство',
            'description' => 'Step-от-step руководства covering setup, конфигурация и everyday использовать каждый ERP module.',
            'button'      => 'Открыт Руководство',
        ],

        'website' => [
            'title'       => 'Товар Site',
            'description' => 'Module overviews, выпуск заметки и everything :app можно, вкл. товар сайт.',
            'button'      => 'Visit Сайт',
        ],
    ],

    'empty' => 'Справка links есть не задать вверх для этот :app installation ещё. Контакт ваш система администратор для assistance.',

    'contact' => [
        'title'       => 'Всё ещё need вручную?',
        'description' => 'Talk до наш команда около hosting, implementation, свой development или anything иначе.',
        'live_chat'   => 'Live Chat',
        'button'      => 'Контакт Us',
    ],
];
