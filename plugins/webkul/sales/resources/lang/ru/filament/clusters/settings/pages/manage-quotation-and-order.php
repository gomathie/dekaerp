<?php

return [
    'title' => 'Manage Коммерческое предложение & Заказ',

    'breadcrumb' => 'Manage Коммерческое предложение & Заказ',

    'navigation' => [
        'title' => 'Manage Коммерческое предложение & Заказ',
    ],

    'form' => [
        'fields' => [
            'validity-suffix'             => 'дни',
            'validity'                    => 'По умолчанию Коммерческое предложение Validity',
            'validity-help'               => 'по умолчанию номер дни коммерческое предложение действительно для.',
            'lock-confirm-sales'          => 'Lock Подтвердить Продажи',
            'lock-confirm-sales-help'     => 'Если включено, заказ на продажу быть заблокировано после confirmation.',
            'goods-leave-on-invoice'      => 'Клиенты take goods когда invoiced',
            'goods-leave-on-invoice-help' => 'По умолчанию для счета тот создать заказ на продажу. Вкл.: доставка recorded как завершён когда счёт подтверждён. Выкл.: доставка осталось для склад до validate.',
        ],
    ],
];
