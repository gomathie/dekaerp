<?php

return [
    'title' => 'Печать & Send',

    'modal' => [
        'title' => 'Preview Счёт',

        'form' => [
            'partners'    => 'Клиент',
            'subject'     => 'Subject',
            'description' => 'Описание',
            'files'       => 'Вложение',
        ],

        'action' => [
            'submit' => [
                'title' => 'Send',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Счёт Отправлено',
                'body'  => 'Счёт есть отправлено successfully.',
            ],
        ],
    ],
];
