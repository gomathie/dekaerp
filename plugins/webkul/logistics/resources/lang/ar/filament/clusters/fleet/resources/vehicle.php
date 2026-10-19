<?php

return [
    'navigation' => [
        'title' => 'المركبات',
    ],

    'form' => [
        'fields' => [
            'registration-no'       => 'رقم التسجيل',
            'name'                  => 'الاسم',
            'company'               => 'الشركة',
            'vehicle-type'          => 'نوع المركبة',
            'ownership'             => 'الملكية',
            'carrier'               => 'الناقل',
            'default-driver'        => 'السائق الافتراضي',
            'equipment'             => 'معدات الصيانة',
            'telematics-device-ref' => 'مرجع جهاز التتبع',
            'capacity-kg'           => 'سعة الوزن',
            'capacity-m3'           => 'سعة الحجم',
            'is-active'             => 'نشط',
        ],
    ],

    'table' => [
        'columns' => [
            'registration-no'       => 'التسجيل',
            'name'                  => 'الاسم',
            'ownership'             => 'الملكية',
            'vehicle-type'          => 'النوع',
            'carrier'               => 'الناقل',
            'capacity-kg'           => 'السعة',
            'telematics-device-ref' => 'التتبع',
            'company'               => 'الشركة',
            'is-active'             => 'نشط',
        ],

        'filters' => [
            'ownership'    => 'الملكية',
            'vehicle-type' => 'نوع المركبة',
        ],
    ],

    'infolist' => [
        'sections' => [
            'general'      => 'المركبة',
            'capacity'     => 'السعة',
            'integrations' => 'عمليات التكامل',
        ],
    ],

    'actions' => [
        'import-maintenance-equipment' => [
            'label' => 'استيراد من معدات الصيانة',

            'fields' => [
                'equipment' => 'المعدة',
            ],

            'notification' => [
                'title' => 'اكتمل استيراد معدات الصيانة',
                'body'  => 'تم إنشاء :created وتخطي :skipped.',
            ],
        ],
    ],
];
