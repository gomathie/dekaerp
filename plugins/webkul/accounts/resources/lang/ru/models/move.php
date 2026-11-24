<?php

return [
    'title'          => 'Документ',

    'titles'         => [
        'invoice'     => 'Счёт',
        'credit-note' => 'Кредит-нота',
        'bill'        => 'Счёт поставщика',
        'refund'      => 'Возврат',
    ],

    'log-attributes' => [
        'name'                  => 'Ссылка на счёт',
        'reference'             => 'Ссылка',
        'date'                  => 'Дата счёта',
        'state'                 => 'Статус счёта',
        'move-type'             => 'Тип документа',
        'checked'               => 'Проверен',
        'payment-reference'     => 'Назначение платежа',
        'payment-state'         => 'Статус оплаты',
        'amount-untaxed'        => 'Подытог',
        'invoice-source-email'  => 'Эл. почта источника',
        'is-move-sent'          => 'Документ отправлен',
        'invoice-origin'        => 'Источник счёта',
        'currency'              => 'Валюта',
        'partner'               => 'Контакт',
        'partner-bank'          => 'Банк контакта',
        'invoice-user'          => 'Ответственный за счёт',
        'fiscal-position'       => 'Налоговая позиция',
        'invoice-payment-term'  => 'Условие оплаты счёта',
        'invoice-cash-rounding' => 'Округление наличных в счёте',
    ],
];
