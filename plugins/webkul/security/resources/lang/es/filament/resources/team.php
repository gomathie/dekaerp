<?php

return [
    'title' => 'Equipos',

    'navigation' => [
        'title' => 'Equipos',
    ],

    'form' => [
        'fields' => [
            'name' => 'Nombre',
        ],
    ],

    'table' => [
        'columns' => [
            'name'       => 'Nombre',
            'created-by' => 'Creado por',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Equipo actualizado',
                    'body'  => 'El equipo se ha actualizado correctamente.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Equipo eliminado',
                    'body'  => 'El equipo se ha eliminado correctamente.',
                ],
            ],
        ],

        'empty-state-actions' => [
            'create' => [
                'notification' => [
                    'title' => 'Equipos creados',
                    'body'  => 'Los equipos se han creado correctamente.',
                ],
            ],
        ],
    ],

    'infolist' => [
        'entries' => [
            'name' => 'Nombre',
        ],
    ],
];
