<?php

return [
    'label'         => 'Produce Все',
    'partial-label' => 'Produce',

    'modal' => [
        'consumption-warning' => [
            'heading'     => 'Consumption Предупреждение',
            'description' => 'Некоторые товары consumed different количество чем ожидается. вы want до validate производство заказ с текущий quantities?',

            'form' => [
                'product'    => 'Товар',
                'to-consume' => 'До Consume',
                'consumed'   => 'Consumed',
                'uom'        => 'Единица измерения',
            ],

            'actions' => [
                'confirm' => [
                    'label' => 'Подтвердить',
                ],

                'set-quantities' => [
                    'label' => 'Задать Quantities и Подтвердить',
                ],
            ],
        ],

        'produced-warning' => [
            'heading'     => 'Produced different чем ожидается',
            'description' => 'количество produced different чем ожидается. вы want до подтвердить производство заказ с текущий количество?',
        ],
    ],

    'notification' => [
        'success' => [
            'title' => 'Производство заказ завершено',
            'body'  => 'производство заказ есть завершено successfully.',
        ],
    ],
];
