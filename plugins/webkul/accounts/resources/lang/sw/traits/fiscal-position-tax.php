<?php

return [
    'form' => [
        'fields' => [
            'tax-source'      => 'Kodi chanzo',
            'tax-destination' => 'Kodi lengwa',
        ],
    ],

    'table' => [
        'columns' => [
            'tax-source'      => 'Kodi chanzo',
            'tax-destination' => 'Kodi lengwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Awamu ya malipo imesasishwa',
                    'body'  => 'Awamu ya malipo imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Awamu ya malipo imefutwa',
                    'body'  => 'Awamu ya malipo imefutwa kikamilifu.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Awamu ya malipo imeundwa',
                    'body'  => 'Awamu ya malipo imeundwa kikamilifu.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'tax-source'      => 'Kodi chanzo',
            'tax-destination' => 'Kodi lengwa',
        ],
    ],
];
