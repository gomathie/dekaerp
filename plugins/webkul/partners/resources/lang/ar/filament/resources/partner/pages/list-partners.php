<?php

return [
    'title' => 'جهات الاتصال',

    'header-actions' => [
        'create' => [
            'label' => 'إنشاء جهة اتصال',
        ],

        'import' => [
            'label' => 'استيراد من Excel',

            'modal' => [
                'heading'                 => 'استيراد جهات الاتصال من ملف جدول بيانات',
                'description'             => 'نزّل الملف النموذجي، واملأ جهة اتصال واحدة في كل صف، ثم ارفعه هنا. حتى :max جهة اتصال في كل عملية رفع.',
                'submit'                  => 'استيراد جهات الاتصال',
                'download-template-csv'   => 'تنزيل نموذج (CSV)',
                'download-template-excel' => 'تنزيل نموذج (Excel)',

                'file' => [
                    'label'  => 'الملف المكتمل',
                    'helper' => 'ملف Excel (.xlsx أو .xls) أو CSV. اترك صف العناوين كما هو. العمود المطلوب: :columns. يتم الإبلاغ عن الصفوف التي بها مشكلة برقم الصف، ويتم استيراد بقية الصفوف.',
                ],
            ],

            'notifications' => [
                'imported'         => 'تم استيراد :count جهة اتصال',
                'skipped'          => 'تم تخطي :count صف:',
                'nothing-imported' => 'لم يتم استيراد أي جهة اتصال',
                'more-errors'      => '... و:count صف إضافي.',
                'unreadable'       => 'تعذّر قراءة هذا الملف. يرجى رفع ملف Excel أو CSV.',
            ],
        ],
    ],

    'tabs' => [
        'individuals' => 'أفراد',
        'companies'   => 'شركات',
        'employees'   => 'موظفون',
        'customers'   => 'العملاء',
        'vendors'     => 'المورّدون',
        'archived'    => 'مؤرشف',
    ],
];
