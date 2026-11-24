<?php

return [
    'title' => 'Команды продаж',

    'navigation' => [
        'title' => 'Команды продаж',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'Команда продаж',
                'status'   => 'Статус',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'Сведения о команде',
                        'fields' => [
                            'team-leader'            => 'Руководитель команды',
                            'company'                => 'Компания',
                            'invoiced-target'        => 'План по выставлению',
                            'invoiced-target-suffix' => '/ мес.',
                            'color'                  => 'Цвет',
                            'members'                => 'Участники',
                        ],
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'id'              => 'ID',
            'company'         => 'Компания',
            'team-leader'     => 'Руководитель команды',
            'name'            => 'Название',
            'status'          => 'Статус',
            'invoiced-target' => 'План по выставлению',
            'color'           => 'Цвет',
            'created-by'      => 'Кем создано',
            'created-at'      => 'Создано',
            'updated-at'      => 'Обновлено',
        ],

        'filters' => [
            'name'        => 'Название',
            'team-leader' => 'Руководитель команды',
            'company'     => 'Компания',
            'created-by'  => 'Кем создано',
            'updated-at'  => 'Обновлено',
            'created-at'  => 'Создано',
        ],

        'groups' => [
            'name'        => 'Название',
            'company'     => ' Компания',
            'team-leader' => 'Руководитель команды',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Команда продаж восстановлена',
                    'body'  => 'Команда продаж успешно восстановлена.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Команда продаж удалена',
                    'body'  => 'Команда продаж успешно удалена.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Команда продаж удалена навсегда',
                    'body'  => 'Команда продаж успешно удалена навсегда.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Команды продаж восстановлены',
                    'body'  => 'Команды продаж успешно восстановлены.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Команды продаж удалены',
                    'body'  => 'Команды продаж успешно удалены.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Команды продаж удалены навсегда',
                    'body'  => 'Команды продаж успешно удалены навсегда.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Команды продаж созданы',
                    'body'  => 'Команды продаж успешно созданы.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'Команда продаж',
                'status'   => 'Статус',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'Сведения о команде',
                        'entries' => [
                            'team-leader'            => 'Руководитель команды',
                            'company'                => 'Компания',
                            'invoiced-target'        => 'План по выставлению',
                            'invoiced-target-suffix' => '/ мес.',
                            'color'                  => 'Цвет',
                            'members'                => 'Участники',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
