<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Kodi Chanzo',
            'tax-destination' => 'Kodi Unakoenda',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Kodi Chanzo',
            'tax-destination' => 'Kodi Unakoenda',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Malipo Due Term imesasishwa',
                    'body'  => 'malipo due term ina imesasishwa successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Malipo Due Term imefutwa',
                    'body'  => 'malipo due term ina imefutwa successfully.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Malipo Due Term imeundwa',
                    'body'  => 'malipo due term ina imeundwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Kodi Chanzo',
            'tax-destination' => 'Kodi Unakoenda',
        ],
    ],
];
