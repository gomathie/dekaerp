<?php

return [
    'navigation' => [
        'title' => 'Industries',
    ],

    'form' => [
        'name'      => 'Jina',
        'full-name' => 'Kamili Jina',
    ],

    'table' => [
        'columns' => [
            'name'       => 'Jina',
            'full-name'  => 'Kamili Jina',
            'created-at' => 'Imeundwa',
            'updated-at' => 'Imesasishwa',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Industry imesasishwa',
                    'body'  => 'industry ina imesasishwa successfully.',
                ],
            ],

            'restore' => [
                'notification' => [
                    'title' => 'Industry restored',
                    'body'  => 'industry ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Industry imefutwa',
                    'body'  => 'industry ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Industry force imefutwa',
                    'body'  => 'industry ina force imefutwa successfully.',
                ],
            ],
        ],

        'bulk-actions' => [
            'restore' => [
                'notification' => [
                    'title' => 'Industries restored',
                    'body'  => 'industries ina restored successfully.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Industries imefutwa',
                    'body'  => 'industries ina imefutwa successfully.',
                ],
            ],

            'force-delete' => [
                'notification' => [
                    'title' => 'Industries force imefutwa',
                    'body'  => 'industries ina force imefutwa successfully.',
                ],
            ],
        ],
    ],
];
