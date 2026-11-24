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
                'title'  => 'Варианты',

                'fields' => [
                    'name'        => 'Название',
                    'color'       => 'Цвет',
                    'extra-price' => 'Доплата',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'        => 'Название',
            'type'        => 'Тип',
            'deleted-at'  => 'Удалён',
            'created-at'  => 'Создан',
            'updated-at'  => 'Обновлён',
        ],

        'groups' => [
            'type'       => 'Тип',
            'created-at' => 'Создан',
            'updated-at' => 'Обновлён',
        ],

        'filters' => [
            'type' => 'Тип',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Атрибут восстановлен',
                    'body'  => 'Атрибут успешно восстановлен.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Атрибут удалён',
                    'body'  => 'Атрибут успешно удалён.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Атрибут удалён навсегда',
                        'body'  => 'Атрибут успешно удалён навсегда.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить атрибут',
                        'body'  => 'Атрибут нельзя удалить, так как он используется.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Атрибуты восстановлены',
                    'body'  => 'Атрибуты успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Атрибуты удалены',
                    'body'  => 'Атрибуты успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Атрибуты удалены навсегда',
                        'body'  => 'Атрибуты успешно удалены навсегда.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить атрибуты',
                        'body'  => 'Атрибуты нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => [
                'title' => 'Общая информация',

                'entries' => [
                    'name' => 'Название',
                    'type' => 'Тип',
                ],
            ],

            'record-information' => [
                'title' => 'Сведения о записи',

                'entries' => [
                    'creator'    => 'Автор',
                    'created_at' => 'Создан',
                    'updated_at' => 'Последнее обновление',
                ],
            ],
        ],
    ],
];
