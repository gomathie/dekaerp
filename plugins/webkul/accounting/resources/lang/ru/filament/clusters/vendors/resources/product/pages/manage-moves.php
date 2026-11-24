<?php

return [
    'title' => 'ПРИХОД/РАСХОД',

    'table' => [
        'columns' => [
            'date'                 => 'Дата',
            'reference'            => 'Ссылка',
            'product'              => 'Товар',
            'package'              => 'Упаковка',
            'lot'                  => 'Партии / серийные номера',
            'source-location'      => 'Исходное место',
            'destination-location' => 'Место назначения',
            'quantity'             => 'Количество',
            'state'                => 'Статус',
            'done-by'              => 'Выполнил',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Движение удалено',
                    'body'  => 'Движение успешно удалено.',
                ],
            ],
        ],
    ],
];
