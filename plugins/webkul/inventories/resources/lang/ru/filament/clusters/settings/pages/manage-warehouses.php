<?php

return [
    'title' => 'Manage Склады',

    'form' => [
        'enable-locations'                      => 'Места',
        'enable-locations-helper-text'          => 'Track товар место в ваш склад',
        'configure-locations'                   => 'Configure Места',
        'enable-multi-steps-routes'             => 'Multi Steps Маршруты',
        'enable-multi-steps-routes-helper-text' => 'Использовать ваш собственную маршруты до manage перевод товары между склады',
        'configure-routes'                      => 'Configure Склад Маршруты',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Есть multiple склады',
                'body'  => 'Вы can\'t deactivate multi-место если вы есть больше чем one склад.',
            ],
        ],
    ],
];
