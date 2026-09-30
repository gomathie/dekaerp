<?php

return [
    'navigation' => [
        'title' => 'السائقون',
    ],

    'form' => [
        'fields' => [
            'company'            => 'الشركة',
            'employee'           => 'الموظف',
            'partner'            => 'جهة اتصال الناقل',
            'name'               => 'الاسم',
            'phone'              => 'الهاتف',
            'license-number'     => 'رقم رخصة القيادة',
            'license-class'      => 'فئة رخصة القيادة',
            'license-expires-at' => 'انتهاء رخصة القيادة',
            'is-active'          => 'نشط',
        ],
    ],

    'table' => [
        'columns' => [
            'name'               => 'الاسم',
            'phone'              => 'الهاتف',
            'employee'           => 'الموظف',
            'partner'            => 'جهة اتصال الناقل',
            'license-number'     => 'رقم رخصة القيادة',
            'license-expires-at' => 'انتهاء رخصة القيادة',
            'company'            => 'الشركة',
            'is-active'          => 'نشط',
        ],

        'filters' => [
            'license-attention' => 'منتهية أو ستنتهي قريباً',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general' => 'السائق',
            'license' => 'رخصة القيادة',
        ],
    ],

    'actions' => [
        'add-drivers-from-employees' => [
            'label' => 'إضافة سائقين من الموظفين',

            'fields' => [
                'employees' => 'الموظفون',
            ],

            'notification' => [
                'title' => 'اكتمل استيراد الموظفين',
                'body'  => 'تم إنشاء :created وتخطي :skipped.',
            ],
        ],
    ],
];
