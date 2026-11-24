<?php

return [
    'form' => [
        'factor-percent'    => 'Asilimia ya kipengele',
        'factor-ratio'      => 'Uwiano wa kipengele',
        'repartition-type'  => 'Aina ya mgawanyo',
        'document-type'     => 'Aina ya hati',
        'account'           => 'Akaunti',
        'tax'               => 'Kodi',
        'tax-closing-entry' => 'Ingizo la kufunga kodi',
    ],

    'table' => [
        'columns' => [
            'factor-percent'    => 'Asilimia ya kipengele (%)',
            'account'           => 'Akaunti',
            'tax'               => 'Kodi',
            'company'           => 'Kampuni',
            'repartition-type'  => 'Aina ya mgawanyo',
            'document-type'     => 'Aina ya hati',
            'tax-closing-entry' => 'Ingizo la kufunga kodi',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Mgawanyo wa kodi umesasishwa',
                    'body'  => 'Mgawanyo wa kodi umesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Mstari wa mgawanyo wa kodi umefutwa',
                    'body'  => 'Mstari wa mgawanyo wa kodi umefutwa kikamilifu.',
                ],
            ],
        ],

        'header-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Mstari wa mgawanyo wa kodi umeundwa',
                    'body'  => 'Mstari wa mgawanyo wa kodi umeundwa kikamilifu.',
                ],
            ],
        ],
    ],
];
