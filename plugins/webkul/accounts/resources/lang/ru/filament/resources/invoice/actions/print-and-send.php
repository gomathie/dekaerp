<?php

return [
    'title' => 'Печать и отправка',

    'modal' => [
        'title' => 'Предпросмотр счёта',

        'form' => [
            'partners'    => 'Клиент',
            'subject'     => 'Тема',
            'description' => 'Описание',
            'files'       => 'Вложение',
        ],

        'action' => [
            'submit' => [
                'title' => 'Отправить',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Счёт отправлен',
                'body'  => 'Счёт успешно отправлен.',
            ],
        ],
    ],

    'message' => [
        'subject'   => 'Счёт для :partner (номер :number)',
        'greeting'  => 'Уважаемый(ая) :name!',
        'body'      => 'Ваш счёт :number от :company на сумму :amount готов. Просим оплатить его в ближайшее время.',
        'bank'      => 'При оплате укажите номер :number для счёта :account.',
        'questions' => 'Если у вас есть вопросы, свяжитесь с нами.',
        'regards'   => 'С уважением,',
    ],
];
