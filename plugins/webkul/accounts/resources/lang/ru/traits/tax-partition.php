<?php

return [
    'form' => [
        'factor-percent'    => 'Factor Процент',
        'factor-ratio'      => 'Factor Ratio',
        'repartition-type'  => 'Repartition Тип',
        'document-type'     => 'Документ Тип',
        'account'           => 'Счёт',
        'tax'               => 'Налог',
        'tax-closing-entry' => 'Налог Closing Entry',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'Factor Процент(%)',
            'account'           => 'Счёт',
            'tax'               => 'Налог',
            'company'           => 'Компания',
            'repartition-type'  => 'Repartition Тип',
            'document-type'     => 'Документ Тип',
            'tax-closing-entry' => 'Налог Closing Entry',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Налог Partition обновлено',
                    'body'  => 'налог partition есть обновлено successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Налог Partition Term удалено',
                    'body'  => 'налог Partition term есть удалено successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Налог Partition Term создано',
                    'body'  => 'налог Partition term есть создано successfully.',
                ],
            ],
        ],
    ],
];
