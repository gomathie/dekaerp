<?php

return [
    'waiting-time' => [
        // A suggestion only: D14 bills detention when a person confirms it,
        // never automatically.
        'description' => 'وقت الانتظار عند :stop (:minutes دقيقة بعد المهلة المجانية)',
    ],

    'charges' => [
        'title'  => 'الرسوم',
        'fields' => [
            'product'            => 'الخدمة',
            'description'        => 'الوصف',
            'quantity'           => 'الكمية',
            'price-unit'         => 'سعر الوحدة',
            'discount'           => 'الخصم (%)',
            'taxes'              => 'الضرائب',
            'is-billable'        => 'قابل للفوترة',
            'is-billable-helper' => 'تُدرج الرسوم القابلة للفوترة فقط عند إنشاء فاتورة.',
            'invoiced'           => 'تمت فوترته',
        ],
    ],

    'invoices' => [
        'title'   => 'الفواتير',
        'columns' => [
            'number'        => 'الفاتورة',
            'state'         => 'الحالة',
            'payment-state' => 'الدفع',
            'date'          => 'التاريخ',
            'total'         => 'الإجمالي',
        ],
        'actions' => [
            'open' => 'فتح في المحاسبة',
        ],
    ],

    'unbilled' => [
        'title'   => 'الرسوم غير المفوترة',
        'columns' => [
            'shipment'    => 'الشحنة',
            'customer'    => 'العميل',
            'description' => 'الرسم',
            'quantity'    => 'الكمية',
            'subtotal'    => 'المبلغ',
            'total'       => 'الإجمالي',
        ],
        'filters' => [
            'currency' => 'العملة',
            'shipment' => 'الشحنة',
        ],
    ],

    'actions' => [
        'create-invoice' => [
            'label'              => 'إنشاء فاتورة',
            'heading'            => 'فوترة هذه الشحنة؟',
            'description'        => 'يتحول كل رسم قابل للفوترة لم تتم فوترته بعد إلى بند في فاتورة عميل جديدة. تُترك الرسوم التي تمت فوترتها كما هي.',
            'notification'       => 'تم إنشاء الفاتورة.',
            'notification-body'  => 'الفاتورة :invoice موجودة في المحاسبة كمسودة يمكنك مراجعتها قبل الترحيل.',
            'nothing-to-invoice' => 'لا يوجد ما تتم فوترته',
        ],
    ],
];
