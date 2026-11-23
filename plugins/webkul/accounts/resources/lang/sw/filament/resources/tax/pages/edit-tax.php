<?php

return [
    'notification' => [
        'title' => 'Kodi imesasishwa',
        'body'  => 'kodi ina imesasishwa successfully.',
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'success' => [
                    'title' => 'Kodi imefutwa',
                    'body'  => 'kodi ina imefutwa successfully.',
                ],

                'error' => [
                    'title' => 'Kodi could si kuwa imefutwa',
                    'body'  => 'kodi haiwezekani kuwa imefutwa kwa sababu hii ni currently katika tumia.',
                ],

                'invalid-repartition-lines' => [
                    'title' => 'Si halali Repartition Mistari',
                ],
            ],
        ],
    ],
];
