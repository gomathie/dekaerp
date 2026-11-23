<?php

return [
    'label' => 'Send PO Na Barua pepe',

    'form' => [
        'fields' => [
            'to'      => 'Hadi',
            'subject' => 'Subject',
            'message' => 'Ujumbe',
        ],
    ],

    'action' => [
        'notification' => [
            'success' => [
                'title' => 'Barua pepe imetumwa',
                'body'  => 'barua pepe ina imetumwa successfully.',
            ],

            'warning' => [
                'title' => 'Baadhi emails zilikuwa si imetumwa',
                'body'  => 'Baadhi wasambazaji ita si pata barua pepe kwa sababu yao barua pepe anwani ni si inapatikana.',
            ],

            'danger' => [
                'title' => 'Barua pepe si imetumwa',
                'body'  => 'Tafadhali ongeza barua pepe anwani hadi imechaguliwa wasambazaji na jaribu tena.',
            ],
        ],
    ],
];
