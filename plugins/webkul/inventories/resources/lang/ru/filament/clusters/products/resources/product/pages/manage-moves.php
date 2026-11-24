<?php

return [
    'title' => 'Приход/расход',

    'tabs' => [
        'todo'     => 'К выполнению',
        'done'     => 'Выполнено',
        'incoming' => 'Входящие',
        'outgoing' => 'Исходящие',
        'internal' => 'Внутренние',
    ],

    'table' => [
        'columns' => [
            'date'                 => 'Дата',
            'reference'            => 'Номер',
            'product'              => 'Товар',
            'package'              => 'Упаковка',
            'lot'                  => 'Партии / серийные номера',
            'source-location'      => 'Исходное местоположение',
            'destination-location' => 'Местоположение назначения',
            'quantity'             => 'Количество',
            'unit'                 => 'Единица',
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
