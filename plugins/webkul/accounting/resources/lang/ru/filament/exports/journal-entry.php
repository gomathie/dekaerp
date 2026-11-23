<?php

return [
    'columns' => [
        'invoice-date' => 'Дата счёта',
        'date'         => 'Дата',
        'number'       => 'Номер',
        'partner'      => 'Контрагент',
        'reference'    => 'Ссылка',
        'journal'      => 'Журнал',
        'company'      => 'Компания',
        'total'        => 'Итого',
        'state'        => 'Состояние',
        'checked'      => 'Checked',
    ],

    'values' => [
        'yes' => 'Да',
        'no'  => 'Нет',
    ],

    'notification' => [
        'completed' => 'Ваш проводка экспорт есть завершено и :count строка(s) exported.',
        'failed'    => ':count строка(s) не удалось до экспорт.',
    ],
];
