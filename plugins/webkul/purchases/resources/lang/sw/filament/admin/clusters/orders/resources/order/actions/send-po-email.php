<?php

return [
    'label' => 'Tuma oda kwa barua pepe',

    'form' => [
        'fields' => [
            'to'      => 'Kwa',
            'subject' => 'Mada',
            'message' => 'Ujumbe',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'Barua pepe imetumwa',
                'body'  => 'Barua pepe imetumwa.',
            ],

            'warning' => [
                'title' => 'Baadhi ya barua pepe hazikutumwa',
                'body'  => 'Baadhi ya wasambazaji hawatapata barua pepe kwa sababu hawana anwani ya barua pepe.',
            ],

            'danger' => [
                'title' => 'Barua pepe haikutumwa',
                'body'  => 'Ongeza anwani ya barua pepe kwa wasambazaji uliowachagua kisha ujaribu tena.',
            ],
        ],
    ],
];
