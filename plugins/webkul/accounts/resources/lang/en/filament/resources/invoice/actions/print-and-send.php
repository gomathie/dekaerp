<?php

return [
    'title' => 'Print & Send',

    'modal' => [
        'title' => 'Preview Invoice',

        'form' => [
            'partners'    => 'Customer',
            'subject'     => 'Subject',
            'description' => 'Description',
            'files'       => 'Attachment',
        ],

        'action' => [
            'submit' => [
                'title' => 'Send',
            ],
        ],

        'notification' => [
            'invoice-sent' => [
                'title' => 'Invoice Sent',
                'body'  => 'Invoice has been sent successfully.',
            ],
        ],
    ],

    'message' => [
        'subject'   => ':partner Invoice (Ref :number)',
        'greeting'  => 'Dear :name,',
        'body'      => 'Your invoice :number from :company for :amount is now available. Kindly arrange payment at your earliest convenience.',
        'bank'      => 'When making the payment, please reference :number for account :account.',
        'questions' => 'If you have any questions, feel free to reach out.',
        'regards'   => 'Best regards,',
    ],
];
