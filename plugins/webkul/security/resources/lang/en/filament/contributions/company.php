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
            'email'                => 'Administrator email',
            'email-helper'         => 'Without a password, an invitation is sent to this address and they choose their own password. Leave it blank to invite the company email.',
            'email-helper-edit'    => 'Enter an address to invite a new administrator. Add a password to create their account straight away, or to reset the password of this company’s administrator. Leave blank to change nothing.',
            'password'             => 'Administrator password',
            'password-helper'      => 'Optional. Sets the password now instead of sending an invitation, or resets it for this company’s administrator. It is never emailed: give it to them yourself.',
            'must-change-password' => 'Must change this password at next sign-in',
        ],
    ],
];
