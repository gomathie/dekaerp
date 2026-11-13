<?php

return [
    'navigation'      => 'التقارير',
    'export-complete' => 'ملف تصدير المبيعات جاهز: :count صف.',

    'analysis' => [
        'title'      => 'تحليل المبيعات',
        'subheading' => 'الطلبات المؤكدة بعملة الشركة. جمّع حسب العميل أو مندوب المبيعات أو الشهر لرؤية الإجماليات.',
        'export'     => 'تصدير',
        'sum'        => 'الإجمالي',
        'columns'    => [
            'reference'   => 'الطلب',
            'date'        => 'التاريخ',
            'customer'    => 'العميل',
            'salesperson' => 'مندوب المبيعات',
            'untaxed'     => 'بدون ضريبة',
            'total'       => 'الإجمالي',
        ],
        'groups' => [
            'customer'    => 'العميل',
            'salesperson' => 'مندوب المبيعات',
            'month'       => 'الشهر',
        ],
        'filters' => [
            'from'  => 'من',
            'until' => 'إلى',
        ],
    ],
];
