<?php

return [
    'actions' => [
        'clock-in'  => 'Clock в',
        'clock-out' => 'Clock вне',
        'correct'   => 'Correct посещаемость',
        'export'    => 'Экспорт посещаемость',
    ],
    'capture' => [
        'off'      => 'Выкл.',
        'optional' => 'Необязательно',
        'required' => 'Обязательно',
    ],
    'exceptions' => [
        'not-enabled' => 'Посещаемость не включено для компания :company.',
    ],
    'notifications' => [
        'failed' => 'Тот не work. изменить был не saved.',
    ],
    'statuses' => [
        'clocked-in'     => 'Clocked в since :time',
        'not-clocked-in' => 'Не clocked в',
        'on-leave'       => 'Вкл. отпуск',
    ],
    'validation' => [
        'check-out-order'     => 'Чек-вне есть до быть после чек-в.',
        'location-required'   => 'место обязательно до clock в или вне.',
        'no-employee'         => 'Вы не есть сотрудник запись в этот компания.',
        'not-allowed'         => 'Вы можно только correct посещаемость для ваш собственную команда.',
        'not-clocked-in'      => 'Вы не clocked в.',
        'not-your-attendance' => 'Вы можно только clock yourself в или вне.',
        'reason-required'     => 'correction needs причина.',
    ],
];
