<?php

return [
    'title' => 'Этапы',

    'navigation' => [
        'title' => 'Этапы',
        'group' => 'Должности',
    ],

    'form' => [
        'sections' => [
            'general-information' => [
                'title' => 'Общие сведения',

                'fields' => [
                    'stage-name'   => 'Название этапа',
                    'sort'         => 'Порядок',
                    'requirements' => 'Требования',
                ],
            ],

            'tooltips' => [
                'title'       => 'Подсказки',
                'description' => 'Задайте свои подписи для статусов заявки.',

                'fields' => [
                    'gray-label'          => 'Серая метка',
                    'gray-label-tooltip'  => 'Подпись для серого статуса.',
                    'red-label'           => 'Красная метка',
                    'red-label-tooltip'   => 'Подпись для красного статуса.',
                    'green-label'         => 'Зелёная метка',
                    'green-label-tooltip' => 'Подпись для зелёного статуса.',
                ],
            ],

            'additional-information' => [
                'title' => 'Дополнительная информация',

                'fields' => [
                    'job-positions' => 'Должности',
                    'folded'        => 'Свёрнут',
                    'hired-stage'   => 'Этап найма',
                    'default-stage' => 'Этап по умолчанию',
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'                 => 'ID',
            'name'               => 'Название этапа',
            'hired-stage'        => 'Этап найма',
            'default-stage'      => 'Этап по умолчанию',
            'folded'             => 'Свёрнут',
            'job-positions'      => 'Должности',
            'created-by'         => 'Создал',
            'created-at'         => 'Дата создания',
            'updated-at'         => 'Дата изменения',
        ],

        'filters' => [
            'name'         => 'Название этапа',
            'job-position' => 'Должность',
            'folded'       => 'Свёрнут',
            'gray-label'   => 'Серая метка',
            'red-label'    => 'Красная метка',
            'green-label'  => 'Зелёная метка',
            'created-by'   => 'Создал',
            'created-at'   => 'Дата создания',
            'updated-at'   => 'Дата изменения',
        ],

        'groups' => [
            'job-position' => 'Должность',
            'stage-name'   => 'Название этапа',
            'folded'       => 'Свёрнут',
            'gray-label'   => 'Серая метка',
            'red-label'    => 'Красная метка',
            'green-label'  => 'Зелёная метка',
            'created-by'   => 'Создал',
            'created-at'   => 'Дата создания',
            'updated-at'   => 'Дата изменения',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'success' => [
                        'title' => 'Этапы удалены',
                        'body'  => 'Этапы успешно удалены.',
                    ],

                    'error' => [
                        'title' => 'Не удалось удалить этапы',
                        'body'  => 'Этапы нельзя удалить, так как они используются.',
                    ],
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Этапы удалены',
                    'body'  => 'Этапы успешно удалены.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'label' => 'Новый этап',
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'general-information' => [
                'title' => 'Общие сведения',

                'entries' => [
                    'stage-name'   => 'Название этапа',
                    'sort'         => 'Порядок',
                    'requirements' => 'Требования',
                ],
            ],

            'tooltips' => [
                'title'       => 'Подсказки',
                'description' => 'Задайте свои подписи для статусов заявки.',

                'entries' => [
                    'gray-label'          => 'Серая метка',
                    'gray-label-tooltip'  => 'Подпись для серого статуса.',
                    'red-label'           => 'Красная метка',
                    'red-label-tooltip'   => 'Подпись для красного статуса.',
                    'green-label'         => 'Зелёная метка',
                    'green-label-tooltip' => 'Подпись для зелёного статуса.',
                ],
            ],

            'additional-information' => [
                'title' => 'Дополнительная информация',

                'entries' => [
                    'job-positions'      => 'Должность',
                    'folded'             => 'Свёрнут',
                    'hired-stage'        => 'Этап найма',
                    'default-stage'      => 'Этап по умолчанию',
                ],
            ],
        ],
    ],

];
