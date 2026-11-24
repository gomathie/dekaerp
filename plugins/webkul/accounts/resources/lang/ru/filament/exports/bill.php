<?php

return [
    'columns' => [
        'number'          => 'Номер',
        'state'           => 'Статус',
        'customer'        => 'Клиент',
        'bill-date'       => 'Дата счёта',
        'due-date'        => 'Срок оплаты',
        'tax-excluded'    => 'Без налога',
        'tax'             => 'Налог',
        'total'           => 'Итого',
        'amount-due'      => 'К оплате',
        'payment-state'   => 'Статус оплаты',
        'checked'         => 'Проверен',
        'accounting-date' => 'Дата учёта',
        'source-document' => 'Исходный документ',
        'reference'       => 'Ссылка',
        'sales-person'    => 'Менеджер по продажам',
        'bill-currency'   => 'Валюта счёта',
    ],

    'values' => [
        'yes' => 'Да',
        'no'  => 'Нет',
    ],

    'notification' => [
        'completed' => 'Экспорт счетов поставщиков завершён, экспортировано строк: :count.',
        'failed'    => 'Не удалось экспортировать строк: :count.',
    ],
];
