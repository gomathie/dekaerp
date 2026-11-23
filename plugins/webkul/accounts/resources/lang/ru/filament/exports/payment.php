<?php

return [
    'columns' => [
        'date'            => 'Дата',
        'name'            => 'Название',
        'journal'         => 'Журнал',
        'payment-method'  => 'Оплата Method',
        'partner'         => 'Контрагент',
        'amount-currency' => 'Сумма Валюта',
        'amount'          => 'Сумма',
        'state'           => 'Состояние',
        'company'         => 'Компания',
    ],

    'notification' => [
        'completed' => 'Ваш оплата экспорт есть завершено и :count строка(s) exported.',
        'failed'    => ':count строка(s) не удалось до экспорт.',
    ],
];
