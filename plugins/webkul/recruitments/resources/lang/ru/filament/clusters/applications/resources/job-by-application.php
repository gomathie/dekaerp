<?php

return [
    'title' => 'Должность',

    'navigation' => [
        'title' => 'Должности',
    ],

    'table' => [
        'columns' => [
            'name'         => 'Название',
            'manager-name' => 'Руководитель',
            'company-name' => 'Компания',
        ],

        'actions' => [
            'applications' => [
                'new-applications' => 'Новые заявки: :count',
            ],

            'to-recruitment' => [
                'to-recruitment' => 'К найму: :count',
            ],

            'total-application' => [
                'total-application' => 'Заявки: :count',
            ],
        ],
    ],

];
