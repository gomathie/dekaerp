<?php

return [
    'notification' => [
        'title'              => 'Likizo imesasishwa',
        'body'               => 'likizo ina imesasishwa successfully.',
        'action_not_allowed' => [
            'title' => 'Kitendo Si Inaruhusiwa',
            'body'  => 'Wewe haiwezekani modify hii likizo request kama hii ni katika imefungwa hali.',
        ],
        'overlap' => [
            'title' => 'Overlapping Likizo Request',
            'body'  => 'imechaguliwa likizo dates overlap na existing request. Tafadhali chagua different dates.',
        ],
    ],

    'header-actions' => [
        'delete' => [
            'notification' => [
                'title' => 'Likizo imefutwa',
                'body'  => 'likizo ina imefutwa successfully.',
            ],
        ],
    ],
];
