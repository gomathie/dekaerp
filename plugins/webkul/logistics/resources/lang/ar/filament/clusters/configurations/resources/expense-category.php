<?php

return [
    'navigation' => [
        'title' => 'فئات المصروفات',
    ],

    'form' => [
        'fields' => [
            'requires-receipt'         => 'الإيصال مطلوب',
            'is-subcontracting'        => 'تكلفة مقاول من الباطن أو ناقل',
            'is-subcontracting-helper' => 'تُدفع تكاليف هذه الفئة إلى مورد (ناقل أو وكيل) وتُفوتر لكل مورد.',
        ],
    ],

    'table' => [
        'columns' => [
            'requires-receipt'  => 'الإيصال مطلوب',
            'is-subcontracting' => 'التعاقد من الباطن',
        ],
    ],
];
