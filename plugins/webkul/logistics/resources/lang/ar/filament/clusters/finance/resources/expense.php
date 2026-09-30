<?php

return [
    'navigation' => [
        'title' => 'المصروفات',
    ],

    'form' => [
        'sections' => [
            'details'   => 'تفاصيل المصروف',
            'reference' => 'المرجع والإيصال',
        ],
        'fields' => [
            'company'          => 'الشركة',
            'date'             => 'التاريخ',
            'amount'           => 'المبلغ',
            'currency'         => 'العملة',
            'category'         => 'الفئة',
            'paid-by'          => 'دفعه',
            'employee'         => 'الموظف',
            'payee'            => 'المورد أو الناقل',
            'shipment'         => 'الشحنة',
            'trip'             => 'الرحلة',
            'vehicle'          => 'المركبة',
            'vendor-reference' => 'مرجع المورد',
            'description'      => 'الوصف',
            'receipt'          => 'الإيصال',
        ],
    ],

    'table' => [
        'columns' => [
            'date'        => 'التاريخ',
            'category'    => 'الفئة',
            'description' => 'الوصف',
            'amount'      => 'المبلغ',
            'state'       => 'الحالة',
            'shipment'    => 'الشحنة',
            'company'     => 'الشركة',
        ],
        'filters' => [
            'state'    => 'الحالة',
            'category' => 'الفئة',
        ],
    ],

    'infolist' => [
        'sections' => [
            'summary' => 'المصروف',
        ],
        'fields' => [
            'state'       => 'الحالة',
            'date'        => 'التاريخ',
            'amount'      => 'المبلغ',
            'category'    => 'الفئة',
            'shipment'    => 'الشحنة',
            'trip'        => 'الرحلة',
            'vehicle'     => 'المركبة',
            'receipt'     => 'الإيصال',
            'description' => 'الوصف',
        ],
    ],

    'actions' => [
        'submitExpense' => [
            'label'        => 'إرسال',
            'heading'      => 'إرسال هذا المصروف؟',
            'notification' => 'تم إرسال المصروف.',
        ],
        'approveExpense' => [
            'label'        => 'موافقة',
            'heading'      => 'الموافقة على هذا المصروف؟',
            'notification' => 'تمت الموافقة على المصروف.',
        ],
        'rejectExpense' => [
            'label'        => 'رفض',
            'heading'      => 'رفض هذا المصروف؟',
            'notification' => 'تم رفض المصروف.',
        ],
        'cannot-proceed' => 'هذا المصروف غير جاهز بعد',
    ],
];
