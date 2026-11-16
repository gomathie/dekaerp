<?php

$dateFilters = [
    'from'    => 'من',
    'until'   => 'حتى',
    'between' => 'من :from إلى :until',
    'since'   => 'من :from',
    'up-to'   => 'حتى :until',
];

/*
 * Vehicle and driver trip history are the same table with a different filter in
 * front, so they share their column and filter labels rather than keeping two
 * copies to drift apart. TripHistoryExporter labels its file from the
 * vehicle-trips keys whichever page it was started from, which is only correct
 * while the two sets are identical - keep them so.
 */
$tripColumns = [
    'reference' => 'الرحلة',
    'vehicle'   => 'المركبة',
    'driver'    => 'السائق',
    'state'     => 'الحالة',
    'started'   => 'بدأت',
    'ended'     => 'انتهت',
    'shipments' => 'الشحنات',
    'distance'  => 'المسافة',
];

$tripFilters = $dateFilters + [
    'vehicle' => 'المركبة',
    'driver'  => 'السائق',
    'state'   => 'الحالة',
];

return [
    'export'          => 'تصدير',
    'export-complete' => 'تم تصدير :count صف.',

    'shipment-register' => [
        'title'  => 'سجل الشحنات',
        'export' => 'تصدير',

        'columns' => [
            'reference'   => 'الشحنة',
            'customer'    => 'العميل',
            'state'       => 'الحالة',
            'origin'      => 'من',
            'destination' => 'إلى',
            'expected'    => 'المتوقع',
            'delivered'   => 'تم التسليم',
            'company'     => 'الشركة',
        ],

        'filters' => $dateFilters + [
            'state'    => 'الحالة',
            'customer' => 'العميل',
        ],
    ],

    'delivery-performance' => [
        'title'      => 'أداء التسليم',
        'subheading' => 'يُقاس مقابل تاريخ التسليم المتوقع. لا تُقيّم الشحنات التي ليس لها تاريخ متوقع.',
        'export'     => 'تصدير',

        'columns' => [
            'reference' => 'الشحنة',
            'customer'  => 'العميل',
            'expected'  => 'المتوقع',
            'delivered' => 'تم التسليم',
            'outcome'   => 'النتيجة',
            'delay'     => 'مدة التأخير',
        ],

        'outcomes' => [
            'on-time'      => 'في الموعد',
            'late'         => 'متأخر',
            'failed'       => 'فشل',
            'overdue'      => 'متجاوز للموعد',
            'pending'      => 'قيد التنفيذ',
            'not-measured' => 'لم يُحدد موعد',
        ],

        'filters' => $dateFilters + [
            'customer'    => 'العميل',
            'late-only'   => 'المتأخرة فقط',
            'failed-only' => 'الفاشلة فقط',
        ],
    ],

    'profitability' => [
        'title'      => 'ربحية الشحنة',
        'subheading' => 'الرسوم القابلة للفوترة مقابل التكاليف المعتمدة، كل منها بعملة الشحنة. لا تُحتسب التكاليف المسودة.',
        'export'     => 'تصدير',

        'columns' => [
            'reference' => 'الشحنة',
            'customer'  => 'العميل',
            'revenue'   => 'الإيرادات',
            'costs'     => 'التكاليف',
            'margin'    => 'الهامش',
            'currency'  => 'العملة',
        ],

        'filters' => $dateFilters + [
            'customer'     => 'العميل',
            'loss-making'  => 'الخاسرة فقط',
        ],
    ],

    'vehicle-trips' => [
        'title'   => 'سجل رحلات المركبة',
        'export'  => 'تصدير',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],

    'driver-trips' => [
        'title'   => 'سجل رحلات السائق',
        'export'  => 'تصدير',
        'columns' => $tripColumns,
        'filters' => $tripFilters,
    ],
];
