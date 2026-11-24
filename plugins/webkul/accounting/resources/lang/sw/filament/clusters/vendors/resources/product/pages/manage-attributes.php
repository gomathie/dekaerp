<?php

return [
    'title' => 'Sifa',

    'form' => [
        'attribute' => 'Sifa',
        'values'    => 'Thamani',
    ],

    'table' => [
        'description' => 'Onyo: kuongeza au kufuta sifa kutafuta na kuunda upya aina zilizopo, na marekebisho yake yatapotea.',

        'header-actions' => [
            'create' => [
                'label' => 'Ongeza sifa',

                'notification' => [
                    'title' => 'Sifa imeundwa',
                    'body'  => 'Sifa imeundwa.',
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
                    'body'  => 'Sifa imesasishwa.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sifa imefutwa',
                    'body'  => 'Sifa imefutwa.',
                ],
            ],
        ],
    ],
];
