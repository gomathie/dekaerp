<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Отгул и отпуск создано',
            'body'  => 'отгул и отпуск есть создано successfully.',
        ],

        'overlap' => [
            'title' => 'Overlapping Отпуск Request',
            'body'  => 'выбрано отпуск dates overlap с existing request. Пожалуйста выбрать different dates.',
        ],

        'warning' => [
            'title' => 'Вы don\'t есть сотрудник счёт',
            'body'  => 'Вы don\'t есть сотрудник счёт. Пожалуйста контакт ваш администратор.',
        ],

        'invalid_half_day_leave' => [
            'title' => 'Недействительно Отпуск Request',
            'body'  => 'Half-день отпуск можно только быть применено для single день.',
        ],

        'leave_request_denied_no_allocation' => [
            'title' => 'Отпуск Request Отказано',
            'body'  => 'Вы не есть любой allocated отпуск для :leaveType.',
        ],

        'leave_request_denied_insufficient_balance' => [
            'title' => 'Отпуск Request Отказано',
            'body'  => 'Insufficient отпуск остаток. Вы есть :available_balance день(s) доступно. Requested: :requested_days день(s).',
        ],
    ],
];
