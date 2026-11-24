<?php

return [
    'setup' => [
        'title'               => 'Запланировать действие',
        'submit-action-title' => 'Запланировать',

        'form' => [
            'fields' => [
                'activity-plan' => 'План действий',
                'plan-date'     => 'Дата плана',
                'plan-summary'  => 'Сводка плана',
                'activity-type' => 'Тип действия',
                'due-date'      => 'Срок',
                'summary'       => 'Сводка',
                'assigned-to'   => 'Ответственный',
                'log-note'      => 'Записать заметку',
                'body'          => 'Введите сообщение',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Действие создано',
                    'body'  => 'Действие создано.',
                ],

                'warning'  => [
                    'title' => 'Нет новых файлов',
                    'body'  => 'Все файлы уже загружены.',
                ],

                'error' => [
                    'title' => 'Не удалось создать действие',
                    'body'  => 'Не удалось создать действие ',
                ],
            ],
        ],
    ],
];
