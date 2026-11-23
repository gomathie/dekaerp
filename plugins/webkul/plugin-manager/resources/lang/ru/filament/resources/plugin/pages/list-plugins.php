<?php

return [
    'navigation' => [
        'title' => 'Модули',
    ],

    'tabs' => [
        'apps'          => 'Apps',
        'extra'         => 'Extra',
        'installed'     => 'Installed',
        'not-installed' => 'Не Installed',
    ],

    'header-actions' => [
        'sync' => [
            'label'                     => 'Sync Доступно Модули',
            'modal-heading'             => 'Sync Модули',
            'modal-description'         => 'Этот scan и регистрация любой новое модули найдено.',
            'modal-submit-action-label' => 'Sync Модули',

            'notification' => [
                'success' => [
                    'title' => 'Модули Synced Successfully',
                    'body'  => 'Найдено и synced :count новое модуль(s).',
                ],

                'error' => [
                    'title' => 'Модуль Sync Не удалось',
                    'body'  => 'ошибка (:error) occurred пока syncing модули. Пожалуйста попробуйте снова.',
                ],
            ],
        ],
    ],
];
