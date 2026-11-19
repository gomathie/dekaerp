<?php

return [
    'title' => 'دفع',

    'form' => [
        'fields' => [
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
