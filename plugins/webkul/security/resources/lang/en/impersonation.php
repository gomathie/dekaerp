<?php

return [
    'banner' => [
        'message' => 'You are signed in as :target (:company). Impersonated by :impersonator.',
        'ends-at' => 'Ends at :time',
        'end'     => 'End impersonation',
    ],

    'notifications' => [
        'started' => 'You are now signed in as :name.',
        'refused' => 'Impersonation was not started',

        'ended-automatically' => [
            'expired' => 'The impersonation reached its time limit and has ended.',
            'invalid' => 'The impersonation is no longer allowed and has ended.',
        ],

        'granted' => 'Impersonation access granted.',
        'revoked' => 'Impersonation access revoked.',
    ],

    'actions' => [
        'impersonate' => [
            'label'             => 'Impersonate',
            'modal-heading'     => 'Sign in as :name',
            'modal-description' => 'You will act as this user, with their permissions only, for at most :minutes minutes. Everything you do is recorded with your name.',
            'submit'            => 'Start impersonation',

            'form' => [
                'reason'   => 'Reason',
                'password' => 'Your password',
            ],
        ],

        'grant' => [
            'label'             => 'Allow impersonation',
            'modal-description' => 'This Multi-Company Admin will be able to impersonate tenant users in their assigned companies.',
        ],

        'revoke' => [
            'label'             => 'Remove impersonation access',
            'modal-description' => 'An impersonation they are running now ends on their next page load.',
        ],
    ],
];
