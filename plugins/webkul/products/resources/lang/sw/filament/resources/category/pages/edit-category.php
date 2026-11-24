<?php

return [
    'notification' => [
        'title' => 'Kategoria imesasishwa',
        'body'  => 'Kategoria imesasishwa kikamilifu.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Kategoria imefutwa',
                    'body'  => 'Kategoria imefutwa kikamilifu.',
                ],

                'error' => [
                    'title' => 'Kategoria haikuweza kufutwa',
                    'body'  => 'Kategoria haiwezi kufutwa kwa sababu inatumika kwa sasa.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'Kusasisha kategoria kumeshindwa',
            ],
        ],
    ],
];
