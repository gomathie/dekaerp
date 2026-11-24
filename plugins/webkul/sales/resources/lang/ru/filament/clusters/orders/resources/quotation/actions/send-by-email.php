<?php

return [
    'title'        => 'Отправить по эл. почте',
    'resend-title' => 'Отправить повторно',
    'quotation'    => 'предложение',
    'quotations'   => 'предложения',

    'modal' => [
        'heading' => 'Отправить предложение по эл. почте',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Контакты',
            'subject'     => 'Тема',
            'description' => 'Описание',
            'attachment'  => 'Вложение',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Получатели не выбраны',
                    'body'  => 'Выберите хотя бы один контакт для отправки предложений.',
                ],

                'all_success' => [
                    'title' => 'Предложения отправлены!',
                    'body'  => 'Успешно отправлено (:plural): :recipients',
                ],

                'all_failed' => [
                    'title' => 'Не удалось отправить предложения',
                    'body'  => 'При отправке предложений возникли проблемы: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Часть предложений отправлена',
                    'sent_part'   => 'Успешно доставлено: :recipients',
                    'failed_part' => 'Не удалось доставить: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
