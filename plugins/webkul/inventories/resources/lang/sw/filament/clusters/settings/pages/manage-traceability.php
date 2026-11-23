<?php

return [
    'title' => 'Manage Traceability',

    'form' => [
        'enable-lots-serial-numbers'                             => 'Makundi & Nambari ya mfululizo Numbers',
        'enable-lots-serial-numbers-helper-text'                 => 'Pata kamili traceability kutoka wasambazaji hadi wateja',
        'configure-lots'                                         => 'Configure Makundi',
        'enable-expiration-dates'                                => 'Expiration Dates',
        'enable-expiration-dates-helper-text'                    => 'Weka expiration dates washa makundi & nambari ya mfululizo numbers',
        'display-on-delivery-slips'                              => 'Display washa Usafirishaji Slips',
        'display-on-delivery-slips-helper-text'                  => 'Makundi & Nambari ya mfululizo numbers ita appear washa usafirishaji slips',
        'display-expiration-dates-on-delivery-slips'             => 'Display Expiration Dates washa Usafirishaji Slips',
        'display-expiration-dates-on-delivery-slips-helper-text' => 'Expiration dates ita appear washa usafirishaji slip',
        'enable-consignments'                                    => 'Consignments',
        'enable-consignments-helper-text'                        => 'Weka mmiliki washa stored bidhaa',
    ],

    'before-save' => [
        'notification' => [
            'warning' => [
                'title' => 'Wewe ina bidhaa katika hisa hiyo ina kundi/nambari ya mfululizo nambari tracking imewashwa. ',
                'body'  => 'Kwanza switch zima tracking washa zote bidhaa kabla ya switching zima hii mpangilio.',
            ],
        ],
    ],
];
