<?php

return [
    'title' => 'Управление прослеживаемостью',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Партии и серийные номера',
        'enable-lots-serial-numbers-helper-text'                 => 'Полная прослеживаемость от поставщиков до клиентов',
        'configure-lots'                                         => 'Настроить партии',
        'enable-expiration-dates'                                => 'Сроки годности',
        'enable-expiration-dates-helper-text'                    => 'Задавайте сроки годности для партий и серийных номеров',
        'display-on-delivery-slips'                              => 'Показывать в накладных на отгрузку',
        'display-on-delivery-slips-helper-text'                  => 'Партии и серийные номера будут отображаться в накладных на отгрузку',
        'display-expiration-dates-on-delivery-slips'             => 'Показывать сроки годности в накладных на отгрузку',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Сроки годности будут отображаться в накладной на отгрузку',
        'enable-consignments'                                    => 'Консигнация',
        'enable-consignments-helper-text'                        => 'Указывайте владельца хранимых товаров',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'На складе есть товары с включённым отслеживанием партий/серийных номеров. ',
                'body'  => 'Прежде чем отключить эту настройку, отключите отслеживание у всех товаров.',
            ],
        ],
    ],
];
