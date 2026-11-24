    <?php

    return [
        'setup' => [
            'title'               => 'Подписчики',
            'submit-action-title' => 'Добавить подписчика',
            'tooltip'             => 'Добавить подписчика',

            'form' => [
                'fields' => [
                    'recipients'  => 'Получатели',
                    'notify-user' => 'Уведомить пользователя',
                    'add-a-note'  => 'Добавить примечание',
                ],
            ],

            'actions' => [
                'notification' => [
                    'success' => [
                        'title' => 'Подписчик добавлен',
                        'body'  => 'Подписчик добавлен.',
                    ],

                    'partial_message' => [
                        'title'    => 'Сообщение отправлено с замечанием',
                        'single'   => 'Не уведомлено подписчиков без эл. почты: :count (:names)',
                        'multiple' => 'Не уведомлено подписчиков без эл. почты: :count (:names)',
                    ],

                    'error' => [
                        'title' => 'Ошибка добавления подписчика',
                        'body'  => 'Не удалось добавить «:partner» в подписчики',
                    ],
                ],

                'mail' => [
                    'subject' => 'Приглашение следить за :model: :department',
                ],
            ],
        ],
    ];
