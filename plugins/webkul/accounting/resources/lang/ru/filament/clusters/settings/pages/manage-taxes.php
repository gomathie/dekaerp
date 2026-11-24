<?php

return [
    'title' => 'Управление налогами',

    'form' => [
        'default-taxes' => [
            'label'       => 'Налоги по умолчанию',
            'helper-text' => 'Применяются к товарам, если налог не выбран',
        ],

        'sales-tax' => [
            'label' => 'Налог с продаж',
        ],

        'purchase-tax' => [
            'label' => 'Налог на закупки',
        ],

        'prices' => [
            'label' => 'Цены',
        ],

        'rounding-method' => [
            'label'       => 'Метод округления',
            'helper-text' => 'Метод округления сумм налогов',

            'options' => [
                'round-per-line' => 'Округлять по строкам',
                'round-globally' => 'Округлять итог',
            ],
        ],

        'fiscal-country' => [
            'label' => 'Страна налогообложения',
        ],
    ],
];
