<?php

return [
    'title' => 'الفرق',

    'navigation' => [
        'title' => 'الفرق',
    ],

    'form' => [
        'fields' => [
            'name' => 'الاسم',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'الاسم',
            'created-by' => 'أُنشئ بواسطة',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'تم تحديث الفريق',
                    'body'  => 'تم تحديث الفريق بنجاح.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'تم حذف الفريق',
                    'body'  => 'تم حذف الفريق بنجاح.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'تم إنشاء الفرق',
                    'body'  => 'تم إنشاء الفرق بنجاح.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'الاسم',
        ],
    ],
];
