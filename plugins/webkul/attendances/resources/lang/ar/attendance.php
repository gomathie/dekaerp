<?php

return [
    'actions' => [
        'clock-in'  => 'تسجيل الحضور',
        'clock-out' => 'تسجيل الانصراف',
        'correct'   => 'تصحيح الحضور',
        'export'    => 'تصدير الحضور',
    ],
    'capture' => [
        'off'      => 'معطّل',
        'optional' => 'اختياري',
        'required' => 'إلزامي',
    ],
    'exceptions' => [
        'not-enabled' => 'الحضور غير مفعّل للشركة :company.',
    ],
    'notifications' => [
        'failed' => 'لم تنجح العملية. لم يتم حفظ التغيير.',
    ],
    'statuses' => [
        'clocked-in'     => 'مسجّل منذ :time',
        'not-clocked-in' => 'لم يتم التسجيل',
        'on-leave'       => 'في إجازة',
    ],
    'validation' => [
        'check-out-order'     => 'يجب أن يكون الانصراف بعد الحضور.',
        'location-required'   => 'الموقع مطلوب لتسجيل الحضور أو الانصراف.',
        'no-employee'         => 'ليس لديك سجل موظف في هذه الشركة.',
        'not-allowed'         => 'يمكنك تصحيح حضور فريقك فقط.',
        'not-clocked-in'      => 'لم تسجّل الحضور.',
        'not-your-attendance' => 'يمكنك تسجيل حضورك فقط.',
        'reason-required'     => 'التصحيح يحتاج إلى سبب.',
    ],
];
