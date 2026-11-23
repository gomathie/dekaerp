<?php

return [
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'Общее',

                'fields' => [
                    'name' => 'Название',
                    'type' => 'Тип',
                ],
            ],

            'options' => [
                'title'  => 'Options',

                'fields' => [
                    'name'        => 'Название',
                    'color'       => 'Цвет',
                    'extra-price' => 'Extra Цена',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Название',
            'type'        => 'Тип',
            'deleted-at'  => 'Удалено',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
        ],

        'groups' => [
            'type'       => 'Тип',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'filters' => [
            'type' => 'Тип',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attribute restored',
                    'body'  => 'attribute есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attribute удалено',
                    'body'  => 'attribute есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attribute force удалено',
                        'body'  => 'attribute есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Attribute could не быть удалено',
                        'body'  => 'attribute нельзя быть удалено потому что это currently в использовать.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Attributes restored',
                    'body'  => 'attributes есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attributes удалено',
                    'body'  => 'attributes есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Attributes force удалено',
                        'body'  => 'attributes есть force удалено successfully.',
                    ],

                    'error' => [
                        'title' => 'Attributes could не быть удалено',
                        'body'  => 'attributes нельзя быть удалено потому что они currently в использовать.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общее Сведения',

                'entries' => [
                    'name' => 'Название',
                    'type' => 'Тип',
                ],
            ],

            'record-information' => [
                'title' => 'Запись Сведения',

                'entries' => [
                    'creator'    => 'Создано От',
                    'created_at' => 'Создано',
                    'updated_at' => 'Последний Обновлено',
                ],
            ],
        ],
    ],
];
