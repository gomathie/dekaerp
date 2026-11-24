<?php

return [
    'tooltip' => 'Фильтры',

    'fields'  => [
        'search'             => 'Поиск',
        'search-placeholder' => 'Поиск сообщений...',
        'type'               => 'Тип',
        'date'               => 'Дата',
        'sort-by'            => 'Сортировка',
        'pinned-only'        => 'Только закреплённые',
    ],
    'type-options' => [
        'all'          => 'Все типы',
        'note'         => 'Заметки',
        'comment'      => 'Комментарии',
        'notification' => 'Уведомления',
        'activity'     => 'Действия',
    ],
    'date-options' => [
        ''          => 'За всё время',
        'today'     => 'Сегодня',
        'yesterday' => 'Вчера',
        'week'      => 'Последние 7 дней',
        'month'     => 'Последние 30 дней',
        'quarter'   => 'Последние 3 месяца',
        'year'      => 'Последний год',
    ],
    'sort-options' => [
        'created_at_desc' => 'Сначала новые',
        'created_at_asc'  => 'Сначала старые',
        'updated_at_desc' => 'Недавно изменённые',
        'priority'        => 'Приоритет',
    ],
    'actions' => [
        'apply' => 'Применить фильтры',
    ],
];
