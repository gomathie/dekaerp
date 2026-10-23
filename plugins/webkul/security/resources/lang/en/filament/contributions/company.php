<?php

return [
    'administrator' => [
        'title'       => 'Administrator',
        'description' => 'The person who administers this company and manages its own users.',
        'none'        => 'No administrator yet for this company.',
        'pending'     => 'invitation sent, not accepted yet',
        'inactive'    => 'deactivated',
        'manage'      => 'Manage this user',

        'fields' => [
            'email'        => 'Administrator email',
            'email-helper' => 'An invitation is sent to this address, where they set their own name and password. Leave it blank to use the company email.',
        ],
    ],
];
