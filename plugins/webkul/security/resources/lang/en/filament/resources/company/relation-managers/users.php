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
            'copy-invitation-link' => [
                'label'      => 'Copy invitation link',
                'invitation' => 'Pending invitation',
                'link'       => 'Invitation link',
            ],
            'show-password' => [
                'label'             => 'Show password',
                'modal-heading'     => 'Show the password for :email',
                'modal-description' => 'Enter your own password to continue. This view is recorded in the audit log.',
                'submit'            => 'Show password',
                'shown'             => 'Password for :email',

                'form' => [
                    'password' => 'Your password',
                ],
            ],
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
