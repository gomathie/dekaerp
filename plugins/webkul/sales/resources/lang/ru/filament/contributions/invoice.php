<?php

return [
    'create-sale-order' => [
        'label'  => 'Создать заказ на продажу',
        'helper' => 'Когда счёт подтверждён, подтверждён заказ на продажу сделано от это, с доставка для stocked товары, поэтому это shows в Продажи и Склад.',
    ],

    'linked-order' => [
        'label' => 'Заказ на продажу',
    ],
    'goods-delivered-now' => [
        'label'  => 'Клиент есть taken goods',
        'helper' => 'Записи доставка как завершён когда счёт подтверждён. Untick до отпуск это для склад.',
    ],

    'delivery' => [
        'label'               => 'Доставка',
        'waiting'             => 'Waiting для склад',
        'done'                => 'Delivered',
        'mark-delivered'      => 'Mark как delivered',
        'confirm-heading'     => 'Запись доставка?',
        'confirm-description' => 'позиции вкл. этот счёт быть recorded как delivered и taken вне запас. Позиции без достаточно запас stay waiting.',
        'delivered'           => 'Доставка recorded.',
        'not-enough-stock'    => 'Некоторые позиции не в запас; они всё ещё waiting в Склад.',
    ],
];
