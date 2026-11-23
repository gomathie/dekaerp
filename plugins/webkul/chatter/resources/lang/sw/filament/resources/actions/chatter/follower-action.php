    <?php

    return [
        'setup' => [
            'title'               => 'Followers',
            'submit-action-title' => 'Ongeza Follower',
            'tooltip'             => 'Ongeza Follower',

            'form' => [
                'fields' => [
                    'recipients'  => 'Recipients',
                    'notify-user' => 'Notify Mtumiaji',
                    'add-a-note'  => 'Ongeza maelezo',
                ],
            ],

            'actions' => [
                'notification' => [
                    'success' => [
                        'title' => 'Follower Added',
                        'body'  => 'Follower ina added Successfully.',
                    ],

                    'partial_message' => [
                        'title'    => 'Ujumbe imetumwa na notice',
                        'single'   => ':count follower ilikuwa si notified due hadi haipo barua pepe: :names',
                        'multiple' => ':count followers zilikuwa si notified due hadi haipo emails: :names',
                    ],

                    'error' => [
                        'title' => 'Follower added hitilafu',
                        'body'  => 'Imeshindwa hadi ongeza ":partner" kama follower',
                    ],
                ],

                'mail' => [
                    'subject' => 'Mwaliko hadi follow :model: :department',
                ],
            ],
        ],
    ];
