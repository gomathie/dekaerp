<?php

return [
    'global-search' => [
        'zip-from' => 'Zip Kutoka',
        'zip-to'   => 'Zip Hadi',
        'name'     => 'Jina',
    ],

    'form' => [
        'fields' => [
            'name'                   => 'Jina',
            'foreign-vat'            => 'Foreign VAT',
            'country'                => 'Nchi',
            'country-group'          => 'Nchi Kundi',
            'zip-from'               => 'Zip Kutoka',
            'zip-to'                 => 'Zip Hadi',
            'detect-automatically'   => 'Detect Kiotomatiki',
            'notes'                  => 'Maelezo',
            'company'                => 'Kampuni',
        ],
        'tabs' => [
            'account-mapping' => [
                'table' => [
                    'columns' => [
                        'source-account'      => 'Chanzo Akaunti',
                        'destination-account' => 'Unakoenda Akaunti',
                    ],
                ],

            ],
            'tax-mapping' => [
                'table' => [
                    'columns' => [
                        'tax-source'      => 'Kodi Chanzo',
                        'tax-destination' => 'Kodi Unakoenda',
                    ],
                ],
            ],
        ],
    ],

    'table' => [
        'columns' => [
            'name'                 => 'Jina',
            'company'              => 'Kampuni',
            'country'              => 'Nchi',
            'country-group'        => 'Nchi Kundi',
            'created-by'           => 'Imeundwa Na',
            'zip-from'             => 'Zip Kutoka',
            'zip-to'               => 'Zip Hadi',
            'status'               => 'Hali',
            'detect-automatically' => 'Detect Kiotomatiki',
        ],

        'actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Masharti ya malipo imefutwa',
                    'body'  => 'Masharti ya malipo ina imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Kifedha Cheo imefutwa',
                    'body'  => 'kifedha Cheo ina imefutwa successfully.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name'                 => 'Jina',
            'foreign-vat'          => 'Foreign VAT',
            'country'              => 'Nchi',
            'country-group'        => 'Nchi Kundi',
            'zip-from'             => 'Zip Kutoka',
            'zip-to'               => 'Zip Hadi',
            'detect-automatically' => 'Detect Kiotomatiki',
            'notes'                => 'Maelezo',
        ],
    ],
];
