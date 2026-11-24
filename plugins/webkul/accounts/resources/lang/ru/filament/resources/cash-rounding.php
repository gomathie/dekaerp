<?php

return [
    'form' => [
        'fields' => [
            'name'               => 'Название',
            'rounding-precision' => 'Точность округления',
            'rounding-strategy'  => 'Стратегия округления',
            'profit-account'     => 'Счёт прибыли',
            'loss-account'       => 'Счёт убытков',
            'rounding-method'    => 'Способ округления',
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Название',
            'rounding-strategy'    => 'Стратегия округления',
            'rounding-method'      => 'Способ округления',
            'created-by'           => 'Создал',
            'profit-account'       => 'Счёт прибыли',
            'loss-account'         => 'Счёт убытков',
        ],

        'groups' => [
            'name'              => 'Название',
            'rounding-strategy' => 'Стратегия округления',
            'rounding-method'   => 'Способ округления',
            'created-by'        => 'Создал',
            'profit-account'    => 'Счёт прибыли',
            'loss-account'      => 'Счёт убытков',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Округление наличных удалено',
                    'body'  => 'Округление наличных успешно удалено.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Округления наличных удалены',
                    'body'  => 'Округления наличных успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'               => 'Название',
            'rounding-precision' => 'Точность округления',
            'rounding-strategy'  => 'Стратегия округления',
            'profit-account'     => 'Счёт прибыли',
            'loss-account'       => 'Счёт убытков',
            'rounding-method'    => 'Способ округления',
        ],
    ],
];
