<?php

return [
    'setup' => [
        'title'        => 'Send Сообщение',
        'submit-title' => 'Send',

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
                    'title' => 'Сообщение отправлено',
                    'body'  => 'Ваш сообщение есть send successfully.',
                ],

                'error' => [
                    'title' => 'Сообщение отправлено ошибка',
                    'body'  => 'Не удалось до send ваш сообщение',
                ],
            ],

            'mail' => [
                'subject' => ':record_name',
            ],
        ],
    ],
];
