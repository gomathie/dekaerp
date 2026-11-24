<?php

return [
    'title' => 'Sifa',

    'form' => [
        'attribute' => 'Sifa',
        'values'    => 'Thamani',
    ],

    'table' => [
        'description' => 'Tahadhari: kuongeza au kufuta sifa kutafuta na kuunda upya aina zilizopo, na marekebisho yake yanaweza kupotea.',

        'header-actions' => [
            'create' => [
                'label' => 'Ongeza sifa',

                'notification' => [
                    'title' => 'Sifa imeundwa',
                    'body'  => 'Sifa imeundwa kwa mafanikio.',
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
                    'body'  => 'Sifa imesasishwa kwa mafanikio.',
                ],
            ],

            'delete' => [
                'notification' => [
                    'title' => 'Sifa imefutwa',
                    'body'  => 'Sifa imefutwa kwa mafanikio.',
                ],
            ],
        ],
    ],
];
