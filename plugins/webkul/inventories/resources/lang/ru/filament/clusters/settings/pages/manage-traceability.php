<?php

return [
    'title' => 'Manage Traceability',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Партии & Серийный номер Numbers',
        'enable-lots-serial-numbers-helper-text'                 => 'Получить полный traceability от поставщики до клиенты',
        'configure-lots'                                         => 'Configure Партии',
        'enable-expiration-dates'                                => 'Expiration Dates',
        'enable-expiration-dates-helper-text'                    => 'Задать expiration dates вкл. партии & серийный номер numbers',
        'display-on-delivery-slips'                              => 'Display вкл. Доставка Slips',
        'display-on-delivery-slips-helper-text'                  => 'Партии & Серийный номер numbers appear вкл. доставка slips',
        'display-expiration-dates-on-delivery-slips'             => 'Display Expiration Dates вкл. Доставка Slips',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Expiration dates appear вкл. доставка slip',
        'enable-consignments'                                    => 'Consignments',
        'enable-consignments-helper-text'                        => 'Задать владелец вкл. stored товары',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Вы есть товары в запас тот есть партия/серийный номер номер tracking включено. ',
                'body'  => 'Первый switch выкл. tracking вкл. все товары до switching выкл. этот настройка.',
            ],
        ],
    ],
];
