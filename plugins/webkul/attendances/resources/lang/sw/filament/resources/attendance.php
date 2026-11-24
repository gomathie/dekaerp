<?php

return [
    'model' => [
        'label'  => 'Rekodi ya mahudhurio',
        'plural' => 'Mahudhurio',
    ],
    'navigation' => [
        'title' => 'Mahudhurio',
    ],
    'table' => [
        'columns' => [
            'employee'   => 'Mfanyakazi',
            'check-in'   => 'Kuingia',
            'check-out'  => 'Kutoka',
            'worked'     => 'Muda wa kazi',
            'review'     => 'Inahitaji ukaguzi',
            'on-leave'   => 'Yuko likizoni',
            'source'     => 'Chanzo cha kuingia',
            'source-out' => 'Chanzo cha kutoka',
            'company'    => 'Kampuni',
            'note'       => 'Kidokezo',
            'latitude'   => 'Latitudo (kuingia)',
            'longitude'  => 'Longitudo (kuingia)',
            'ip'         => 'Anwani ya IP (kuingia)',
        ],
        'filters' => [
            'open'         => 'Yuko kazini sasa',
            'needs-review' => 'Inahitaji ukaguzi',
            'employee'     => 'Mfanyakazi',
            'day'          => 'Siku',
            'on'           => 'Tarehe',
        ],
    ],
    'actions' => [
        'export'  => 'Hamisha',
        'correct' => [
            'label'        => 'Sahihisha mahudhurio',
            'heading'      => 'Sahihisha mahudhurio haya',
            'description'  => 'Badiliko hurekodiwa pamoja na jina lako na sababu unayotoa.',
            'fields'       => [
                'check-in'    => 'Kuingia',
                'check-out'   => 'Kutoka',
                'note'        => 'Kidokezo',
                'reason'      => 'Sababu',
                'reason-help' => 'Lazima. Eleza kilichokuwa kibaya na ulichobadilisha.',
            ],
            'notification' => 'Mahudhurio yamesahihishwa.',
        ],
    ],
    'export-complete' => 'Safu :count za mahudhurio zimehamishwa.',
];
