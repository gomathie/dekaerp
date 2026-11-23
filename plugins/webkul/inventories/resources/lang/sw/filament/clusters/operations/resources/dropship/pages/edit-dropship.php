<?php

return [
    'notification' => [
        'title' => 'Dropship imesasishwa',
        'body'  => 'dropship ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Dropship imefutwa',
                    'body'  => 'dropship ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Dropship could si kuwa imefutwa',
                    'body'  => 'dropship haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],
            ],
        ],
    ],
];
