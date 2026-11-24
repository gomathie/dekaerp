<?php

return [
    'title' => 'Управление складами',

    'form' => [
        'enable-locations'                      => 'Местоположения',
        'enable-locations-helper-text'          => 'Отслеживайте местоположение товаров на складе',
        'configure-locations'                   => 'Настроить местоположения',
        'enable-multi-steps-routes'             => 'Многоэтапные маршруты',
        'enable-multi-steps-routes-helper-text' => 'Используйте свои маршруты для перемещения товаров между складами',
        'configure-routes'                      => 'Настроить маршруты склада',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Несколько складов',
                'body'  => 'Нельзя отключить несколько местоположений, если у вас больше одного склада.',
            ],
        ],
    ],
];
