<?php

return [
    'title'        => 'Send От Эл. почта',
    'resend-title' => 'Re-Send От Эл. почта',
    'quotation'    => 'коммерческое предложение',
    'quotations'   => 'коммерческие предложения',

    'modal' => [
        'heading' => 'Send Коммерческое предложение От Эл. почта',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Контрагенты',
            'subject'     => 'Subject',
            'description' => 'Описание',
            'attachment'  => 'Вложение',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Нет Recipients Выбрано',
                    'body'  => 'Пожалуйста выбрать в наименее one контрагент до send коммерческие предложения до.',
                ],

                'all_success' => [
                    'title' => 'Коммерческие предложения Отправлено!',
                    'body'  => 'Ваш :plural есть successfully delivered до: :recipients',
                ],

                'all_failed' => [
                    'title' => 'Не удалось до Send Коммерческие предложения',
                    'body'  => 'Мы encountered issues sending ваш коммерческие предложения: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Некоторые Коммерческие предложения Отправлено',
                    'sent_part'   => 'Successfully delivered до: :recipients',
                    'failed_part' => 'Could не deliver до: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
