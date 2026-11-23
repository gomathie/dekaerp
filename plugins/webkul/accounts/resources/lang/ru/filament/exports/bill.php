<?php

return [
    'columns' => [
        'number'          => 'Номер',
        'state'           => 'Состояние',
        'customer'        => 'Клиент',
        'bill-date'       => 'Счёт поставщика Дата',
        'due-date'        => 'Срок оплаты',
        'tax-excluded'    => 'Налог Исключено',
        'tax'             => 'Налог',
        'total'           => 'Итого',
        'amount-due'      => 'Сумма Due',
        'payment-state'   => 'Оплата Состояние',
        'checked'         => 'Checked',
        'accounting-date' => 'Учёт Дата',
        'source-document' => 'Источник Документ',
        'reference'       => 'Ссылка',
        'sales-person'    => 'Продажи Person',
        'bill-currency'   => 'Счёт поставщика Валюта',
    ],

    'values' => [
        'yes' => 'Да',
        'no'  => 'Нет',
    ],

    'notification' => [
        'completed' => 'Ваш счёт поставщика экспорт есть завершено и :count строка(s) exported.',
        'failed'    => ':count строка(s) не удалось до экспорт.',
    ],
];
