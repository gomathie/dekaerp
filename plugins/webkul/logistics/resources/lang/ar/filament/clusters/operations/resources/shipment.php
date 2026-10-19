<?php

return [
    'navigation' => [
        'title' => 'الشحنات',
    ],

    'global-search' => [
        'customer'  => 'العميل',
        'state'     => 'الحالة',
        'reference' => 'مرجع العميل',
    ],

    'form' => [
        'tabs' => [
            'general'    => 'عام',
            'route'      => 'المسار ومحطات التوقف',
            'cargo'      => 'الحمولة',
            'assignment' => 'التعيين',
        ],

        'fields' => [
            'number'                 => 'رقم الشحنة',
            'number-placeholder'     => 'يُعيّن عند حفظ الشحنة',
            'company'                => 'الشركة',
            'customer'               => 'العميل',
            'customer-reference'     => 'مرجع العميل',
            'sale-order'             => 'من أمر البيع',
            'sale-order-helper'      => 'ينسخ العملة ومرجع العميل من أمر بيع لهذا العميل.',
            'service-type'           => 'نوع الخدمة',
            'transport-mode'         => 'وسيلة النقل',
            'priority'               => 'الأولوية',
            'currency'               => 'العملة',
            'origin'                 => 'نقطة الانطلاق',
            'destination'            => 'الوجهة',
            'pickup-address'         => 'عنوان الاستلام',
            'delivery-address'       => 'عنوان التسليم',
            'planned-pickup-at'      => 'الاستلام المخطط',
            'expected-delivery-at'   => 'التسليم المتوقع',
            'stops'                  => 'محطات التوقف',
            'stop-type'              => 'النوع',
            'stop-address'           => 'العنوان',
            'stop-contact'           => 'جهة الاتصال',
            'stop-phone'             => 'الهاتف',
            'stop-planned-arrival'   => 'الوصول المخطط',
            'stop-instructions'      => 'التعليمات',
            'declared-value'         => 'القيمة المصرّح بها',
            'is-fragile'             => 'قابل للكسر',
            'is-hazardous'           => 'خطرة',
            'is-hazardous-helper'    => 'للتنبيه فقط. لا تتم معالجة مستندات البضائع الخطرة هنا.',
            'cargo-lines'            => 'الحمولة',
            'line-description'       => 'الوصف',
            'line-package-type'      => 'نوع الطرد',
            'line-quantity'          => 'الكمية',
            'line-weight'            => 'الوزن',
            'line-volume'            => 'الحجم',
            'instructions'           => 'تعليمات خاصة',
            'dispatcher'             => 'مسؤول الإرسال',
            'carrier'                => 'الناقل',
            'carrier-helper'         => 'للنقل المتعاقد عليه من الباطن. تُسجل تكاليف الناقل كمصروفات.',
            'carrier-reference'      => 'مرجع الناقل',
            'waybill-no'             => 'رقم بوليصة الشحن',
        ],
    ],

    'table' => [
        'columns' => [
            'number'               => 'الرقم',
            'customer'             => 'العميل',
            'state'                => 'الحالة',
            'planned-pickup-at'    => 'الاستلام المخطط',
            'expected-delivery-at' => 'التسليم المتوقع',
            'service-type'         => 'الخدمة',
            'packages'             => 'الطرود',
            'dispatcher'           => 'مسؤول الإرسال',
            'company'              => 'الشركة',
        ],

        'filters' => [
            'state'         => 'الحالة',
            'customer'      => 'العميل',
            'service-type'  => 'نوع الخدمة',
            'pickup-from'   => 'الاستلام من',
            'pickup-until'  => 'الاستلام حتى',
        ],
    ],

    'infolist' => [
        'summary'            => 'الشحنة',
        'route'              => 'المسار',
        'cargo'              => 'الحمولة',
        'assignment'         => 'التعيين',
        'actual-pickup-at'   => 'الاستلام الفعلي',
        'actual-delivery-at' => 'التسليم الفعلي',
        'total-weight'       => 'الوزن الإجمالي',
        'total-volume'       => 'الحجم الإجمالي',
    ],

    'timeline' => [
        'title'   => 'الخط الزمني',
        'columns' => [
            'occurred-at' => 'الوقت',
            'type'        => 'الحدث',
            'source'      => 'المصدر',
            'user'        => 'بواسطة',
            'location'    => 'الموقع',
            'notes'       => 'ملاحظات',
        ],
    ],

    'actions' => [
        'confirm' => [
            'label'        => 'تأكيد',
            'heading'      => 'تأكيد هذه الشحنة؟',
            'notification' => 'تم تأكيد الشحنة',
        ],

        'hold' => [
            'label'        => 'تعليق',
            'heading'      => 'تعليق هذه الشحنة؟',
            'reason'       => 'السبب',
            'notification' => 'تم تعليق الشحنة',
        ],

        'release' => [
            'label'        => 'إلغاء التعليق',
            'heading'      => 'إلغاء تعليق هذه الشحنة؟',
            'description'  => 'تعود الشحنة إلى حالة مؤكدة ويمكن تعيينها لرحلة مرة أخرى.',
            'notification' => 'تم إلغاء تعليق الشحنة',
        ],

        'cancel' => [
            'label'        => 'إلغاء الشحنة',
            'heading'      => 'إلغاء هذه الشحنة؟',
            'description'  => 'لا يمكن إعادة فتح الشحنة الملغاة. تبقى رسومها كمرجع.',
            'reason'       => 'السبب',
            'notification' => 'تم إلغاء الشحنة',
        ],
    ],
];
