<?php

return [
    'tabs' => [
        'help'       => 'Help',
        'developers' => 'Developers',
    ],

    'heading' => 'API reference',
    'intro'   => 'Connect your own software to your company\'s data. Every request acts as a user, with a token, and sees only what that user may see.',
    'general' => 'General',
    'empty'   => 'This company has no modules with an API yet. Signing in and out works for every company.',
    'copy'    => 'Copy',
    'copied'  => 'Copied',

    'badges' => [
        'version'   => ':name API v1',
        'total'     => '{0} No endpoints|{1} 1 endpoint|[2,*] :count endpoints',
        'in_module' => ':count in :module',
    ],

    'modules' => [
        'label'       => 'Modules',
        'sales'       => 'Sales',
        'purchases'   => 'Purchases',
        'accounts'    => 'Accounting',
        'inventories' => 'Inventory',
        'products'    => 'Products',
        'partners'    => 'Contacts',
        'projects'    => 'Projects',
        'support'     => 'Settings',
    ],

    'search' => [
        'placeholder' => 'Search :module endpoints',
        'empty'       => 'No endpoint matches.',
    ],

    'getting_started' => [
        'title'               => 'Getting started',
        'base_url'            => 'Base URL',
        'authentication'      => 'Authentication',
        'authentication_help' => 'Send your token in the Authorization header of every request.',
        'token'               => 'Get a token',
        'token_page'          => 'Issue one under Settings › API Tokens, for a user created for the integration.',
        'token_page_button'   => 'Open API Tokens',
        'token_login'         => 'Or have the integration sign in with POST /admin/api/v1/login (shown below).',
        'scopes'              => 'Scopes',
        'scopes_help'         => 'A read token can only make GET requests. Creating, changing or deleting anything needs write.',
        'company'             => 'Which company',
        'company_help'        => 'Send X-Company-Id with one of the token user\'s companies. Without it, requests go to the user\'s default company.',
        'company_current'     => 'The company you are working in has id :id.',
        'limits'              => 'Limits',
        'rate_limit'          => '{1} 1 request per minute per token.|[2,*] :count requests per minute per token.',
        'expiry'              => 'Tokens expire after :time.',
        'no_expiry'           => 'Tokens do not expire.',
        'format'              => 'Format',
        'format_help'         => 'Always send this header, so errors come back as JSON rather than as a web page.',
    ],

    'full_reference' => [
        'title'       => 'Full reference',
        'description' => 'Every module and endpoint, with a console to try requests. Opens in a new tab and needs you to be signed in.',
        'button'      => 'Open full reference',
    ],

    'headers' => [
        'authorization' => 'Bearer followed by your token.',
        'company'       => 'The id of the company to work in.',
        'accept'        => 'application/json',
    ],

    'in' => [
        'path'   => 'path',
        'query'  => 'query',
        'body'   => 'body',
        'header' => 'header',
    ],

    'endpoint' => [
        'parameters'    => 'Parameters',
        'name'          => 'Name',
        'type'          => 'Type',
        'description'   => 'Description',
        'required'      => 'Required',
        'optional'      => 'Optional',
        'samples'       => 'Request samples',
        'response'      => 'Response (:status)',
        'other_answers' => 'Other answers:',
        'no_token'      => 'No token needed',
    ],

    'answers' => [
        '401' => '401 no token, or an expired one',
        '403' => '403 the token\'s user or company may not do this',
        '422' => '422 the data sent is not valid, and errors names each field',
        '429' => '429 too many requests, wait a minute',
    ],
];
