<?php

return [
    'title' => 'طباعة وإرسال',

    'modal' => [
        'title' => 'معاينة الفاتورة',

        'form' => [
            'partners'    => 'العميل',
            'subject'     => 'الموضوع',
            'description' => 'الوصف',
            'files'       => 'المرفق',
        ],

        'action' => [
            'submit' => [
                'title' => 'إرسال',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'تم إرسال الفاتورة',
                'body'  => 'تم إرسال الفاتورة بنجاح.',
            ],
        ],
    ],

    'message' => [
        'subject'   => 'فاتورة :partner (المرجع :number)',
        'greeting'  => 'عزيزي :name،',
        'body'      => 'فاتورتك :number من :company بمبلغ :amount متاحة الآن. يرجى ترتيب الدفع في أقرب وقت ممكن.',
        'bank'      => 'عند الدفع، يرجى ذكر المرجع :number للحساب :account.',
        'questions' => 'إذا كانت لديك أي أسئلة، فلا تتردد في التواصل معنا.',
        'regards'   => 'مع أطيب التحيات،',
    ],
];
