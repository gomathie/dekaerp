<?php

return [
    'heading' => 'Обсуждение',

    'placeholders' => [
        'no-record-found' => 'Запись не найдена.',
        'loading'         => 'Загрузка обсуждения...',
    ],

    'activity-infolist' => [
        'title' => 'Действия',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'Отменить действие',
    ],

    'delete-message-action' => [
        'title' => 'Удалить сообщение',
    ],

    'edit-activity' => [
        'title' => 'Изменить действие',

        'form' => [
            'fields' => [
                'activity-plan' => 'План действий',
                'plan-date'     => 'Дата плана',
                'plan-summary'  => 'Сводка плана',
                'activity-type' => 'Тип действия',
                'due-date'      => 'Срок',
                'summary'       => 'Сводка',
                'assigned-to'   => 'Ответственный',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Действие обновлено',
                    'body'  => 'Действие обновлено.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Исходная заметка</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">Отзыв</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title'   => 'Отметить выполненным',
        'actions' => [
            'done' => [
                'label' => 'Готово',
            ],
        ],
        'form'  => [
            'fields' => [
                'feedback' => 'Отзыв',
            ],
        ],

        'footer-actions' => [
            'label' => 'Готово и запланировать следующее',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Действие выполнено',
                        'body'  => 'Действие отмечено как выполненное.',
                    ],
                ],
            ],
        ],
    ],
];
