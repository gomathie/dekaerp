<?php

return [
    'notification' => [
        'title' => 'Mtumiaji imesasishwa',
        'body'  => 'mtumiaji ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'change-password' => [
            'label' => 'Badilisha Nenosiri',

            'notification' => [
                'title' => 'Nenosiri imebadilishwa',
                'body'  => 'nenosiri ina imebadilishwa successfully.',
            ],

            'form' => [
                'new-password'         => 'Mpya Nenosiri',
                'confirm-new-password' => 'Thibitisha Mpya Nenosiri',
            ],
        ],

        'delete' => [
            'notification' => [
                'title' => 'Mtumiaji imefutwa',
                'body'  => 'mtumiaji ina imefutwa successfully.',
                'error' => [
                    'title' => 'Mtumiaji Haiwezekani Kuwa Imefutwa',
                    'body'  => 'Hii ni chaguo-msingi mtumiaji au wewe haiwezekani futa yourself.',
                ],
            ],
        ],
    ],
];
