<?php

return [
    'title' => 'Manage Операции',

    'form' => [
        'enable-work-orders' => [
            'label'       => 'Производственный заказs',
            'helper-text' => 'Execute операции в designated work centers.',
            'link-text'   => 'Configure work centers',
        ],

        'enable-work-order-dependencies' => [
            'label'       => 'Производственный заказ Dependencies',
            'helper-text' => 'Задать нумерация в который производственный заказs следует быть processed. Включить этот feature от Miscellaneous вкладка каждый BoM.',
        ],

        'enable-byproducts' => [
            'label'       => 'Byproducts',
            'helper-text' => 'Generate от-товары в течение рабочая среда ( + B → C + D).',
        ],
    ],
];
