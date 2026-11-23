<?php

return [
    'breadcrumb' => 'Branding',
    'title'      => 'Branding',
    'group'      => 'Общее',

    'navigation' => [
        'label' => 'Branding',
    ],

    'form' => [
        'sections' => [
            'logo' => [
                'title'       => 'Логотип & Favicon',
                'description' => 'Override logos, favicon и логотип высота используется по администратор и клиент panels. Отпуск поле пусто до оставить по умолчанию.',
            ],
            'colors' => [
                'title'       => 'Colors',
                'description' => 'Override тема colors используется по администратор и клиент panels. Отпуск цвет пусто до оставить по умолчанию.',
            ],
        ],
        'fields' => [
            'light-logo'         => 'Светлая Логотип',
            'light-logo-helper'  => 'Shown вкл. светлая backgrounds. Replaces по умолчанию логотип.',
            'dark-logo'          => 'Тёмная Логотип',
            'dark-logo-helper'   => 'Shown когда тёмная mode включено.',
            'favicon'            => 'Favicon',
            'favicon-helper'     => 'Browser вкладка значок.',
            'logo-height'        => 'Логотип Высота',
            'logo-height-helper' => 'CSS высота значение, e.g. 2rem или 40px.',
            'primary-color'      => 'Primary',
            'gray-color'         => 'Gray',
            'danger-color'       => 'Danger',
            'info-color'         => 'Info',
            'success-color'      => 'Успешно',
            'warning-color'      => 'Предупреждение',
        ],
    ],

    'actions' => [
        'reset' => [
            'label' => 'Сбросить до по умолчанию',
        ],
    ],
];
