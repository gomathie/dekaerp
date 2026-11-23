<?php

return [
    'navigation' => [
        'title' => 'Расход Категории',
    ],

    'form' => [
        'fields' => [
            'requires-receipt'         => 'Приход обязательно',
            'is-subcontracting'        => 'Subcontractor или carrier стоимость',
            'is-subcontracting-helper' => 'Затраты в этот категория оплачен до поставщик (carrier, agent) и billed за поставщик.',
        ],
    ],

    'table' => [
        'columns' => [
            'requires-receipt'  => 'Приход обязательно',
            'is-subcontracting' => 'Subcontracting',
        ],
    ],
];
