    <?php

    return [
        'setup' => [
            'title'               => 'Followers',
            'submit-action-title' => 'Добавить Follower',
            'tooltip'             => 'Добавить Follower',

            'form' => [
                'fields' => [
                    'recipients'  => 'Recipients',
                    'notify-user' => 'Notify Пользователь',
                    'add-a-note'  => 'Добавить заметка',
                ],
            ],

            'actions' => [
                'notification' => [
                    'success' => [
                        'title' => 'Follower Added',
                        'body'  => 'Follower есть added Successfully.',
                    ],

                    'partial_message' => [
                        'title'    => 'Сообщение отправлено с notice',
                        'single'   => ':count follower был не notified due до отсутствует эл. почта: :names',
                        'multiple' => ':count followers были не notified due до отсутствует emails: :names',
                    ],

                    'error' => [
                        'title' => 'Follower added ошибка',
                        'body'  => 'Не удалось до добавить ":partner" как follower',
                    ],
                ],

                'mail' => [
                    'subject' => 'Приглашение до follow :model: :department',
                ],
            ],
        ],
    ];
