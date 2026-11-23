<?php

return [
    'notification' => [
        'title' => 'Stakabadhi imesasishwa',
        'body'  => 'stakabadhi ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Stakabadhi imefutwa',
                    'body'  => 'stakabadhi ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Stakabadhi could si kuwa imefutwa',
                    'body'  => 'Stakabadhi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],
            ],
        ],
    ],
];
