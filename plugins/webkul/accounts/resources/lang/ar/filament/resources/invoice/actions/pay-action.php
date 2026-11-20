<?php

return [
    'title' => 'دفع',

    'paid-now' => [
        'label'   => 'مدفوعة الآن',
        'helper'  => 'عند الحفظ تُؤكَّد الفاتورة ويُسجَّل الدفع كاملًا.',
        'invalid' => 'لا يمكن استخدام طريقة الدفع هذه لهذه الفاتورة.',
    ],

    'form' => [
        'more-options' => 'خيارات إضافية',
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
