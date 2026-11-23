<?php

return [
    'title'      => 'Accrual Plan',
    'navigation' => [
        'title' => 'Accrual Plan',
    ],

    'form' => [
        'fields' => [
            'name'                    => 'Заголовок',
            'is-based-on-worked-time' => 'Based вкл. Worked Time',
            'accrued-gain-time'       => 'Accrued Gain Time',
            'carry-over-time'         => 'Carry Более Time',
            'carry-over-date'         => 'Carry Более Дата',
            'status'                  => 'Статус',
        ],
    ],

    'table' => [
        'columns' => [
            'name'   => 'Название',
            'levels' => 'Levels',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accrual Plan удалено',
                    'body'  => 'Accrual Plan есть удалено successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Accrual Plan удалено',
                    'body'  => 'Accrual Plan есть удалено successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'sections' => [
            'basic-information' => 'Basic Сведения',
        ],

        'entries' => [
            'name'                    => 'Название',
            'is-based-on-worked-time' => 'Based вкл. Worked Time',
            'accrued-gain-time'       => 'Accrued Gain Time',
            'carry-over-time'         => 'Carry Более Time',
            'carry-over-day'          => 'Carry Более День',
            'carry-over-month'        => 'Carry Более Месяц',
        ],
    ],
];
