<?php

return [
    'notification' => [
        'title' => 'Kundi imesasishwa',
        'body'  => 'kundi ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Kundi imefutwa',
                    'body'  => 'kundi ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Kundi could si kuwa imefutwa',
                    'body'  => 'kundi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],
            ],
        ],
    ],
];
