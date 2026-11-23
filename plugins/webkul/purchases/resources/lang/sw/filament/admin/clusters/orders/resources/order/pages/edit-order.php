<?php

return [
    'notification' => [
        'title' => 'Agizo imesasishwa',
        'body'  => 'agizo ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'confirm' => [
            'label' => 'Thibitisha',
        ],

        'close' => [
            'label' => 'Funga',
        ],

        'cancel' => [
            'label' => 'Ghairi',
        ],

        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Agizo imefutwa',
                    'body'  => 'agizo ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Agizo could si kuwa imefutwa',
                    'body'  => 'agizo haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],
            ],
        ],
    ],
];
