<?php

return [
    'columns' => [
        'number'           => 'Номер',
        'date'             => 'Дата',
        'account'          => 'Счёт',
        'partner'          => 'Контакт',
        'label'            => 'Метка',
        'reference'        => 'Ссылка',
        'journal'          => 'Журнал',
        'debit'            => 'Дебет',
        'credit'           => 'Кредит',
        'balance'          => 'Сальдо',
        'currency'         => 'Валюта',
        'company'          => 'Компания',
        'status'           => 'Статус',
        'amount-currency'  => 'Сумма в валюте',
        'amount-residual'  => 'Остаток',
        'reconciled'       => 'Сверено',
        'due-date'         => 'Срок оплаты',
    ],

    'values' => [
        'yes' => 'Да',
        'no'  => 'Нет',
    ],

    'notification' => [
        'completed' => 'Экспорт строк проводок завершён, выгружено строк: :count.',
        'failed'    => 'Не удалось выгрузить строк: :count.',
    ],
];
