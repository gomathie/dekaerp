<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Likizo imeundwa',
            'body'  => 'likizo ina imeundwa successfully.',
        ],

        'overlap' => [
            'title' => 'Overlapping Likizo Request',
            'body'  => 'imechaguliwa likizo dates overlap na existing request. Tafadhali chagua different dates.',
        ],

        'warning' => [
            'title' => 'Wewe don\'t ina mfanyakazi akaunti',
            'body'  => 'Wewe don\'t ina mfanyakazi akaunti. Tafadhali mwasiliani yako msimamizi.',
        ],

        'invalid_half_day_leave' => [
            'title' => 'Si halali Likizo Request',
            'body'  => 'Half-siku likizo unaweza tu kuwa imetumika kwa single siku.',
        ],

        'leave_request_denied_no_allocation' => [
            'title' => 'Likizo Request Imekataliwa',
            'body'  => 'Wewe si ina yoyote allocated likizo kwa :leaveType.',
        ],

        'leave_request_denied_insufficient_balance' => [
            'title' => 'Likizo Request Imekataliwa',
            'body'  => 'Insufficient likizo salio. Wewe ina :available_balance siku(s) inapatikana. Requested: :requested_days siku(s).',
        ],
    ],
];
