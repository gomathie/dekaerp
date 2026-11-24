<?php

return [
    'navigation' => [
        'title' => 'Типы услуг',
    ],

    'form' => [
        'fields' => [
            'transport-mode'           => 'Вид транспорта',
            'product-reference'        => 'Ссылка на товар-услугу',
            'product-reference-helper' => 'Ссылка на товар-услугу, начисляемый за эту услугу в каждой компании, например LOG-FREIGHT.',
        ],
    ],

    'table' => [
        'columns' => [
            'transport-mode'    => 'Вид транспорта',
            'product-reference' => 'Товар-услуга',
        ],
    ],
];
