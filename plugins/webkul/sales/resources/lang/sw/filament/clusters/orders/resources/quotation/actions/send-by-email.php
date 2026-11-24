<?php

return [
    'title'        => 'Tuma kwa barua pepe',
    'resend-title' => 'Tuma tena kwa barua pepe',
    'quotation'    => 'nukuu',
    'quotations'   => 'nukuu',

    'modal' => [
        'heading' => 'Tuma nukuu kwa barua pepe',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Wahusika',
            'subject'     => 'Kichwa',
            'description' => 'Maelezo',
            'attachment'  => 'Kiambatisho',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Hakuna wapokeaji waliochaguliwa',
                    'body'  => 'Tafadhali chagua angalau mhusika mmoja wa kutumiwa nukuu.',
                ],

                'all_success' => [
                    'title' => 'Nukuu zimetumwa!',
                    'body'  => ':plural zako zimewasilishwa kwa mafanikio kwa: :recipients',
                ],

                'all_failed' => [
                    'title' => 'Imeshindikana kutuma nukuu',
                    'body'  => 'Tumekumbana na matatizo kutuma nukuu zako: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Baadhi ya nukuu zimetumwa',
                    'sent_part'   => 'Zimewasilishwa kwa mafanikio kwa: :recipients',
                    'failed_part' => 'Hazikuweza kuwasilishwa kwa: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
