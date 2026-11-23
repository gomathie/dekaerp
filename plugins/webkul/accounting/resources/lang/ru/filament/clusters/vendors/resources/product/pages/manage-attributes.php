<?php

return [
    'title' => 'Attributes',

    'form' => [
        'attribute' => 'Attribute',
        'values'    => 'Values',
    ],

    'table' => [
        'description' => 'Предупреждение: adding или deleting attributes удалить и recreate existing variants и lead до убыток их possible customizations.',

        'header-actions' => [
            'create' => [
                'label' => 'Добавить Attribute',

                'notification' => [
                    'title' => 'Attribute создано',
                    'body'  => 'attribute есть создано successfully.',
                ],
            ],
        ],

        'columns' => [
            'attribute' => 'Attribute',
            'values'    => 'Values',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Attribute обновлено',
                    'body'  => 'attribute есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Attribute удалено',
                    'body'  => 'attribute есть удалено successfully.',
                ],
            ],
        ],
    ],
];
