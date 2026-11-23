<?php

return [
    'title' => 'Manage Счёт',

    'breadcrumb' => 'Manage Счёт',

    'navigation' => [
        'title' => 'Manage Счёт',
    ],

    'form' => [
        'invoice-policy' => [
            'label'      => 'Счёт Policy',
            'label-help' => 'Define как счета generated от заказ на продажуs.',
            'options'    => [
                'order'    => 'Generate счёт based вкл. ordered quantities',
                'delivery' => 'Generate счёт based вкл. delivered quantities',
            ],
        ],
    ],
];
