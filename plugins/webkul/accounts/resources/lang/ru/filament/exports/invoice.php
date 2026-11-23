<?php

return [
    'columns' => [
        'number'           => 'Номер',
        'state'            => 'Состояние',
        'customer'         => 'Клиент',
        'invoice-date'     => 'Дата счёта',
        'due-date'         => 'Срок оплаты',
        'tax-excluded'     => 'Налог Исключено',
        'tax'              => 'Налог',
        'total'            => 'Итого',
        'amount-due'       => 'Сумма Due',
        'payment-state'    => 'Оплата Состояние',
        'checked'          => 'Checked',
        'accounting-date'  => 'Учёт Дата',
        'source-document'  => 'Источник Документ',
        'reference'        => 'Ссылка',
        'sales-person'     => 'Продажи Person',
        'invoice-currency' => 'Счёт Валюта',
    ],

    'values' => [
        'yes' => 'Да',
        'no'  => 'Нет',
    ],

    'notification' => [
        'completed' => 'Ваш счёт экспорт есть завершено и :count строка(s) exported.',
        'failed'    => ':count строка(s) не удалось до экспорт.',
    ],
];
