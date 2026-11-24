<?php

return [
    'title' => 'Печать и отправка',

    'modal' => [
        'title' => 'Предпросмотр счёта',

        'form' => [
            'partners'    => 'Клиент',
            'subject'     => 'Тема',
            'description' => 'Описание',
            'files'       => 'Вложение',
        ],

        'action' => [
            'submit' => [
                'title' => 'Отправить',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Счёт отправлен',
                'body'  => 'Счёт успешно отправлен.',
            ],
        ],
    ],
];
