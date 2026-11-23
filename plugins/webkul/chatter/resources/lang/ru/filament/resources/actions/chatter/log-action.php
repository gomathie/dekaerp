<?php

return [
    'setup' => [
        'title'        => 'Log Заметка',
        'submit-title' => 'Log',

        'form' => [
            'fields' => [
                'hide-subject'            => 'Скрыть Subject',
                'add-subject'             => 'Добавить Subject',
                'subject'                 => 'Subject',
                'write-message-here'      => 'Запись ваш сообщение здесь',
                'attachments-helper-text' => 'Max файл размер: 10MB. Разрешено types: Images, PDF, Word, Excel, Text',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Log Заметка added',
                    'body'  => 'Ваш log заметка added successfully.',
                ],

                'error' => [
                    'title' => 'Log добавить ошибка',
                    'body'  => 'Не удалось до добавить ваш log заметка',
                ],
            ],
        ],
    ],
];
