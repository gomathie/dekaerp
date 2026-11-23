<?php

return [
    'statement-name' => 'Выписка :from до :to',
    'entry-label'    => 'Банк выписка строка',

    'fields' => [
        'date'   => 'дата',
        'amount' => 'сумма',
    ],

    'errors' => [
        'not-allowed'            => 'Вы нельзя сверить этот банковский счёт.',
        'not-a-bank-journal'     => 'Выбрать банк или касса журнал.',
        'foreign-currency'       => 'Этот журнал uses foreign валюта. Только банковский счётs в компания валюта можно быть reconciled для сейчас.',
        'missing-bank-account'   => 'Этот журнал есть нет банковский счёт задать. Задать его по умолчанию счёт первый.',
        'empty-file'             => 'файл есть нет выписка строки.',
        'too-many-rows'          => 'файл есть больше чем :max строки. Split это в smaller statements.',
        'bad-row'                => 'Строка :row: :field нельзя быть чтение. Чек колонка numbers и дата формат.',
        'already-reconciled'     => 'Этот строка уже reconciled.',
        'payment-does-not-match' => 'Тот оплата не совпадение этот строка: это нужно быть в тот же банк журнал, для тот же сумма и direction, и не уже matched.',
        'account-not-allowed'    => 'Тот счёт нельзя быть используется здесь. Клиент и поставщик счета settled от matching оплата.',
    ],
];
