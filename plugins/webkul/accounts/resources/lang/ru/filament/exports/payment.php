<?php

return [
    'columns' => [
        'date'            => 'Дата',
        'name'            => 'Название',
        'journal'         => 'Журнал',
        'payment-method'  => 'Способ оплаты',
        'partner'         => 'Контакт',
        'amount-currency' => 'Сумма в валюте',
        'amount'          => 'Сумма',
        'state'           => 'Статус',
        'company'         => 'Компания',
    ],

    'notification' => [
        'completed' => 'Экспорт платежей завершён, экспортировано строк: :count.',
        'failed'    => 'Не удалось экспортировать строк: :count.',
    ],
];
