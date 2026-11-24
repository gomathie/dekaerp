<?php

return [
    'setup' => [
        'title'        => 'Отправить сообщение',
        'submit-title' => 'Отправить',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Скрыть тему',
                'add-subject'             => 'Добавить тему',
                'subject'                 => 'Тема',
                'write-message-here'      => 'Введите сообщение',
                'attachments-helper-text' => 'Макс. размер файла: 10 МБ. Типы: изображения, PDF, Word, Excel, текст',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Сообщение отправлено',
                    'body'  => 'Ваше сообщение отправлено.',
                ],

                'error' => [
                    'title' => 'Ошибка отправки сообщения',
                    'body'  => 'Не удалось отправить сообщение',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
