<?php

return [
    'columns' => [
        'number'           => 'Номер',
        'date'             => 'Дата',
        'account'          => 'Счёт',
        'partner'          => 'Контрагент',
        'label'            => 'Подпись',
        'reference'        => 'Ссылка',
        'journal'          => 'Журнал',
        'debit'            => 'Дебет',
        'credit'           => 'Кредит',
        'balance'          => 'Остаток',
        'currency'         => 'Валюта',
        'company'          => 'Компания',
        'status'           => 'Статус',
        'amount-currency'  => 'Сумма Валюта',
        'amount-residual'  => 'Сумма Residual',
        'reconciled'       => 'Reconciled',
        'due-date'         => 'Срок оплаты',
    ],

    'values' => [
        'yes' => 'Да',
        'no'  => 'Нет',
    ],

    'notification' => [
        'completed' => 'Ваш журнал позиция экспорт есть завершено и :count строка(s) exported.',
        'failed'    => ':count строка(s) не удалось до экспорт.',
    ],
];
