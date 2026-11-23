<?php

return [
    'tooltip' => 'Фильтры',

    'fields'  => [
        'search'             => 'Поиск',
        'search-placeholder' => 'Поиск сообщения...',
        'type'               => 'Тип',
        'date'               => 'Дата',
        'sort-by'            => 'Sort от',
        'pinned-only'        => 'Pinned только',
    ],
    'type-options' => [
        'all'          => 'Все types',
        'note'         => 'Заметки',
        'comment'      => 'Комментарии',
        'notification' => 'Уведомления',
        'activity'     => 'Деятельность',
    ],
    'date-options' => [
        ''          => 'Любой time',
        'today'     => 'Сегодня',
        'yesterday' => 'Вчера',
        'week'      => 'Последний 7 дни',
        'month'     => 'Последний 30 дни',
        'quarter'   => 'Последний 3 months',
        'year'      => 'Последний год',
    ],
    'sort-options' => [
        'created_at_desc' => 'Newest первый',
        'created_at_asc'  => 'Oldest первый',
        'updated_at_desc' => 'Recently обновлено',
        'priority'        => 'Приоритет',
    ],
    'actions' => [
        'apply' => 'Применить фильтры',
    ],
];
