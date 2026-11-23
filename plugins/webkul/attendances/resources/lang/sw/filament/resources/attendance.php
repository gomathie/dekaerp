<?php

return [
    'model' => [
        'label'  => 'Mahudhurio rekodi',
        'plural' => 'Mahudhurio',
    ],
    'navigation' => [
        'title' => 'Mahudhurio',
    ],
    'table' => [
        'columns' => [
            'employee'   => 'Mfanyakazi',
            'check-in'   => 'Clock katika',
            'check-out'  => 'Clock nje',
            'worked'     => 'Worked',
            'review'     => 'Needs review',
            'on-leave'   => 'Washa likizo',
            'source'     => 'Chanzo katika',
            'source-out' => 'Chanzo nje',
            'company'    => 'Kampuni',
            'note'       => 'Maelezo',
            'latitude'   => 'Latitude (katika)',
            'longitude'  => 'Longitude (katika)',
            'ip'         => 'IP anwani (katika)',
        ],
        'filters' => [
            'open'         => 'Clocked katika sasa',
            'needs-review' => 'Needs review',
            'employee'     => 'Mfanyakazi',
            'day'          => 'Siku',
            'on'           => 'Washa',
        ],
    ],
    'actions' => [
        'export'  => 'Hamisha',
        'correct' => [
            'label'        => 'Correct mahudhurio',
            'heading'      => 'Correct hii mahudhurio',
            'description'  => 'badilisha ni recorded na yako jina na sababu wewe give.',
            'fields'       => [
                'check-in'    => 'Clock katika',
                'check-out'   => 'Clock nje',
                'note'        => 'Maelezo',
                'reason'      => 'Sababu',
                'reason-help' => 'Inahitajika. Say nini ilikuwa wrong na nini wewe imebadilishwa.',
            ],
            'notification' => 'Mahudhurio corrected.',
        ],
    ],
    'export-complete' => ':count mahudhurio mstari(s) exported.',
];
