<?php

return [
    'heading' => [
        'title' => 'Matengenezo Kalenda',
    ],

    'config' => [
        'button-text' => [
            'today' => 'Leo',
            'year'  => 'Mwaka',
            'month' => 'Mwezi',
            'week'  => 'Wiki',
            'list'  => 'Orodha',
        ],
    ],

    'header-actions' => [
        'create' => [
            'label'         => 'Mpya Request',
            'modal-heading' => 'Mpya Matengenezo Request',
            'notification'  => [
                'success' => [
                    'title' => 'Matengenezo request imeundwa',
                    'body'  => 'matengenezo request ina imeundwa successfully.',
                ],
                'error' => [
                    'title' => 'Matengenezo request could si kuwa imeundwa',
                    'body'  => 'Unda matengenezo hatua na timu kwanza.',
                ],
            ],
        ],
    ],

    'view-action' => [
        'label' => 'Angalia',
    ],

    'modal-actions' => [
        'edit' => [
            'label' => 'Hariri',
        ],
    ],

    'form' => [
        'fields' => [
            'subject'      => 'Subject',
            'scheduled-at' => 'Scheduled Saa',
        ],
    ],

    'infolist' => [
        'title'   => 'Matengenezo Request',
        'entries' => [
            'subject'          => 'Subject',
            'date'             => 'Tarehe',
            'time'             => 'Time',
            'technician'       => 'Technician',
            'priority'         => 'Kipaumbele',
            'maintenance-type' => 'Matengenezo Aina',
            'stage'            => 'Hatua',
        ],
    ],
];
