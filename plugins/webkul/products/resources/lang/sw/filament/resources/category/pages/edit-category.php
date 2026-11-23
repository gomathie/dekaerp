<?php

return [
    'notification' => [
        'title' => 'Kategoria imesasishwa',
        'body'  => 'kategoria ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Kategoria imefutwa',
                    'body'  => 'kategoria ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Kategoria could si kuwa imefutwa',
                    'body'  => 'kategoria haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],
            ],
        ],
    ],

    'save' => [
        'notification' => [
            'error' => [
                'title' => 'Kategoria imesasishwa imeshindwa',
            ],
        ],
    ],
];
