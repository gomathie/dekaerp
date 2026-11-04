<?php

return [
    'title' => 'Users and logins',

    'table' => [
        'columns' => [
            'name'                 => 'Name',
            'login'                => 'Login (email)',
            'company-admin'        => 'Company Admin',
            'active'               => 'Active',
            'must-change-password' => 'Must change password',
        ],

        'empty-state' => [
            'heading'     => 'No users yet',
            'description' => 'Invite or create the administrator in the Administrator section above.',
        ],

        'actions' => [
            'set-password' => [
                'label'             => 'Set password',
                'modal-heading'     => 'Set the password for :email',
                'modal-description' => 'Existing passwords cannot be shown, because they are stored encrypted one way. Set a new one and give it to the user yourself: it is not emailed.',

                'form' => [
                    'password'             => 'New password',
                    'must-change-password' => 'Must change this password at next sign-in',
                ],
            ],
        ],
    ],
];
