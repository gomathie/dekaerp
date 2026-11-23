<?php

return [
    'title' => 'Продажи Teams',

    'navigation' => [
        'title' => 'Продажи Teams',
    ],

    'form' => [
        'sections' => [
            'fields' => [
                'name'     => 'Продажи Команда',
                'status'   => 'Статус',
                'fieldset' => [
                    'team-details' => [
                        'title'  => 'Команда Подробности',
                        'fields' => [
                            'team-leader'            => 'Команда Leader',
                            'company'                => 'Компания',
                            'invoiced-target'        => 'Invoiced Назначение',
                            'invoiced-target-suffix' => '/ Месяц',
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
            'id'              => 'Ид.',
            'company'         => 'Компания',
            'team-leader'     => 'Команда Leader',
            'name'            => 'Название',
            'status'          => 'Статус',
            'invoiced-target' => 'Invoiced Назначение',
            'color'           => 'Цвет',
            'created-by'      => 'Создано От',
            'created-at'      => 'Создано',
            'updated-at'      => 'Обновлено',
        ],

        'filters' => [
            'name'        => 'Название',
            'team-leader' => 'Команда Leader',
            'company'     => 'Компания',
            'created-by'  => 'Создано От',
            'updated-at'  => 'Обновлено',
            'created-at'  => 'Создано',
        ],

        'groups' => [
            'name'        => 'Название',
            'company'     => ' Компания',
            'team-leader' => 'Команда Leader',
            'created-at'  => 'Создано',
            'updated-at'  => 'Обновлено',
        ],

        'actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Продажи Команда restored',
                    'body'  => 'продажи Команда есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Продажи Команда удалено',
                    'body'  => 'продажи Команда есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Продажи Команда force удалено',
                    'body'  => 'продажи Команда есть force удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Продажи Teams restored',
                    'body'  => 'продажи Teams есть restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Продажи Teams удалено',
                    'body'  => 'продажи Teams есть удалено successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Продажи Teams force удалено',
                    'body'  => 'продажи Teams есть force удалено successfully.',
                ],
            ],
        ],

        'empty-state-action' => [
            'create' => [
                'notification' => [
                    'title' => 'Продажи Teams создано',
                    'body'  => 'продажи Teams есть создано successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'entries' => [
                'name'     => 'Продажи Команда',
                'status'   => 'Статус',
                'fieldset' => [
                    'team-details' => [
                        'title'   => 'Команда Подробности',
                        'entries' => [
                            'team-leader'            => 'Команда Leader',
                            'company'                => 'Компания',
                            'invoiced-target'        => 'Invoiced Назначение',
                            'invoiced-target-suffix' => '/ Месяц',
                            'color'                  => 'Цвет',
                            'members'                => 'Участники',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
