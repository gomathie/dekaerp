<?php

return [
    'setup' => [
        'title'        => 'Записать заметку',
        'submit-title' => 'Записать',

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
                    'title' => 'Заметка добавлена',
                    'body'  => 'Ваша заметка добавлена.',
                ],

                'error' => [
                    'title' => 'Ошибка добавления заметки',
                    'body'  => 'Не удалось добавить заметку',
                ],
            ],
        ],
    ],
];
