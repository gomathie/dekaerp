<?php

return [
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
                    'invite-failed' => 'The company was created, but the invitation could not be issued. The address may already belong to a user, or the company may not yet be one you are allowed to assign. Invite from the Users page instead.',
                    'mail-failed'   => 'The company was created and the invitation was issued, but the email could not be sent. Resend it from the Users page.',
                ],
            ],
        ],
    ],
];
