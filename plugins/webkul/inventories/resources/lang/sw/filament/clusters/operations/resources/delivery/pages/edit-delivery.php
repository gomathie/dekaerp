<?php

return [
    'notification' => [
        'title' => 'Usafirishaji imesasishwa',
        'body'  => 'usafirishaji ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Usafirishaji imefutwa',
                    'body'  => 'usafirishaji ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Usafirishaji could si kuwa imefutwa',
                    'body'  => 'usafirishaji haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],
            ],
        ],
    ],
];
