<?php

return [
    'columns' => [
        'invoice-date' => 'Дата счёта',
        'date'         => 'Дата',
        'number'       => 'Номер',
        'partner'      => 'Контакт',
        'reference'    => 'Ссылка',
        'journal'      => 'Журнал',
        'company'      => 'Компания',
        'total'        => 'Итого',
        'state'        => 'Статус',
        'checked'      => 'Проверено',
    ],

    'values' => [
        'yes' => 'Да',
        'no'  => 'Нет',
    ],

    'notification' => [
        'completed' => 'Экспорт проводок завершён, выгружено строк: :count.',
        'failed'    => 'Не удалось выгрузить строк: :count.',
    ],
];
