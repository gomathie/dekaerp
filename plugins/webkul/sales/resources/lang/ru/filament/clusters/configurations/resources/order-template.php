<?php

return [
    'title' => 'Товары шаблона заказа',

    'navigation' => [
        'title' => 'Товары шаблона заказа',
        'group' => 'Заказы на продажу',
    ],

    'global-search' => [
        'name'    => 'Название',
    ],

    'form' => [
        'fields' => [
            'sort'           => 'Сортировка',
            'order-template' => 'Шаблон заказа',
            'company'        => 'Компания',
            'product'        => 'Товар',
            'product-uom'    => 'Ед. изм. товара',
            'creator'        => 'Создатель',
            'display-type'   => 'Тип отображения',
            'name'           => 'Название',
            'quantity'       => 'Количество',
        ],
    ],

    'table' => [
        'columns' => [
            'sort'           => 'Сортировка',
            'order-template' => 'Шаблон заказа',
            'company'        => 'Компания',
            'product'        => 'Товар',
            'product-uom'    => 'Ед. изм. товара',
            'created-by'     => 'Кем создано',
            'display-type'   => 'Тип отображения',
            'name'           => 'Название',
            'quantity'       => 'Количество',
            'created-at'     => 'Создано',
            'updated-at'     => 'Обновлено',

        ],
        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Товары шаблона заказа обновлены',
                    'body'  => 'Товары шаблона заказа успешно обновлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Товары шаблона заказа удалены',
                    'body'  => 'Товары шаблона заказа успешно удалены.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Товары шаблона заказа удалены',
                    'body'  => 'Товары шаблона заказа успешно удалены.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'sort'           => 'Порядок сортировки',
            'order-template' => 'Шаблон заказа',
            'company'        => 'Компания',
            'product'        => 'Товар',
            'product-uom'    => 'Ед. изм. товара',
            'display-type'   => 'Тип отображения',
            'name'           => 'Название',
            'quantity'       => 'Количество',
        ],
    ],
];
