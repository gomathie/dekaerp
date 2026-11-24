<?php

return [
    'form' => [
        'factor-percent'    => 'Коэффициент, %',
        'factor-ratio'      => 'Доля',
        'repartition-type'  => 'Тип распределения',
        'document-type'     => 'Тип документа',
        'account'           => 'Счёт',
        'tax'               => 'Налог',
        'tax-closing-entry' => 'Проводка закрытия налога',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'Коэффициент (%)',
            'account'           => 'Счёт',
            'tax'               => 'Налог',
            'company'           => 'Компания',
            'repartition-type'  => 'Тип распределения',
            'document-type'     => 'Тип документа',
            'tax-closing-entry' => 'Проводка закрытия налога',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Распределение налога обновлено',
                    'body'  => 'Распределение налога успешно обновлено.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Строка распределения налога удалена',
                    'body'  => 'Строка распределения налога успешно удалена.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Строка распределения налога создана',
                    'body'  => 'Строка распределения налога успешно создана.',
                ],
            ],
        ],
    ],
];
