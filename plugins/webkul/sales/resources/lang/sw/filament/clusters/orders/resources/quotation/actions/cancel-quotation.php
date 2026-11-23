<?php

return [
    'title' => 'Ghairi',
    'modal' => [
        'heading'     => 'Ghairi Nukuu',
        'description' => 'Ni wewe sure wewe want hadi ghairi hii Nukuu?',
    ],

    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Send & Ghairi',

            'notification' => [
                'cancelled' => [
                    'title' => 'Nukuu imeghairiwa',
                    'body'  => 'Nukuu ina imeghairiwa na barua pepe ina imetumwa successfully.',
                ],
            ],
        ],

        'cancel' => [
            'title' => 'Ghairi',

            'notification' => [
                'cancelled' => [
                    'title' => 'Nukuu imeghairiwa',
                    'body'  => 'Nukuu ina imeghairiwa successfully.',
                ],
            ],
        ],

        'close' => [
            'title' => 'Funga',
        ],
    ],

    'form' => [
        'fields' => [
            'partner'             => 'Mshirika',
            'subject'             => 'Subject',
            'subject-placeholder' => 'Subject',
            'subject-default'     => 'Nukuu :name ina imeghairiwa kwa Agizo la mauzo #:id',
            'description'         => 'Maelezo',
            'description-default' => 'Dear <b>:partner_name</b>, <br/><br/>Sisi would like hadi inform wewe hiyo yako Agizo la mauzo <b>:name</b> ina imeghairiwa. Kama matokeo, hakuna further charges ita tumia hadi hii agizo. Ikiwa rudisho ni inahitajika, hii ita kuwa processed saa earliest convenience.<br/><br/>Unapaswa wewe ina yoyote questions au require further assistance, tafadhali feel free hadi reach nje hadi us.',
        ],
    ],
];
