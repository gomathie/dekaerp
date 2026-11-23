<?php

return [
    'title' => 'В/Вне',

    'tabs' => [
        'todo'     => 'До',
        'done'     => 'Завершён',
        'incoming' => 'Incoming',
        'outgoing' => 'Outgoing',
        'internal' => 'Внутренний',
    ],

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
            'unit'                 => 'Единица',
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
