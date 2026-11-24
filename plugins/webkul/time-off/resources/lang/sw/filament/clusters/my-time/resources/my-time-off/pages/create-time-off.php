<?php

return [
    'notification' => [
        'success' => [
            'title' => 'Likizo imeundwa',
            'body'  => 'Likizo imeundwa kwa mafanikio.',
        ],

        'overlap' => [
            'title' => 'Ombi la likizo linaingiliana',
            'body'  => 'Tarehe za likizo zilizochaguliwa zinaingiliana na ombi lililopo. Tafadhali chagua tarehe nyingine.',
        ],

        'warning' => [
            'title' => 'Huna akaunti ya mfanyakazi',
            'body'  => 'Huna akaunti ya mfanyakazi. Tafadhali wasiliana na msimamizi wako.',
        ],

        'invalid_half_day_leave' => [
            'title' => 'Ombi la likizo si sahihi',
            'body'  => 'Likizo ya nusu siku inaweza kuombwa kwa siku moja tu.',
        ],

        'leave_request_denied_no_allocation' => [
            'title' => 'Ombi la likizo limekataliwa',
            'body'  => 'Huna likizo yoyote uliyogawiwa kwa :leaveType.',
        ],

        'leave_request_denied_insufficient_balance' => [
            'title' => 'Ombi la likizo limekataliwa',
            'body'  => 'Salio la likizo halitoshi. Una siku :available_balance zinazopatikana. Zilizoombwa: siku :requested_days.',
        ],
    ],
];
