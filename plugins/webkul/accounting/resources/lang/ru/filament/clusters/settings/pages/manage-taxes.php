<?php

return [
    'title' => 'Manage Налоги',

    'form' => [
        'default-taxes' => [
            'label'       => 'По умолчанию Налоги',
            'helper-text' => 'По умолчанию быть применено вкл. товары если нет налог выбрано',
        ],

        'sales-tax' => [
            'label' => 'Продажи Налог',
        ],

        'purchase-tax' => [
            'label' => 'Закупка Налог',
        ],

        'prices' => [
            'label' => 'Цены',
        ],

        'rounding-method' => [
            'label'       => 'Rounding Method',
            'helper-text' => 'Method используется до round налог amounts',

            'options' => [
                'round-per-line' => 'Round За Строка',
                'round-globally' => 'Round Globally',
            ],
        ],

        'fiscal-country' => [
            'label' => 'Финансовый Страна',
        ],
    ],
];
