<?php

return [
    'title' => 'Sifa',

    'form' => [
        'attribute' => 'Sifa',
        'values'    => 'Thamani',
    ],

    'table' => [
        'description' => 'Onyo: kuongeza au kufuta sifa kutafuta na kuunda upya aina za bidhaa zilizopo, na marekebisho yao yanaweza kupotea.',

        'header-actions' => [
            'create' => [
                'label' => 'Ongeza sifa',

                'notification' => [
                    'title' => 'Sifa imeundwa',
                    'body'  => 'Sifa imeundwa kikamilifu.',
                ],
            ],
        ],

        'columns' => [
            'attribute' => 'Sifa',
            'values'    => 'Thamani',
        ],

        'actions' => [
            'edit' => [
                'notification' => [
                    'title' => 'Sifa imesasishwa',
                    'body'  => 'Sifa imesasishwa kikamilifu.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sifa imefutwa',
                    'body'  => 'Sifa imefutwa kikamilifu.',
                ],
            ],
        ],
    ],
];
