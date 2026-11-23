<?php

return [
    'setup' => [
        'title'   => 'Вложения',
        'tooltip' => 'Загрузить Вложения',

        'modal-submit-action-label' => 'Загрузить',

        'form' => [
            'fields' => [
                'files'                  => 'Файлы',
                'attachment-helper-text' => 'Max файл размер: 10MB. Разрешено types: Images, PDF, Word, Excel, Text',

                'actions' => [
                    'delete' => [
                        'title' => 'Файл удалено',
                        'body'  => 'Файл есть удалено successfully.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Вложения Uploaded',
                    'body'  => 'Вложения uploaded successfully.',
                ],

                'warning'  => [
                    'title' => 'Нет новое файлы',
                    'body'  => 'Все файлы есть уже uploaded.',
                ],

                'error' => [
                    'title' => 'Вложение загрузить ошибка',
                    'body'  => 'Не удалось до загрузить вложения ',
                ],
            ],
        ],
    ],
];
