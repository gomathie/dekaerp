<?php

return [
    'header-actions' => [
        'print' => [
            'label' => 'Chapisha',

            'actions' => [
                'without-content' => [
                    'label' => 'Chapisha Msimbo pau',
                ],

                'with-content' => [
                    'label' => 'Chapisha Msimbo pau Na Content',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Kifurushi Imefutwa',
                    'body'  => 'kifurushi ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Kifurushi could si kuwa imefutwa',
                    'body'  => 'kifurushi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],
            ],
        ],
    ],
];
