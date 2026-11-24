<?php

return [
    'title' => 'Simamia ufuatiliaji wa asili',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Makundi na nambari za mfululizo',
        'enable-lots-serial-numbers-helper-text'                 => 'Pata ufuatiliaji kamili kutoka kwa wasambazaji hadi kwa wateja',
        'configure-lots'                                         => 'Sanidi makundi',
        'enable-expiration-dates'                                => 'Tarehe za kuisha muda',
        'enable-expiration-dates-helper-text'                    => 'Weka tarehe za kuisha muda kwenye makundi na nambari za mfululizo',
        'display-on-delivery-slips'                              => 'Onyesha kwenye hati za uwasilishaji',
        'display-on-delivery-slips-helper-text'                  => 'Makundi na nambari za mfululizo zitaonekana kwenye hati za uwasilishaji',
        'display-expiration-dates-on-delivery-slips'             => 'Onyesha tarehe za kuisha muda kwenye hati za uwasilishaji',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Tarehe za kuisha muda zitaonekana kwenye hati ya uwasilishaji',
        'enable-consignments'                                    => 'Bidhaa za amana',
        'enable-consignments-helper-text'                        => 'Weka mmiliki wa bidhaa zilizohifadhiwa',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Una bidhaa ghalani zenye ufuatiliaji wa kundi/nambari ya mfululizo umewashwa. ',
                'body'  => 'Kwanza zima ufuatiliaji kwenye bidhaa zote kabla ya kuzima mpangilio huu.',
            ],
        ],
    ],
];
