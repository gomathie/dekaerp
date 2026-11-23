<?php

return [
    'notification' => [
        'title' => 'Закупка Agreement обновлено',
        'body'  => 'закупка agreement есть обновлено successfully.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Подтвердить',

            'notification' => [
                'unable' => [
                    'title' => 'Не удалось до подтвердить закупка agreement',
                    'body'  => 'Добавить в наименее one товар строка до confirming этот закупка agreement.',
                ],
            ],
        ],

        'close' => [
            'label'        => 'Закрыть',
            'notification' => [
                'warning' => [
                    'title' => 'Не удалось до закрыть закупка agreement',
                    'body'  => 'Вы нельзя закрыть этот закупка agreement потому что некоторые related RFQs не в Завершён или Отменён статус.',
                ],
            ],
        ],

        'cancel' => [
            'label' => 'Отмена',
        ],

        'print' => [
            'label' => 'Печать',
        ],

        'delete' => [
            'notification' => [
                'title' => 'Закупка Agreement удалено',
                'body'  => 'закупка agreement есть удалено successfully.',
            ],
        ],
    ],
];
