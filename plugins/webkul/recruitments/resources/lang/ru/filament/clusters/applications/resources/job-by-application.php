<?php

return [
    'title' => 'Должность Должность',

    'navigation' => [
        'title' => 'Должность Positions',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'manager-name' => 'Руководитель',
            'company-name' => 'Компания',
        ],

        'actions' => [
            'applications' => [
                'new-applications' => ':count Новое Applications',
            ],

            'to-recruitment' => [
                'to-recruitment' => ':count До Подбор',
            ],

            'total-application' => [
                'total-application' => ':count Applications',
            ],
        ],
    ],

];
