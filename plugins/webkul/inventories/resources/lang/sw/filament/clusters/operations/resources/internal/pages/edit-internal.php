<?php

return [
    'notification' => [
        'title' => 'Ndani Uhamisho imesasishwa',
        'body'  => 'ndani uhamisho ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Ndani Uhamisho imefutwa',
                    'body'  => 'ndani uhamisho ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Ndani Uhamisho could si kuwa imefutwa',
                    'body'  => 'ndani uhamisho haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],
            ],
        ],
    ],
];
