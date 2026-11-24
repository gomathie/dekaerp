<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',

            'actions' => [
                'without-content' => [
                    'label' => 'Chapisha msimbopau',
                ],

                'with-content' => [
                    'label' => 'Chapisha msimbopau pamoja na yaliyomo',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Kifurushi kimefutwa',
                    'body'  => 'Kifurushi kimefutwa kwa mafanikio.',
                ],

                'error' => [
                    'title' => 'Kifurushi hakikuweza kufutwa',
                    'body'  => 'Kifurushi hakiwezi kufutwa kwa sababu kinatumika.',
                ],
            ],
        ],
    ],
];
