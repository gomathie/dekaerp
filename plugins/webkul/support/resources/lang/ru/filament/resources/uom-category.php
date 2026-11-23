<?php

return [
    'navigation' => [
        'title' => 'UOM Категории',
    ],

    'form' => [
        'sections' => [
            'general' => [
                'title' => 'Общее',

                'fields' => [
                    'name' => 'Название',
                ],
            ],

            'uoms' => [
                'title' => 'Единицы Measure',

                'fields' => [
                    'uoms'     => 'Единицы',
                    'type'     => 'Тип',
                    'name'     => 'Единица измерения',
                    'ratio'    => 'Ratio',
                    'rounding' => 'Rounding Precision',
                ],

                'validations' => [
                    'missing-reference'          => 'Этот категория следует есть ссылка единица измерения.',
                    'multiple-references'        => 'Этот категория следует только есть one ссылка единица измерения.',
                    'ratio-greater-than-zero'    => 'conversion ratio для единица измерения нельзя быть zero.',
                    'rounding-greater-than-zero' => 'rounding precision нужно быть strictly positive.',
                ],

                'actions' => [
                    'add' => 'Добавить Единица',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'uoms'       => 'UOMs',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'groups' => [
            'created-at' => 'Создано',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'UOM Категория обновлено',
                    'body'  => 'UOM категория есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'UOM Категория удалено',
                    'body'  => 'UOM категория есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'UOM Категории удалено',
                    'body'  => 'UOM категории есть удалено successfully.',
                ],
            ],
        ],
    ],
];
