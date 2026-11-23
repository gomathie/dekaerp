<?php

return [
    'label' => 'Return',

    'modal' => [
        'form' => [
            'columns' => [
                'product'                 => 'Товар',
                'quantity'                => 'Количество',
                'uom'                     => 'UOM',
                'excess-quantity-tooltip' => 'количество до return greater чем количество processed в original operation.',
            ],
        ],
    ],

    'notification' => [
        'no-products' => [
            'body' => 'Нет товары до return (только строки в Завершён состояние и не fully returned ещё можно быть returned).',
        ],
        'no-quantities' => [
            'body' => 'Пожалуйста specify в наименее one non-zero количество.',
        ],
    ],
];
