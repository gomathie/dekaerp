<?php

return [
    'title' => 'Quantities',

    'tabs' => [
        'internal-locations' => 'Внутренний Места',
        'transit-locations'  => 'Transit Места',
        'on-hand'            => 'Вкл. Вручную',
        'to-count'           => 'До Количество',
        'to-apply'           => 'До Применить',
    ],

    'form' => [
        'fields' => [
            'product'          => 'Товар',
            'location'         => 'Место',
            'package'          => 'Упаковка',
            'lot'              => 'Партия / Серийный номер Numbers',
            'on-hand-qty'      => 'Вкл. Вручную Количество',
            'storage-category' => 'Storage Категория',
        ],
    ],

    'table' => [
        'columns' => [
            'product'           => 'Товар',
            'location'          => 'Место',
            'lot'               => 'Партия / Серийный номер Numbers',
            'storage-category'  => 'Storage Категория',
            'quantity'          => 'Количество',
            'package'           => 'Упаковка',
            'on-hand'           => 'Вкл. Вручную Количество',
            'unit'              => 'Единица',
            'reserved-quantity' => 'Зарезервировано Количество',

            'on-hand-before-state-updated' => [
                'notification' => [
                    'title' => 'Количество обновлено',
                    'body'  => 'количество есть обновлено successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'label' => 'Добавить Количество',

                'notification' => [
                    'title' => 'Количество added',
                    'body'  => 'количество есть added successfully.',
                ],

                'before' => [
                    'notification' => [
                        'title' => 'Количество уже существует',
                        'body'  => 'Уже есть количество для тот же конфигурация. Пожалуйста обновить количество instead.',
                    ],
                ],
            ],
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Количество удалено',
                    'body'  => 'количество есть удалено successfully.',
                ],
            ],
        ],
    ],
];
