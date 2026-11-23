<?php

return [
    'navigation' => [
        'title' => 'Посещаемость настройки',
    ],
    'title'      => 'Посещаемость настройки',
    'subheading' => 'Посещаемость для :company. Switch это вкл. здесь, затем сотрудники можно clock в от панель.',
    'no-company' => 'Выбрать компания до manage его посещаемость настройки.',
    'sections'   => [
        'status' => 'Статус',
        'rules'  => 'Rules',
    ],
    'status' => [
        'enabled'  => 'Вкл. для этот компания',
        'disabled' => 'Выкл. для этот компания',
    ],
    'fields' => [
        'auto-checkout'         => 'Автоматически чек-вне после',
        'auto-checkout-help'    => 'forgotten чек-вне закрыт после этот много часы и flagged для review.',
        'capture-location'      => 'Место в clock-в',
        'capture-location-help' => 'Выкл.: nothing recorded. Необязательно: recorded когда device shares это. Обязательно: clock-в needs это.',
        'late-tolerance'        => 'Late после',
        'overtime-tolerance'    => 'Сверхурочные после',
    ],
    'units' => [
        'hours'   => 'часы',
        'minutes' => 'минуты',
    ],
    'actions' => [
        'save'   => 'Сохранить',
        'enable' => [
            'label'        => 'Switch вкл.',
            'heading'      => 'Switch посещаемость вкл.?',
            'description'  => 'Сотрудники этот компания с сотрудник запись можно clock в и вне от панель. Managers see их команда.',
            'notification' => 'Посещаемость вкл. для этот компания.',
        ],
        'disable' => [
            'label'        => 'Switch выкл.',
            'heading'      => 'Switch посещаемость выкл.?',
            'description'  => 'Clocking в остановки и посещаемость screens скрыто. Existing записи kept.',
            'notification' => 'Посещаемость выкл. для этот компания.',
        ],
    ],
    'notifications' => [
        'saved' => 'Посещаемость настройки saved.',
    ],
];
