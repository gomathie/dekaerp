<?php

return [
    'title' => 'تسجيل دفعة',

    'paid-now' => [
        'label'       => 'مدفوعة الآن',
        'helper'      => 'عند الحفظ تُؤكَّد الفاتورة ويُسجَّل الدفع كاملًا.',
        'invalid'     => 'لا يمكن استخدام طريقة الدفع هذه لهذه الفاتورة.',
        'not-allowed' => 'ليس لديك إذن بتسجيل المدفوعات.',
    ],

    'form' => [
        'more-options' => 'خيارات إضافية',
        'hints'        => [
            'balance-after'  => 'الرصيد بعد هذه الدفعة: :amount',
            'kept-as-credit' => 'سيُحفظ :amount كرصيد لصالح هذا العميل',
        ],
        'fields'       => [
            'paid-how'               => 'كيف تم الدفع؟',
            'new-way'                => 'طريقة دفع جديدة (مثل Wave أو البطاقة)',
            'journal'                => 'دفتر اليومية',
            'amount'                 => 'المبلغ',
            'currency'               => 'العملة',
            'payment-method-line'    => 'بند طريقة الدفع',
            'payment-date'           => 'تاريخ الدفع',
            'recipient-bank-account' => 'الحساب البنكي للمستلم',
            'communication'          => 'البيان',
        ],
    ],

    'notifications' => [
        'payment-failed' => [
            'title'      => 'فشلت عملية الدفع',
            'plain-body' => 'تعذّر تجهيز هذا الدفع. تم إبلاغ فريقنا. حاول مرة أخرى بعد قليل أو تواصل مع الدعم.',
        ],
    ],
];
