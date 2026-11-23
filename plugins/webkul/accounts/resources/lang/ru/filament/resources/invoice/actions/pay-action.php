<?php

return [
    'title' => 'Pay',

    'paid-now' => [
        'label'   => 'Оплачен сейчас',
        'helper'  => 'Saving confirms счёт и записи полный оплата.',
        'invalid' => 'Этот way до pay нельзя быть используется для этот счёт.',
    ],

    'form' => [
        'more-options' => 'Больше options',
        'fields'       => [
            'paid-how'               => 'Как был это оплачен?',
            'new-way'                => 'Новое way до pay (e.g. Wave, Card)',
            'journal'                => 'Журнал',
            'amount'                 => 'Сумма',
            'currency'               => 'Валюта',
            'payment-method-line'    => 'Оплата Method Строка',
            'payment-date'           => 'Оплата Дата',
            'recipient-bank-account' => 'Получатель Банковский счёт',
            'communication'          => 'Memo',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title'      => 'Оплата Не удалось',
            'plain-body' => 'Этот оплата could не быть prepared. Наш команда есть notified. Пожалуйста попробуйте снова в moment, или контакт support.',
        ],
    ],
];
