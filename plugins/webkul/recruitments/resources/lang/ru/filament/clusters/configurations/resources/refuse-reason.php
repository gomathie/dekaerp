<?php

return [
    'title' => 'Причина отказа',

    'navigation' => [
        'title' => 'Причины отказа',
        'group' => 'Заявки',
    ],

    'form' => [
        'fields' => [
            'name'             => 'Название',
            'template'         => [
                'title'                    => 'Шаблон',
                'applicant-refuse'         => 'Отказ соискателю',
                'applicant-not-interested' => 'Соискатель не заинтересован',
            ],
            'name-placeholder' => 'Введите причину отказа',
        ],
    ],

    'table' => [
        'columns' => [
            'id'         => 'ID',
            'name'       => 'Название',
            'template'   => 'Шаблон',
            'created-by' => 'Создал',
            'created-at' => 'Дата создания',
            'updated-at' => 'Дата изменения',
        ],

        'filters' => [
            'name'       => 'Название',
            'employee'   => 'Сотрудник',
            'created-by' => 'Создал',
            'updated-at' => 'Дата изменения',
            'created-at' => 'Дата создания',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Причина отказа обновлена',
                    'body'  => 'Причина отказа успешно обновлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Причина отказа удалена',
                    'body'  => 'Причина отказа успешно удалена.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Причины отказа удалены',
                    'body'  => 'Причины отказа успешно удалены.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Причина отказа создана',
                    'body'  => 'Причина отказа успешно создана.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'name'       => 'Название',
        'template'   => 'Шаблон',
    ],
];
