<?php

return [
    'header-actions' => [
        'validate' => [
            'label' => 'Validate',

            'notification' => [
                'warning' => [
                    'title' => 'Insufficient hisa',
                    'body'  => 'scrap ina insufficient hisa hadi validate.',
                ],

                'success' => [
                    'title' => 'Scrap marked kama imekamilika',
                    'body'  => 'scrap ina marked kama imekamilika successfully.',
                ],
            ],
        ],

        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Scrap Imefutwa',
                    'body'  => 'scrap ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Scraps could si kuwa imefutwa',
                    'body'  => 'scraps haiwezekani kuwa imefutwa kwa sababu wao ni currently katika tumia.',
                ],
            ],
        ],
    ],
];
