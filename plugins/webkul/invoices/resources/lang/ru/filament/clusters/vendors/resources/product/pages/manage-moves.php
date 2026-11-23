<?php

return [
    'title' => 'В/Вне',

    'table' => [
        'columns' => [
            'date'                 => 'Дата',
            'reference'            => 'Ссылка',
            'product'              => 'Товар',
            'package'              => 'Упаковка',
            'lot'                  => 'Партия / Серийный номер Numbers',
            'source-location'      => 'Источник Место',
            'destination-location' => 'Назначение Место',
            'quantity'             => 'Количество',
            'state'                => 'Состояние',
            'done-by'              => 'Завершён От',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Переместить удалено',
                    'body'  => 'переместить есть удалено successfully.',
                ],
            ],
        ],
    ],
];
