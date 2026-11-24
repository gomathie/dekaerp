<?php

return [
    'columns' => [
        'number'           => 'Номер',
        'state'            => 'Статус',
        'customer'         => 'Клиент',
        'invoice-date'     => 'Дата счёта',
        'due-date'         => 'Срок оплаты',
        'tax-excluded'     => 'Без налога',
        'tax'              => 'Налог',
        'total'            => 'Итого',
        'amount-due'       => 'К оплате',
        'payment-state'    => 'Статус оплаты',
        'checked'          => 'Проверен',
        'accounting-date'  => 'Дата учёта',
        'source-document'  => 'Исходный документ',
        'reference'        => 'Ссылка',
        'sales-person'     => 'Менеджер по продажам',
        'invoice-currency' => 'Валюта счёта',
    ],

    'values' => [
        'yes' => 'Да',
        'no'  => 'Нет',
    ],

    'notification' => [
        'completed' => 'Экспорт счетов завершён, экспортировано строк: :count.',
        'failed'    => 'Не удалось экспортировать строк: :count.',
    ],
];
