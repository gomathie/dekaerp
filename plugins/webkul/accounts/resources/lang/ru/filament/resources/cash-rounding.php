<?php

return [
    'form' => [
        'fields' => [
            'name'               => 'Название',
            'rounding-precision' => 'Rounding Precision',
            'rounding-strategy'  => 'Rounding Strategy',
            'profit-account'     => 'Прибыль Счёт',
            'loss-account'       => 'Убыток Счёт',
            'rounding-method'    => 'Rounding Method',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Название',
            'rounding-strategy'    => 'Rounding Strategy',
            'rounding-method'      => 'Rounding Method',
            'created-by'           => 'Создано От',
            'profit-account'       => 'Прибыль Счёт',
            'loss-account'         => 'Убыток Счёт',
        ],

        'groups' => [
            'name'              => 'Название',
            'rounding-strategy' => 'Rounding Strategy',
            'rounding-method'   => 'Rounding Method',
            'created-by'        => 'Создано От',
            'profit-account'    => 'Прибыль Счёт',
            'loss-account'      => 'Убыток Счёт',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Касса Rounding удалено',
                    'body'  => 'касса rounding есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Касса Rounding удалено',
                    'body'  => 'касса rounding есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'               => 'Название',
            'rounding-precision' => 'Rounding Precision',
            'rounding-strategy'  => 'Rounding Strategy',
            'profit-account'     => 'Прибыль Счёт',
            'loss-account'       => 'Убыток Счёт',
            'rounding-method'    => 'Rounding Method',
        ],
    ],
];
