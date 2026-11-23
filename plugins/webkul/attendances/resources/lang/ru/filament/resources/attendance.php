<?php

return [
    'model' => [
        'label'  => 'Посещаемость запись',
        'plural' => 'Посещаемость',
    ],
    'navigation' => [
        'title' => 'Посещаемость',
    ],
    'table' => [
        'columns' => [
            'employee'   => 'Сотрудник',
            'check-in'   => 'Clock в',
            'check-out'  => 'Clock вне',
            'worked'     => 'Worked',
            'review'     => 'Needs review',
            'on-leave'   => 'Вкл. отпуск',
            'source'     => 'Источник в',
            'source-out' => 'Источник вне',
            'company'    => 'Компания',
            'note'       => 'Заметка',
            'latitude'   => 'Latitude (в)',
            'longitude'  => 'Longitude (в)',
            'ip'         => 'IP адрес (в)',
        ],
        'filters' => [
            'open'         => 'Clocked в сейчас',
            'needs-review' => 'Needs review',
            'employee'     => 'Сотрудник',
            'day'          => 'День',
            'on'           => 'Вкл.',
        ],
    ],
    'actions' => [
        'export'  => 'Экспорт',
        'correct' => [
            'label'        => 'Correct посещаемость',
            'heading'      => 'Correct этот посещаемость',
            'description'  => 'изменить recorded с ваш название и причина вы give.',
            'fields'       => [
                'check-in'    => 'Clock в',
                'check-out'   => 'Clock вне',
                'note'        => 'Заметка',
                'reason'      => 'Причина',
                'reason-help' => 'Обязательно. Say что был wrong и что вы изменено.',
            ],
            'notification' => 'Посещаемость corrected.',
        ],
    ],
    'export-complete' => ':count посещаемость строка(s) exported.',
];
