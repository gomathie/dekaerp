<?php

return [
    'form' => [
        'factor-percent'    => 'Factor Asilimia',
        'factor-ratio'      => 'Factor Ratio',
        'repartition-type'  => 'Repartition Aina',
        'document-type'     => 'Hati Aina',
        'account'           => 'Akaunti',
        'tax'               => 'Kodi',
        'tax-closing-entry' => 'Kodi Closing Entry',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'Factor Asilimia(%)',
            'account'           => 'Akaunti',
            'tax'               => 'Kodi',
            'company'           => 'Kampuni',
            'repartition-type'  => 'Repartition Aina',
            'document-type'     => 'Hati Aina',
            'tax-closing-entry' => 'Kodi Closing Entry',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Kodi Partition imesasishwa',
                    'body'  => 'kodi partition ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Kodi Partition Term imefutwa',
                    'body'  => 'kodi Partition term ina imefutwa successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Kodi Partition Term imeundwa',
                    'body'  => 'kodi Partition term ina imeundwa successfully.',
                ],
            ],
        ],
    ],
];
