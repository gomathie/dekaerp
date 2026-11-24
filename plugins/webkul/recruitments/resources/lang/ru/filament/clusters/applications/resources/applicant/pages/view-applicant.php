<?php

return [
    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Соискатель удалён',
                'body'  => 'Соискатель успешно удалён.',
            ],
        ],

        'refuse' => [
            'title'        => 'Причина отказа',
            'form'         => [
                'fields' => [
                    'notify'   => 'Уведомить',
                    'email-to' => 'Кому',
                ],
            ],
            'notification' => [
                'title' => 'Соискателю отказано',
                'body'  => 'Соискателю успешно отказано.',
            ],
        ],

        'reopen' => [
            'title'        => 'Отменить отказ соискателю',
            'notification' => [
                'title' => 'Соискатель возвращён',
                'body'  => 'Соискатель успешно возвращён в работу.',
            ],
        ],

        'state' => [
            'notification' => [
                'title' => 'Статус соискателя обновлён',
                'body'  => 'Статус соискателя успешно обновлён.',
            ],
        ],
    ],

    'mail' => [
        'application-refused' => [
            'subject' => 'Ваша заявка на вакансию: :application',
        ],
    ],
];
