<?php

return [
    'manufacturing-manager' => [
        'unplan-order' => [
            'work-orders-already-done'    => "Некоторые производственный заказs уже завершён, поэтому вы нельзя un-plan этот производство заказ.nnIt'd быть shame до waste все тот ход, справа?",
            'work-orders-already-started' => "Некоторые производственный заказs есть уже started, поэтому вы нельзя un-plan этот производство заказ.nnIt'd быть shame до waste все тот ход, справа?",
        ],
    ],

    'work-center-productivity-log' => [
        'time-tracking'                    => 'Time Tracking: :name',
        'no-performance-productivity-loss' => "Вы need до define в наименее one un archive productivity убыток в категория 'Performance'. Создать от конфигурация настройки.",
    ],

    'work-center' => [
        'already-unblocked' => 'Это есть уже unblocked.',
    ],

    'work-order' => [
        'unblock-work-center'        => 'Пожалуйста unblock work center до начать производственный заказ.',
        'already-done-or-cancelled'  => 'Вы нельзя начать производственный заказ тот уже завершён или отменён',
        'no-calendar-on-work-center' => 'нет defined календарь вкл. work center :name.',
        'no-productivity-loss'       => "Вы need до define в наименее one productivity убыток в категория 'Productivity'. Создать Конфигурация настройки.",
        'no-performance-loss'        => "Вы need до define в наименее one productivity убыток в категория 'Performance'. Создать Конфигурация настройки.",
        'impossible-to-plan'         => 'Impossible до plan производственный заказ. Пожалуйста чек work center availabilities.',
    ],

    'order' => [
        'product-in-byproducts'                    => 'Вы нельзя есть :product как finished товар и в Byproducts',
        'missing-lot-serial-number'                => 'Вы need до supply Партия/Серийный номер Номер для товары и "consume" их: :missing_products',
        'serial-number-already-produced'           => 'Этот серийный номер номер для товар :product есть уже produced',
        'byproduct-serial-number-already-produced' => 'серийный номер номер :number используется для byproduct :product есть уже produced',
        'component-serial-number-consumed'         => 'серийный номер номер :number используется для component :component есть уже consumed',
        'components-availability'                  => [
            'available'     => 'Доступно',
            'not-available' => 'Не Доступно',
            'expected'      => 'Ожидается :date',
        ],
    ],
];
