<?php

return [
    'title' => 'Dispatch board',

    'tabs' => [
        'unassigned'      => 'Не назначен',
        'awaiting-pickup' => 'Awaiting pickup',
        'active'          => 'Активен рейсы',
        'due-today'       => 'Due сегодня',
        'overdue'         => 'Просрочен',
        'failed'          => 'Не удалось',
    ],

    'columns' => [
        'number'               => 'Отправка',
        'state'                => 'Статус',
        'customer'             => 'Клиент',
        'destination'          => 'Назначение',
        'expected-delivery-at' => 'Ожидается доставка',
    ],
];
