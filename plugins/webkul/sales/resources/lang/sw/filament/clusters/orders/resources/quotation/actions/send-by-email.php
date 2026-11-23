<?php

return [
    'title'        => 'Send Na Barua pepe',
    'resend-title' => 'Re-Send Na Barua pepe',
    'quotation'    => 'nukuu',
    'quotations'   => 'nukuu',

    'modal' => [
        'heading' => 'Send Nukuu Na Barua pepe',
    ],

    'form' => [
        'fields' => [
            'partners'    => 'Washirika',
            'subject'     => 'Subject',
            'description' => 'Maelezo',
            'attachment'  => 'Kiambatisho',
        ],
    ],

    'actions' => [
        'notification' => [
            'email' => [
                'no_recipients' => [
                    'title' => 'Hakuna Recipients Imechaguliwa',
                    'body'  => 'Tafadhali chagua saa kidogo one mshirika hadi send nukuu hadi.',
                ],

                'all_success' => [
                    'title' => 'Nukuu Imetumwa!',
                    'body'  => 'Yako :plural ina successfully delivered hadi: :recipients',
                ],

                'all_failed' => [
                    'title' => 'Haiwezekani hadi Send Nukuu',
                    'body'  => 'Sisi encountered issues sending yako nukuu: :failures',
                ],

                'partial_success' => [
                    'title'       => 'Baadhi Nukuu Imetumwa',
                    'sent_part'   => 'Successfully delivered hadi: :recipients',
                    'failed_part' => 'Could si deliver hadi: :failures',
                ],

                'failure_item' => ':partner (:reason)',
            ],
        ],
    ],

];
