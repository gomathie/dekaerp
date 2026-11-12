<?php

return [
    'model' => [
        'label'  => 'سجل حضور',
        'plural' => 'الحضور',
    ],
    'navigation' => [
        'title' => 'الحضور',
    ],
    'table' => [
        'columns' => [
            'employee'   => 'الموظف',
            'check-in'   => 'الحضور',
            'check-out'  => 'الانصراف',
            'worked'     => 'ساعات العمل',
            'review'     => 'يحتاج مراجعة',
            'on-leave'   => 'في إجازة',
            'source'     => 'مصدر الحضور',
            'source-out' => 'مصدر الانصراف',
            'company'    => 'الشركة',
            'note'       => 'ملاحظة',
            'latitude'   => 'خط العرض (الحضور)',
            'longitude'  => 'خط الطول (الحضور)',
            'ip'         => 'عنوان IP (الحضور)',
        ],
        'filters' => [
            'open'         => 'مسجّلون الآن',
            'needs-review' => 'يحتاج مراجعة',
            'employee'     => 'الموظف',
            'day'          => 'اليوم',
            'on'           => 'في',
        ],
    ],
    'actions' => [
        'export'  => 'تصدير',
        'correct' => [
            'label'        => 'تصحيح الحضور',
            'heading'      => 'تصحيح هذا السجل',
            'description'  => 'يُسجَّل التغيير باسمك مع السبب الذي تذكره.',
            'fields'       => [
                'check-in'    => 'الحضور',
                'check-out'   => 'الانصراف',
                'note'        => 'ملاحظة',
                'reason'      => 'السبب',
                'reason-help' => 'إلزامي. اذكر ما كان خطأ وما غيّرته.',
            ],
            'notification' => 'تم تصحيح الحضور.',
        ],
    ],
    'export-complete' => 'تم تصدير :count سجل حضور.',
];
