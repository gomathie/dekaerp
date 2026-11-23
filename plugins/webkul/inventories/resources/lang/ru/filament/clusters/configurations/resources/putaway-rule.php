<?php

return [
    'navigation' => [
        'title' => 'Putaway Rules',
        'group' => 'Склад Management',
    ],

    'form' => [
        'fields' => [
            'in-location'          => 'Когда Товар Arrives В',
            'product'              => 'Товар',
            'product-placeholder'  => 'Все Товары',
            'category'             => 'Товар Категория',
            'category-placeholder' => 'Все Категории',
            'storage-category'     => 'Storage Категория',
            'out-location'         => 'Store До',
            'sub-location'         => 'Sub Место',
            'company'              => 'Компания',
        ],
    ],

    'table' => [
        'columns' => [
            'in-location'      => 'Когда Товар Arrives В',
            'product'          => 'Товар',
            'category'         => 'Товар Категория',
            'storage-category' => 'Storage Категория',
            'out-location'     => 'Store До',
            'sub-location'     => 'Sub Место',
            'company'          => 'Компания',
            'deleted-at'       => 'Удалено',
            'created-at'       => 'Создано',
            'updated-at'       => 'Обновлено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Putaway rule обновлено',
                    'body'  => 'putaway rule есть обновлено successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Putaway rule restored',
                    'body'  => 'putaway rule есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Putaway rule удалено',
                    'body'  => 'putaway rule есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Putaway rule could не быть удалено',
                        'body'  => 'putaway rule нельзя быть permanently удалено потому что это referenced от другое записи.',
                    ],

                    'success' => [
                        'title' => 'Putaway rule permanently удалено',
                        'body'  => 'putaway rule есть permanently удалено successfully.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Putaway rules restored',
                    'body'  => 'putaway rules есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Putaway rules удалено',
                    'body'  => 'putaway rules есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'error' => [
                        'title' => 'Putaway rules could не быть удалено',
                        'body'  => 'Некоторые putaway rules нельзя быть permanently удалено потому что они referenced от другое записи.',
                    ],

                    'success' => [
                        'title' => 'Putaway rules permanently удалено',
                        'body'  => 'putaway rules есть permanently удалено successfully.',
                    ],
                ],
            ],
        ],
    ],
];
