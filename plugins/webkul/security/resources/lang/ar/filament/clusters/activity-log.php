<?php

return [
    'title'           => 'سجل النشاط',
    'navigation'      => 'سجل النشاط',
    'group'           => 'عام',
    'system'          => 'النظام',
    'export'          => 'تصدير',
    'export-complete' => 'ملف تصدير سجل النشاط جاهز: :count صف.',

    'columns' => [
        'when'    => 'متى',
        'who'     => 'من',
        'record'  => 'السجل',
        'action'  => 'الإجراء',
        'changes' => 'التغييرات',
    ],

    'filters' => [
        'record-type' => 'نوع السجل',
        'from'        => 'من',
        'until'       => 'إلى',
    ],

    'events' => [
        'created'      => 'أُنشئ',
        'updated'      => 'عُدّل',
        'deleted'      => 'حُذف',
        'soft_deleted' => 'أُرشف',
        'hard_deleted' => 'حُذف',
        'restored'     => 'استُعيد',
        'reset'        => 'أعيد إلى المسودة',
        'cancelled'    => 'أُلغي',
    ],
];
