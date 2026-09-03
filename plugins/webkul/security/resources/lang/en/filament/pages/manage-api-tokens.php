<?php

return [
    'navigation' => [
        'label' => 'API Tokens',
    ],

    'group'      => 'General',
    'breadcrumb' => 'API Tokens',
    'title'      => 'API Tokens',
    'subheading' => 'Tokens client integrations use to authenticate against the API. A token carries the roles and allowed companies of the user it belongs to.',

    'abilities' => [
        'read'  => 'Read only',
        'write' => 'Write',
        'all'   => 'Full access',
    ],

    'table' => [
        'columns' => [
            'name'      => 'Label',
            'user'      => 'Acts as',
            'abilities' => 'Scopes',
            'last-used' => 'Last used',
            'expires'   => 'Expires',
            'created'   => 'Issued',
        ],

        'never-used' => 'Never used',
    ],

    'actions' => [
        'issue' => [
            'label'  => 'Issue token',
            'submit' => 'Issue token',

            'fields' => [
                'user'             => 'Acts as user',
                'user-helper'      => 'The token can do exactly what this user can do. Use a dedicated user per client, never an administrator.',
                'name'             => 'Label',
                'name-helper'      => 'How you will recognise this token later, e.g. acme-warehouse-sync.',
                'abilities'        => 'Scopes',
                'abilities-helper' => 'Read only cannot POST, PATCH or DELETE. Full access ignores scope checks entirely.',
            ],
        ],

        'revoke' => [
            'label'       => 'Revoke',
            'heading'     => 'Revoke this token?',
            'description' => 'The integration using it will stop working immediately. This cannot be undone.',
            'bulk'        => 'Revoke selected',
        ],
    ],

    'notifications' => [
        'issued' => [
            'title' => 'Token issued',
            'body'  => 'Copy it now - it cannot be shown again.',
        ],

        'revoked'      => 'Token revoked',
        'missing-user' => 'That user no longer exists.',
    ],

    'plaintext' => [
        'title'       => 'Copy this token now',
        'description' => 'It is stored only as a hash, so this is the one time it can be displayed. Send it to the client over a channel you trust.',
    ],

    'empty' => [
        'heading'     => 'No API tokens yet',
        'description' => 'Issue one to let a client integration authenticate against the API.',
    ],
];
