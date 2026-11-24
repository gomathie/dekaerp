<?php

return [
    'navigation' => [
        'title' => 'Отрасли',
    ],

    'form' => [
        'name'      => 'Название',
        'full-name' => 'Полное название',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Название',
            'full-name'  => 'Полное название',
            'created-at' => 'Создано',
            'updated-at' => 'Обновлено',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Отрасль обновлена',
                    'body'  => 'Отрасль успешно обновлена.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Отрасль восстановлена',
                    'body'  => 'Отрасль успешно восстановлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отрасль удалена',
                    'body'  => 'Отрасль успешно удалена.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Отрасль удалена навсегда',
                    'body'  => 'Отрасль успешно удалена навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Отрасли восстановлены',
                    'body'  => 'Отрасли успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Отрасли удалены',
                    'body'  => 'Отрасли успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Отрасли удалены навсегда',
                    'body'  => 'Отрасли успешно удалены навсегда.',
                ],
            ],
        ],
    ],
];
