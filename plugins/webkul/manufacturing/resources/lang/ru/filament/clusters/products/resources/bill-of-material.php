<?php

return [
    'navigation' => [
        'title' => 'Спецификации',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'reference'             => 'Номер',
                    'reference-placeholder' => 'напр. BOM-001',
                    'product'               => 'Товар',
                    'product-variant'       => 'Вариант товара',
                    'quantity'              => 'Количество',
                    'uom'                   => 'Ед. изм.',
                    'operation-type'        => 'Тип операции',
                    'company'               => 'Компания',
                    'type'                  => 'Тип спецификации',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Прочее',
                'fields' => [
                    'kit-information'                         => 'О комплекте',
                    'kit-information-content'                 => 'Спецификация-комплект группирует компоненты для перемещений или продаж, а не производится через производственный заказ.',
                    'manufacturing-lead-time'                 => 'Срок производства',
                    'days-to-prepare-manufacturing-order'     => 'Дней на подготовку производственного заказа',
                    'days-suffix'                             => 'дн.',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'Компоненты',
                'add-action' => 'Добавить строку',
                'columns'    => [
                    'component'              => 'Компонент',
                    'apply-on-variants'      => 'Применять к вариантам',
                    'consumed-in-operation'  => 'Расходуется в операции',
                    'highlight-consumption'  => 'Выделять расход',
                    'quantity'               => 'Количество',
                    'uom'                    => 'Единица измерения товара',
                ],
                'validation' => [
                    'component-different-from-product' => 'Компонент должен отличаться от производимого товара.',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'Название',
                        'type'            => 'Тип',
                        'category'        => 'Категория',
                        'company'         => 'Компания',
                        'uom'             => 'Ед. изм.',
                        'uom-placeholder' => 'Ед. изм.',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'Операции',
                'add-action' => 'Добавить строку',
                'actions'    => [
                    'edit'                 => 'Изменить операцию',
                    'copy-existing'        => 'Копировать существующие операции',
                    'copy-existing-fields' => [
                        'operation' => 'Операция',
                    ],
                ],
                'columns'    => [
                    'operation'        => 'Операция',
                    'work-center'      => 'Рабочий центр',
                    'time-mode'        => 'Расчёт длительности',
                    'time-mode-batch'  => 'По последним',
                    'company'          => 'Компания',
                    'apply-on-variants'=> 'Применять к вариантам',
                    'duration'         => 'Длительность (минуты)',
                ],
            ],
            'by-products' => [
                'title'      => 'Побочная продукция',
                'add-action' => 'Добавить строку',
                'columns'    => [
                    'product'   => 'Побочный продукт',
                    'quantity'  => 'Количество',
                    'uom'       => 'Единица измерения',
                    'operation' => 'Производится в операции',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Прочее',
                'fields' => [
                    'ready-to-produce'       => 'Готовность к производству',
                    'routing'                => 'Маршрутизация',
                    'consumption'            => 'Гибкий расход',
                    'operation-dependencies' => 'Зависимости операций',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'Номер',
            'product'    => 'Товар',
            'quantity'   => 'Количество',
            'uom'        => 'Ед. изм.',
            'type'       => 'Тип спецификации',
            'company'    => 'Компания',
            'deleted-at' => 'Удалена',
            'updated-at' => 'Обновлена',
        ],
        'filters' => [
            'product' => 'Товар',
            'type'    => 'Тип спецификации',
            'company' => 'Компания',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Спецификация восстановлена',
                    'body'  => 'Спецификация успешно восстановлена.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Спецификация архивирована',
                    'body'  => 'Спецификация успешно архивирована.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Спецификация удалена',
                        'body'  => 'Спецификация удалена безвозвратно.',
                    ],
                    'error' => [
                        'title' => 'Не удалось удалить спецификацию',
                        'body'  => 'Спецификацию нельзя удалить, так как она используется.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Спецификации восстановлены',
                    'body'  => 'Выбранные спецификации успешно восстановлены.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Спецификации архивированы',
                    'body'  => 'Выбранные спецификации успешно архивированы.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Спецификации удалены',
                        'body'  => 'Выбранные спецификации удалены безвозвратно.',
                    ],
                    'error' => [
                        'title' => 'Не удалось удалить спецификации',
                        'body'  => 'Одна или несколько выбранных спецификаций используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общая информация',
                'entries' => [
                    'reference'      => 'Номер',
                    'product'        => 'Товар',
                    'product-variant'=> 'Вариант товара',
                    'quantity'       => 'Количество',
                    'uom'            => 'Ед. изм.',
                    'operation-type' => 'Тип операции',
                    'company'        => 'Компания',
                    'type'           => 'Тип спецификации',
                ],
            ],
            'record-information' => [
                'title'   => 'Сведения о записи',
                'entries' => [
                    'created-by'   => 'Автор',
                    'created-at'   => 'Создана',
                    'last-updated' => 'Последнее обновление',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'   => 'Компоненты',
                'entries' => [
                    'component' => 'Компонент',
                    'operation' => 'Операция',
                    'quantity'  => 'Количество',
                    'uom'       => 'Единица измерения товара',
                ],
            ],
            'operations' => [
                'title'   => 'Операции',
                'entries' => [
                    'operation'   => 'Операция',
                    'work-center' => 'Рабочий центр',
                    'time-mode'   => 'Расчёт длительности',
                    'duration'    => 'Длительность (минуты)',
                ],
            ],
            'by-products' => [
                'title'   => 'Побочная продукция',
                'entries' => [
                    'product'   => 'Побочный продукт',
                    'quantity'  => 'Количество',
                    'uom'       => 'Единица измерения',
                    'operation' => 'Производится в операции',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Прочее',
                'entries' => [
                    'kit-information'                         => 'О комплекте',
                    'kit-information-content'                 => 'Спецификация-комплект группирует компоненты для перемещений или продаж, а не производится через производственный заказ.',
                    'ready-to-produce'                        => 'Готовность к производству',
                    'routing'                                 => 'Маршрутизация',
                    'consumption'                             => 'Гибкий расход',
                    'operation-dependencies'                  => 'Зависимости операций',
                    'manufacturing-lead-time'                 => 'Срок производства',
                    'days-to-prepare-manufacturing-order'     => 'Дней на подготовку производственного заказа',
                    'days-suffix'                             => 'дн.',
                ],
            ],
        ],
    ],
];
