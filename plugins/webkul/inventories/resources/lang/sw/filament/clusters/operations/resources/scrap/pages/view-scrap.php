<?php

return [
    'header-actions' => [
        'validate' => [
            'label' => 'Thibitisha',

            'notification' => [
                'warning' => [
                    'title' => 'Bidhaa hazitoshi',
                    'body'  => 'Bidhaa ghalani hazitoshi kuthibitisha rekodi hii ya chakavu.',
                ],

                'success' => [
                    'title' => 'Rekodi ya chakavu imekamilishwa',
                    'body'  => 'Rekodi ya chakavu imekamilishwa kwa mafanikio.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Rekodi ya chakavu imefutwa',
                    'body'  => 'Rekodi ya chakavu imefutwa kwa mafanikio.',
                ],

                'error' => [
                    'title' => 'Rekodi za chakavu hazikuweza kufutwa',
                    'body'  => 'Rekodi za chakavu haziwezi kufutwa kwa sababu zinatumika.',
                ],
            ],
        ],
    ],
];
