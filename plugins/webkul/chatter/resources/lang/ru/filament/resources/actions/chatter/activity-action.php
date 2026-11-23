<?php

return [
    'setup' => [
        'title'               => 'График Активность',
        'submit-action-title' => 'График',

        'form' => [
            'fields' => [
                'activity-plan' => 'Активность Plan',
                'plan-date'     => 'Plan Дата',
                'plan-summary'  => 'Plan Сводка',
                'activity-type' => 'Активность Тип',
                'due-date'      => 'Срок оплаты',
                'summary'       => 'Сводка',
                'assigned-to'   => 'Назначен До',
                'log-note'      => 'Log заметка',
                'body'          => 'Тип ваш сообщение здесь',
            ],
        ],

        'actions' => [
            'notification' => [
                'success' => [
                    'title' => 'Активность Создано',
                    'body'  => 'активность есть создано.',
                ],

                'warning'  => [
                    'title' => 'Нет новое файлы',
                    'body'  => 'Все файлы есть уже uploaded.',
                ],

                'error' => [
                    'title' => 'Активность creation не удалось',
                    'body'  => 'Не удалось до создать активность ',
                ],
            ],
        ],
    ],
];
