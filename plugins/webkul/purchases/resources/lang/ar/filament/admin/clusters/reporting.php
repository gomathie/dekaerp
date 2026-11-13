<?php

return [
    'navigation'      => 'التقارير',
    'export-complete' => 'ملف تصدير المشتريات جاهز: :count صف.',

    'analysis' => [
        'title'      => 'تحليل المشتريات',
        'subheading' => 'أوامر الشراء المؤكدة بعملة الشركة. جمّع حسب المورد أو المشتري أو الشهر لرؤية الإجماليات.',
        'export'     => 'تصدير',
        'sum'        => 'الإجمالي',
        'columns'    => [
            'reference' => 'الأمر',
            'date'      => 'التاريخ',
            'supplier'  => 'المورد',
            'buyer'     => 'المشتري',
            'untaxed'   => 'بدون ضريبة',
            'total'     => 'الإجمالي',
        ],
        'groups' => [
            'supplier' => 'المورد',
            'buyer'    => 'المشتري',
            'month'    => 'الشهر',
        ],
        'filters' => [
            'from'  => 'من',
            'until' => 'إلى',
        ],
    ],
];
