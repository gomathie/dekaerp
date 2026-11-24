<?php

return [
    'title' => 'Ghairi',
    'modal' => [
        'heading'     => 'Ghairi nukuu',
        'description' => 'Una uhakika unataka kughairi nukuu hii?',
    ],

    'footer-actions' => [
        'send-and-cancel' => [
            'title' => 'Tuma na ughairi',

            'notification' => [
                'cancelled' => [
                    'title' => 'Nukuu imeghairiwa',
                    'body'  => 'Nukuu imeghairiwa na barua pepe imetumwa kwa mafanikio.',
                ],
            ],
        ],

        'cancel' => [
            'title' => 'Ghairi',

            'notification' => [
                'cancelled' => [
                    'title' => 'Nukuu imeghairiwa',
                    'body'  => 'Nukuu imeghairiwa kwa mafanikio.',
                ],
            ],
        ],

        'close' => [
            'title' => 'Funga',
        ],
    ],

    'form' => [
        'fields' => [
            'partner'             => 'Mhusika',
            'subject'             => 'Kichwa',
            'subject-placeholder' => 'Kichwa',
            'subject-default'     => 'Nukuu :name imeghairiwa kwa oda ya mauzo #:id',
            'description'         => 'Maelezo',
            'description-default' => 'Mpendwa <b>:partner_name</b>, <br/><br/>Tunapenda kukujulisha kwamba oda yako ya mauzo <b>:name</b> imeghairiwa. Kwa hiyo, hakuna gharama zaidi zitakazotozwa kwa oda hii. Ikiwa marejesho yanahitajika, yatashughulikiwa mapema iwezekanavyo.<br/><br/>Ikiwa una maswali au unahitaji msaada zaidi, tafadhali wasiliana nasi.',
        ],
    ],
];
