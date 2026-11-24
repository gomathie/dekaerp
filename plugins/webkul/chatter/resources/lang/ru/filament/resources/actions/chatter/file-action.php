<?php

return [
    'setup' => [
        'title'   => 'Вложения',
        'tooltip' => 'Загрузить вложения',

        'modal-submit-action-label' => 'Загрузить',

        'form' => [
            'fields' => [
                'files'                  => 'Файлы',
                'attachment-helper-text' => 'Макс. размер файла: 10 МБ. Типы: изображения, PDF, Word, Excel, текст',

                'actions' => [
                    'delete' => [
                        'title' => 'Файл удалён',
                        'body'  => 'Файл удалён.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Вложения загружены',
                    'body'  => 'Вложения загружены.',
                ],

                'warning'  => [
                    'title' => 'Нет новых файлов',
                    'body'  => 'Все файлы уже загружены.',
                ],

                'error' => [
                    'title' => 'Ошибка загрузки вложений',
                    'body'  => 'Не удалось загрузить вложения ',
                ],
            ],
        ],
    ],
];
