<?php

return [
    'navigation' => [
        'title' => 'Счета поставщиков Materials',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',
                'fields' => [
                    'reference'             => 'Ссылка',
                    'reference-placeholder' => 'eg. BOM-001',
                    'product'               => 'Товар',
                    'product-variant'       => 'Товар Variant',
                    'quantity'              => 'Количество',
                    'uom'                   => 'UOM',
                    'operation-type'        => 'Operation Тип',
                    'company'               => 'Компания',
                    'type'                  => 'BoM Тип',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'kit-information'                         => 'Kit Сведения',
                    'kit-information-content'                 => 'kit BoM используется до группа components для перемещения или продажи, instead produced через производство заказ.',
                    'manufacturing-lead-time'                 => 'Производство Lead Time',
                    'days-to-prepare-manufacturing-order'     => 'Дни до prepare Производство Заказ',
                    'days-suffix'                             => 'дни',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'      => 'Components',
                'add-action' => 'Добавить строка',
                'columns'    => [
                    'component'              => 'Component',
                    'apply-on-variants'      => 'Применить вкл. Variants',
                    'consumed-in-operation'  => 'Consumed в Operation',
                    'highlight-consumption'  => 'Highlight Consumption',
                    'quantity'               => 'Количество',
                    'uom'                    => 'Товар Единица измерения',
                ],
                'validation' => [
                    'component-different-from-product' => 'component нужно быть different от товар manufactured.',
                ],
                'create-form' => [
                    'fields' => [
                        'name'            => 'Название',
                        'type'            => 'Тип',
                        'category'        => 'Категория',
                        'company'         => 'Компания',
                        'uom'             => 'UOM',
                        'uom-placeholder' => 'UOM',
                    ],
                ],
            ],
            'operations' => [
                'title'      => 'Операции',
                'add-action' => 'Добавить строка',
                'actions'    => [
                    'edit'                 => 'Изменить Operation',
                    'copy-existing'        => 'Копировать Existing Операции',
                    'copy-existing-fields' => [
                        'operation' => 'Operation',
                    ],
                ],
                'columns'    => [
                    'operation'        => 'Operation',
                    'work-center'      => 'Work Center',
                    'time-mode'        => 'Duration Computation',
                    'time-mode-batch'  => 'Computed вкл. последний',
                    'company'          => 'Компания',
                    'apply-on-variants'=> 'Применить вкл. Variants',
                    'duration'         => 'Duration (минуты)',
                ],
            ],
            'by-products' => [
                'title'      => 'От-товары',
                'add-action' => 'Добавить строка',
                'columns'    => [
                    'product'   => 'От-товар',
                    'quantity'  => 'Количество',
                    'uom'       => 'Единица измерения',
                    'operation' => 'Produced в Operation',
                ],
            ],
            'miscellaneous' => [
                'title'  => 'Miscellaneous',
                'fields' => [
                    'ready-to-produce'       => 'Производство Readiness',
                    'routing'                => 'Routing',
                    'consumption'            => 'Flexible Consumption',
                    'operation-dependencies' => 'Operation Dependencies',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'reference'  => 'Ссылка',
            'product'    => 'Товар',
            'quantity'   => 'Количество',
            'uom'        => 'UOM',
            'type'       => 'BOM Тип',
            'company'    => 'Компания',
            'deleted-at' => 'Удалено',
            'updated-at' => 'Обновлено',
        ],
        'filters' => [
            'product' => 'Товар',
            'type'    => 'BOM Тип',
            'company' => 'Компания',
        ],
        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Счёт поставщика material restored',
                    'body'  => 'счёт поставщика material есть restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Счёт поставщика material в архиве',
                    'body'  => 'счёт поставщика material есть в архиве successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Счёт поставщика material удалено',
                        'body'  => 'счёт поставщика material есть permanently удалено.',
                    ],
                    'error' => [
                        'title' => 'Счёт поставщика material could не быть удалено',
                        'body'  => 'счёт поставщика material нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],
        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Счета поставщиков material restored',
                    'body'  => 'выбрано счета поставщиков material есть restored successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Счета поставщиков material в архиве',
                    'body'  => 'выбрано счета поставщиков material есть в архиве successfully.',
                ],
            ],
            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Счета поставщиков material удалено',
                        'body'  => 'выбрано счета поставщиков material есть permanently удалено.',
                    ],
                    'error' => [
                        'title' => 'Счета поставщиков material could не быть удалено',
                        'body'  => 'One или больше выбрано счета поставщиков material currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title'   => 'Общее Сведения',
                'entries' => [
                    'reference'      => 'Ссылка',
                    'product'        => 'Товар',
                    'product-variant'=> 'Товар Variant',
                    'quantity'       => 'Количество',
                    'uom'            => 'UOM',
                    'operation-type' => 'Operation Тип',
                    'company'        => 'Компания',
                    'type'           => 'BoM Тип',
                ],
            ],
            'record-information' => [
                'title'   => 'Запись Сведения',
                'entries' => [
                    'created-by'   => 'Создано От',
                    'created-at'   => 'Создано',
                    'last-updated' => 'Последний Обновлено',
                ],
            ],
        ],
        'tabs' => [
            'components' => [
                'title'   => 'Components',
                'entries' => [
                    'component' => 'Component',
                    'operation' => 'Operation',
                    'quantity'  => 'Количество',
                    'uom'       => 'Товар Единица измерения',
                ],
            ],
            'operations' => [
                'title'   => 'Операции',
                'entries' => [
                    'operation'   => 'Operation',
                    'work-center' => 'Work Center',
                    'time-mode'   => 'Duration Computation',
                    'duration'    => 'Duration (минуты)',
                ],
            ],
            'by-products' => [
                'title'   => 'От-товары',
                'entries' => [
                    'product'   => 'От-товар',
                    'quantity'  => 'Количество',
                    'uom'       => 'Единица измерения',
                    'operation' => 'Produced в Operation',
                ],
            ],
            'miscellaneous' => [
                'title'   => 'Miscellaneous',
                'entries' => [
                    'kit-information'                         => 'Kit Сведения',
                    'kit-information-content'                 => 'kit BoM используется до группа components для перемещения или продажи, instead produced через производство заказ.',
                    'ready-to-produce'                        => 'Производство Readiness',
                    'routing'                                 => 'Routing',
                    'consumption'                             => 'Flexible Consumption',
                    'operation-dependencies'                  => 'Operation Dependencies',
                    'manufacturing-lead-time'                 => 'Производство Lead Time',
                    'days-to-prepare-manufacturing-order'     => 'Дни до prepare Производство Заказ',
                    'days-suffix'                             => 'дни',
                ],
            ],
        ],
    ],
];
