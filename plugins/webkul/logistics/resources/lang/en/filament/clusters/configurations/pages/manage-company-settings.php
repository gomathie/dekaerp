<?php

return [
    'navigation' => [
        'title' => 'Settings',
    ],

    'title'      => 'Logistics Settings',
    'subheading' => 'Settings for :company. Logistics is switched on and off per company.',
    'no-company' => 'Select a company first: Logistics settings are kept per company.',

    'sections' => [
        'status'     => 'Status',
        'delivery'   => 'Proof of delivery',
        'operations' => 'Operations',
        'accounting' => 'Accounting',
    ],

    'status' => [
        'enabled'  => 'Logistics is enabled for this company',
        'disabled' => 'Logistics is not enabled for this company',
    ],

    'readiness' => [
        'label' => 'Readiness',
        'ok'    => 'Everything Logistics needs is in place.',
    ],

    'fields' => [
        'require-pod-for-delivery'    => 'Require proof of delivery to mark a shipment delivered',
        'require-pod-photo'           => 'Require a photo with the proof of delivery',
        'stop-link-ttl-hours'         => 'Stop link validity',
        'stop-link-ttl-hours-help'    => 'How long a proof-of-delivery link stays usable after it is issued. Shorter is safer: the link is a credential, and it travels in a URL.',
        'stop-link-rate-limit'        => 'Stop link requests per minute',
        'stop-link-rate-limit-help'   => 'Per link, per minute. Leave blank for the default. Raise it only if your drivers are being turned away.',
        'capture-driver-on-pod'       => 'Record the driver on proofs of delivery',
        'capture-driver-on-pod-help'  => 'Takes the driver from the stop’s trip, so the proof shows who was at the door.',
        'capture-recipient-id'        => 'Ask for the recipient’s ID',
        'capture-recipient-id-help'   => 'Adds a required ID field when proof is captured. Only collect this where you have a reason to.',
        'default-service-type'        => 'Default service type',
        'capacity-check'              => 'Vehicle capacity check',
        'overdue-grace-minutes'       => 'Overdue after (minutes past the expected time)',
        'free-waiting-minutes'        => 'Free waiting time at a stop (minutes)',
        'free-waiting-minutes-helper' => 'Beyond this, a waiting-time charge is suggested. It is never added automatically.',
        'invoice-journal'             => 'Journal for customer invoices',
        'bill-journal'                => 'Journal for vendor bills',
        'default-expense-account'     => 'Default expense account',
        'expense-approval-required'   => 'Expenses need approval before billing',
    ],

    'actions' => [
        'save' => 'Save',

        'enable' => [
            'label'                     => 'Enable Logistics',
            'heading'                   => 'Enable Logistics for this company?',
            'description'               => 'Logistics menus and records become available to users of this company who have Logistics permissions.',
            'description-with-problems' => 'Logistics can be enabled, but some features will not work until these are fixed: :problems',
            'notification'              => 'Logistics enabled',
        ],

        'disable' => [
            'label'        => 'Disable Logistics',
            'heading'      => 'Disable Logistics for this company?',
            'description'  => 'Logistics is hidden and no new records can be created for this company. Existing data is kept, and enabling it again restores access.',
            'notification' => 'Logistics disabled',
        ],
    ],

    'notifications' => [
        'saved' => 'Settings saved',
    ],
];
