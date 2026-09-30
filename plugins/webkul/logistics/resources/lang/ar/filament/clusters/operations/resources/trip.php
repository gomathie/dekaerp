<?php

return [
    'navigation' => [
        'title' => 'الرحلات',
    ],

    'form' => [
        'sections' => [
            'crew'     => 'المركبة والطاقم',
            'schedule' => 'الجدول الزمني',
        ],

        'fields' => [
            'company'            => 'الشركة',
            'number'             => 'رقم الرحلة',
            'number-placeholder' => 'يُعيّن تلقائياً',
            'vehicle'            => 'المركبة',
            'driver'             => 'السائق',
            'dispatcher'         => 'مسؤول الإرسال',
            'planned-start-at'   => 'البدء المخطط',
            'planned-end-at'     => 'الانتهاء المخطط',
            'odometer-start'     => 'عداد المسافات عند البدء',
            'odometer-end'       => 'عداد المسافات عند الانتهاء',
            'notes'              => 'ملاحظات',
        ],
    ],

    'table' => [
        'columns' => [
            'number'           => 'الرحلة',
            'state'            => 'الحالة',
            'vehicle'          => 'المركبة',
            'driver'           => 'السائق',
            'shipments'        => 'الشحنات',
            'planned-start-at' => 'البدء المخطط',
            'company'          => 'الشركة',
        ],

        'filters' => [
            'state'   => 'الحالة',
            'vehicle' => 'المركبة',
            'driver'  => 'السائق',
        ],
    ],

    'infolist' => [
        'summary'         => 'الرحلة',
        'schedule'        => 'الجدول الزمني',
        'load'            => 'الحمولة',
        'actual-start-at' => 'البدء الفعلي',
        'actual-end-at'   => 'الانتهاء الفعلي',
        'capacity'        => 'السعة',
        'capacity-ok'     => 'ضمن سعة المركبة.',
    ],

    'relations' => [
        'shipments' => [
            'title'   => 'الشحنات',
            'columns' => [
                'number'   => 'الشحنة',
                'state'    => 'الحالة',
                'customer' => 'العميل',
                'weight'   => 'الوزن (كجم)',
                'volume'   => 'الحجم (م³)',
            ],
            'actions' => [
                'attach' => [
                    'label'        => 'إضافة شحنة',
                    'field'        => 'الشحنة المؤكدة',
                    'notification' => 'تمت إضافة الشحنة إلى هذه الرحلة.',
                ],
            ],
        ],
    ],

    'actions' => [
        'dispatchTrip' => [
            'label'        => 'إرسال',
            'heading'      => 'إرسال هذه الرحلة؟',
            'notification' => 'تم إرسال الرحلة.',
        ],
        'startTrip' => [
            'label'        => 'بدء',
            'heading'      => 'بدء هذه الرحلة؟',
            'notification' => 'بدأت الرحلة.',
        ],
        'completeTrip' => [
            'label'        => 'إكمال',
            'heading'      => 'إكمال هذه الرحلة؟',
            'notification' => 'اكتملت الرحلة.',
        ],
    ],
];
