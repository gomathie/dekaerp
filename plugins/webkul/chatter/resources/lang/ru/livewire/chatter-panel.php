<?php

return [
    'heading' => 'Chatter',

    'placeholders' => [
        'no-record-found' => 'Нет запись найдено.',
        'loading'         => 'Загрузка Chatter...',
    ],

    'activity-infolist' => [
        'title' => 'Деятельность',
    ],

    'cancel-activity-plan-action' => [
        'title' => 'Отмена Активность',
    ],

    'delete-message-action' => [
        'title' => 'Удалить Сообщение',
    ],

    'edit-activity' => [
        'title' => 'Изменить Активность',

        'form' => [
            'fields' => [
                'activity-plan' => 'Активность Plan',
                'plan-date'     => 'Plan Дата',
                'plan-summary'  => 'Plan Сводка',
                'activity-type' => 'Активность Тип',
                'due-date'      => 'Срок оплаты',
                'summary'       => 'Сводка',
                'assigned-to'   => 'Назначен До',
            ],
        ],

        'action' => [
            'notification' => [
                'success' => [
                    'title' => 'Активность обновлено',
                    'body'  => 'активность есть обновлено successfully.',
                ],
            ],
        ],
    ],

    'process-message' => [
        'original-note' => '<br><div><span class="font-bold">Original Заметка</span>: :body</div>',
        'original-note' => '<br><div><span class="font-bold">Original Заметка</span>: :body</div>',
        'feedback'      => '<div><span class="font-bold">Feedback</span>: <p>:feedback</p></div>',
    ],

    'mark-as-done' => [
        'title'   => 'Mark как завершён',
        'actions' => [
            'done' => [
                'label' => 'Завершён',
            ],
        ],
        'form'  => [
            'fields' => [
                'feedback' => 'Feedback',
            ],
        ],

        'footer-actions' => [
            'label' => 'Завершён & График Далее',

            'actions' => [
                'notification' => [
                    'mark-as-done' => [
                        'title' => 'Активность mark как завершён',
                        'body'  => 'активность mark как завершён successfully.',
                    ],
                ],
            ],
        ],
    ],
];
