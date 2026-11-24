<?php

return [
    'notification' => [
        'title' => 'Mtumiaji amesasishwa',
        'body'  => 'Mtumiaji amesasishwa kwa mafanikio.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Badilisha nenosiri',

            'notification' => [
                'title' => 'Nenosiri limebadilishwa',
                'body'  => 'Nenosiri limebadilishwa kwa mafanikio.',
            ],

            'form' => [
                'new-password'         => 'Nenosiri jipya',
                'confirm-new-password' => 'Thibitisha nenosiri jipya',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Mtumiaji amefutwa',
                'body'  => 'Mtumiaji amefutwa kwa mafanikio.',
                'error' => [
                    'title' => 'Mtumiaji hawezi kufutwa',
                    'body'  => 'Huyu ni mtumiaji chaguomsingi, au huwezi kujifuta mwenyewe.',
                ],
            ],
        ],
    ],
];
