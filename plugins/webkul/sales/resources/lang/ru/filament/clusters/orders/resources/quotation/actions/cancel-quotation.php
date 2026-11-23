<?php

return [
    'title' => 'Отмена',
    'modal' => [
        'heading'     => 'Отмена Коммерческое предложение',
        'description' => 'вы sure вы want до отмена этот Коммерческое предложение?',
    ],

    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Send & Отмена',

            'notification' => [
                'cancelled' => [
                    'title' => 'Коммерческое предложение отменён',
                    'body'  => 'Коммерческое предложение есть отменён и эл. почта есть отправлено successfully.',
                ],
            ],
        ],

        'cancel' => [
            'title' => 'Отмена',

            'notification' => [
                'cancelled' => [
                    'title' => 'Коммерческое предложение отменён',
                    'body'  => 'Коммерческое предложение есть отменён successfully.',
                ],
            ],
        ],

        'close' => [
            'title' => 'Закрыть',
        ],
    ],

    'form' => [
        'fields' => [
            'partner'             => 'Контрагент',
            'subject'             => 'Subject',
            'subject-placeholder' => 'Subject',
            'subject-default'     => 'Коммерческое предложение :name есть отменён для Заказ на продажу #:id',
            'description'         => 'Описание',
            'description-default' => 'Dear <b>:partner_name</b>, <br/><br/>Мы would like до inform вы тот ваш Заказ на продажу <b>:name</b> есть отменён. Как результат, нет further charges применить до этот заказ. Если возврат обязательно, это быть processed в earliest convenience.<br/><br/>Следует вы есть любой questions или require further assistance, пожалуйста feel free до reach вне до us.',
        ],
    ],
];
