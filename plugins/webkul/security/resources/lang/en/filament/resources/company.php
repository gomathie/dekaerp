<?php

return [
    'capacity-error' => 'This company has reached its :limit user seats (:used in use, including pending invitations). Raise the seat limit to add more users.',
    'title' => 'Companies',

    'navigation' => [
        'title' => 'Companies',
    ],

    'global-search' => [
        'email' => 'Email',
    ],

    'pages' => [
        'create' => [
            'administrator' => [
                'invited' => [
                    'title' => 'Administrator invited',
                    'body'  => 'An invitation was sent to :email. They choose their own password and can then add their company’s users.',
                ],

                'skipped' => [
                    'title'         => 'No administrator was invited',
                    'missing-email' => 'The company was created, but it has no email address, so nobody could be invited to administer it. Add one and send an invitation from the Users page.',
                    'missing-role'  => 'The company was created, but the Company Admin role does not exist in this installation, so nobody could be invited.',

                    'seats-exhausted' => 'The company was created, but it has no free user seats left, so nobody could be invited to administer it. Raise the seat limit and send an invitation from the Users page.',
                    'invite-failed'   => 'The company was created, but the invitation could not be issued. The address may already belong to a user, or the company may not yet be one you are allowed to assign. Invite from the Users page instead.',
                    'mail-failed'     => 'The company was created and the invitation was issued, but the email could not be sent. Give this sign-in link to :email over a channel you trust — it expires in 7 days, and they set their own password: :link',
                ],
            ],
        ],

        'edit' => [
            'administrator' => [
                'skipped' => [
                    'title'           => 'No administrator was invited',
                    'disabled'        => 'Invitations are switched off in the user settings, so :email was not invited. Set a password instead, or switch invitations on.',
                    'missing-role'    => 'The Company Admin role does not exist in this installation, so nobody could be invited.',
                    'seats-exhausted' => 'This company has no free user seats left, so :email was not invited. Raise the seat limit first.',
                    'invite-failed'   => 'The invitation could not be issued. The address may already belong to a user, or the company may not be one you are allowed to assign. To reset an existing administrator’s password, enter a password as well.',
                    'mail-failed'     => 'The invitation was issued, but the email could not be sent. Give this sign-in link to :email over a channel you trust — it expires in 7 days, and they set their own password: :link',
                ],
            ],
        ],

        'administrator-account' => [
            'created' => [
                'title' => 'Administrator account created',
                'body'  => ':email can sign in now with the password you set. It was not emailed: give it to them yourself.',
            ],

            'password-reset' => [
                'title' => 'Administrator password changed',
                'body'  => 'The password for :email was changed. It was not emailed: give it to them yourself.',
            ],

            'refused' => [
                'title'             => 'The administrator was not changed',
                'missing-email'     => 'Enter the administrator’s email address together with the password.',
                'missing-role'      => 'The Company Admin role does not exist in this installation, so no account was created.',
                'not-member'        => ':email belongs to a user who is not a member of this company, so their password was not changed here. Manage that user from the Users page.',
                'not-allowed'       => 'You are not allowed to manage this company’s administrator.',
                'seats-exhausted'   => 'This company has no free user seats left, so no account was created for :email. Raise the seat limit first.',
            ],
        ],
    ],
];
