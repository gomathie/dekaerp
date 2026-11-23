<?php

return [
    'title' => 'Заказ Шаблон Товары',

    'navigation' => [
        'title' => 'Заказ Шаблон Товары',
        'group' => 'Заказ на продажуs',
    ],

    'global-search' => [
        'name'    => 'Название',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'Sort',
            'order-template' => 'Заказ Шаблон',
            'company'        => 'Компания',
            'product'        => 'Товар',
            'product-uom'    => 'Товар UOM',
            'creator'        => 'Creator',
            'display-type'   => 'Display Тип',
            'name'           => 'Название',
            'quantity'       => 'Количество',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'Sort',
            'order-template' => 'Заказ Шаблон',
            'company'        => 'Компания',
            'product'        => 'Товар',
            'product-uom'    => 'Товар UOM',
            'created-by'     => 'Создано От',
            'display-type'   => 'Display Тип',
            'name'           => 'Название',
            'quantity'       => 'Количество',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Заказ Шаблон Товары обновлено',
                    'body'  => 'заказ шаблон товары есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Заказ Шаблон Товары удалено',
                    'body'  => 'заказ шаблон товары есть удалено successfully.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Заказ Шаблон Товары удалено',
                    'body'  => 'заказ шаблон товары есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'Sort Заказ',
            'order-template' => 'Заказ Шаблон',
            'company'        => 'Компания',
            'product'        => 'Товар',
            'product-uom'    => 'Товар UOM',
            'display-type'   => 'Display Тип',
            'name'           => 'Название',
            'quantity'       => 'Количество',
        ],
    ],
];
