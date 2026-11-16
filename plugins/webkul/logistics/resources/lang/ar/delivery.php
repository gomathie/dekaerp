<?php

return [
    'actions' => [
        'pickup' => [
            'label'                => 'تسجيل الاستلام',
            'notification'         => 'تم تسجيل استلام الشحنة',
            'transit-label'        => 'بدء النقل',
            'transit-notification' => 'تم تحديد الشحنة كقيد النقل',
        ],
        'deliver' => [
            'label'                         => 'تسليم الشحنة',
            'notification'                  => 'تم تسليم الشحنة',
            'stop-notification'             => 'تم تسجيل محطة التسليم',
            'out-for-delivery-label'        => 'تحديد كخارجة للتسليم',
            'out-for-delivery-notification' => 'تم تحديد الشحنة كخارجة للتسليم',
            'fields'                        => [
                'capture-pod'    => 'تسجيل إثبات التسليم',
                'stop'           => 'محطة التسليم',
                'recipient-name' => 'اسم المستلم',
                'received-at'    => 'وقت الاستلام',
                'reference'      => 'المرجع',
                'notes'          => 'ملاحظات',
                'photo'          => 'صورة التسليم',
                'signature'      => 'توقيع المستلم',
            ],
        ],
        'fail' => [
            'label'               => 'تسجيل فشل التسليم',
            'reason'              => 'السبب',
            'notification'        => 'تم تسجيل فشل التسليم',
            'resolve-label'       => 'معالجة فشل التسليم',
            'resolution'          => 'الخطوة التالية',
            'retry-notification'  => 'أصبحت الشحنة جاهزة لمحاولة تسليم أخرى',
            'return-notification' => 'تم تحديد الشحنة كمُرتجعة',
            'resolutions'         => [
                'retry'  => 'إعادة محاولة التسليم',
                'return' => 'تحديد كمُرتجعة',
            ],
        ],
    ],
    'relation-manager' => [
        'title'   => 'إثبات التسليم',
        'columns' => [
            'recipient'    => 'المستلم',
            'received-at'  => 'وقت الاستلام',
            'stop'         => 'محطة التوقف',
            'captured-via' => 'وسيلة التسجيل',
            'photo'        => 'الصورة',
            'signature'    => 'التوقيع',
        ],
    ],
    'validation' => [
        'pod-required'        => 'يجب تقديم إثبات التسليم قبل تحديد هذه الشحنة كمسلّمة.',
        'stop-required'       => 'اختر محطة التسليم التي يتبع لها هذا الإثبات.',
        'stop-unavailable'    => 'لم تعد محطة التسليم المحددة متاحة.',
        'upload-failed'       => 'تعذر تخزين ملف الإثبات. يرجى المحاولة مرة أخرى.',
        'resolution-required' => 'اختر إعادة المحاولة أو إرجاع الشحنة بعد فشل التسليم.',
    ],
];
