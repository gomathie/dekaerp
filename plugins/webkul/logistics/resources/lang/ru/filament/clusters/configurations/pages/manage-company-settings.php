<?php

return [
    'navigation' => [
        'title' => 'Настройки',
    ],

    'title'      => 'Логистика Настройки',
    'subheading' => 'Настройки для :company. Логистика switched вкл. и выкл. за компания.',
    'no-company' => 'Выбрать компания первый: Логистика настройки kept за компания.',

    'sections' => [
        'status'                   => 'Статус',
        'delivery'                 => 'Proof доставка',
        'operations'               => 'Операции',
        'accounting'               => 'Учёт',
        'integrations'             => 'Telematics integrations',
        'integrations-description' => 'Connect GPS tracking providers до импорт ваш fleet и pull live транспорт data.',
    ],

    'status' => [
        'enabled'  => 'Логистика включено для этот компания',
        'disabled' => 'Логистика не включено для этот компания',
    ],

    'readiness' => [
        'label' => 'Readiness',
        'ok'    => 'Everything Логистика needs в place.',
    ],

    'fields' => [
        'require-pod-for-delivery'    => 'Require proof доставка до mark отправка delivered',
        'require-pod-photo'           => 'Require photo с proof доставка',
        'stop-link-ttl-hours'         => 'Остановка link validity',
        'stop-link-ttl-hours-help'    => 'Как long proof--доставка link stays usable после это issued. Shorter safer: link credential, и это travels в URL.',
        'stop-link-rate-limit'        => 'Остановка link requests за минута',
        'stop-link-rate-limit-help'   => 'За link, за минута. Отпуск blank для по умолчанию. Raise это только если ваш водители turned away.',
        'capture-driver-on-pod'       => 'Запись водитель вкл. proofs доставка',
        'capture-driver-on-pod-help'  => 'Takes водитель от остановка’s рейс, поэтому proof shows кто был в door.',
        'capture-recipient-id'        => 'Ask для получатель’s Ид.',
        'capture-recipient-id-help'   => 'Adds обязательно Ид. поле когда proof captured. Только collect этот где вы есть причина до.',
        'default-service-type'        => 'По умолчанию service тип',
        'capacity-check'              => 'Транспорт capacity чек',
        'overdue-grace-minutes'       => 'Просрочен после (минуты прошлые ожидается time)',
        'free-waiting-minutes'        => 'Free waiting time в остановка (минуты)',
        'free-waiting-minutes-helper' => 'Beyond этот, waiting-time charge suggested. Это никогда added автоматически.',
        'invoice-journal'             => 'Журнал для Счёт клиентуs',
        'bill-journal'                => 'Журнал для счёт поставщикаs',
        'default-expense-account'     => 'По умолчанию расход счёт',
        'expense-approval-required'   => 'Расходы need approval до billing',
        'telematics-provider'         => 'Telematics platform',
        'telematics-provider-helper'  => 'GPS tracking platform этот компания uses. Отпуск пусто если это не использовать one.',
        'telematics-server'           => 'Server URL',
        'telematics-server-helper'    => 'base URL ваш telematics server.',
        'telematics-node'             => 'Node',
        'telematics-node-helper'      => 'PILOT только. Отпуск в 1 unless ваш счёт uses another node.',
        'telematics-username'         => 'Username',
        'telematics-password'         => 'Пароль',
    ],

    'actions' => [
        'save' => 'Сохранить',

        'enable' => [
            'label'                     => 'Включить Логистика',
            'heading'                   => 'Включить Логистика для этот компания?',
            'description'               => 'Логистика menus и записи become доступно до пользователи этот компания кто есть Логистика права.',
            'description-with-problems' => 'Логистика можно быть включено, но некоторые features не work до эти фиксированный: :problems',
            'notification'              => 'Логистика включено',
        ],

        'disable' => [
            'label'        => 'Выключить Логистика',
            'heading'      => 'Выключить Логистика для этот компания?',
            'description'  => 'Логистика скрыто и нет новое записи можно быть создано для этот компания. Existing data kept, и enabling это снова restores access.',
            'notification' => 'Логистика выключено',
        ],

        'telematics-sync' => [
            'label'        => 'Sync PILOT fleet',
            'heading'      => 'Sync fleet от PILOT?',
            'description'  => 'Транспорт найдено в PILOT быть создано или обновлено в этот company\'s fleet. Existing транспорт тот не в PILOT не touched.',
            'notification' => ':created создано, :updated обновлено, :skipped skipped.',
            'error'        => 'PILOT sync не удалось',
        ],
    ],

    'notifications' => [
        'saved' => 'Настройки saved',
    ],
];
