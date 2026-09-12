<?php

return [
    'navigation' => [
        'label' => 'Help',
    ],

    'title'      => 'Help',
    'heading'    => 'Help & Resources',
    'subheading' => 'Everything you need to get the most out of :app.',

    'guides' => [
        'group' => 'Guides',

        'admin' => [
            'title'       => 'Admin Guide',
            'description' => 'Set up :app for your company, teams, modules and controls before daily work begins.',
            'button'      => 'Admin setup checklist',
            'sections'    => [
                [
                    'title' => 'Set up the company',
                    'items' => [
                        'Create or review companies, warehouses, currencies, taxes and fiscal settings.',
                        'Assign users to the companies they can access and confirm the active company switcher.',
                        'Configure sequences, payment terms, products, units of measure and document templates before transactions begin.',
                    ],
                ],
                [
                    'title' => 'Control access',
                    'items' => [
                        'Create roles for finance, sales, inventory, HR, project and management teams.',
                        'Grant module permissions only to the users who need them.',
                        'Review custom fields, imports, exports and API tokens before giving broad access.',
                    ],
                ],
                [
                    'title' => 'Operate and maintain',
                    'items' => [
                        'Install only the modules the business uses, then hide or remove unused modules.',
                        'Monitor discussions, attachments, approvals and record ownership during rollout.',
                        'Keep email, PDF, backup and update settings aligned with the deployment policy.',
                    ],
                ],
            ],
        ],

        'user' => [
            'title'          => 'User Guide',
            'description'    => 'Use the built-in user guide for DEKA walkthroughs and module-reference pages for daily work in :app.',
            'button'         => 'Open User Guide',
            'full_heading'   => 'Full User Guide',
            'count'          => '{0} No guide pages|{1} 1 guide page|[2,*] :count guide pages',
            'category_count' => '{0} No pages|{1} 1 page|[2,*] :count pages',
            'callouts'       => [
                'tip'       => 'Tip',
                'note'      => 'Note',
                'important' => 'Important',
            ],
            'sections'    => [
                [
                    'title' => 'Getting started',
                    'items' => [
                        'Platform Overview & Navigation',
                        'Multi-Company Setup & Switcher',
                    ],
                ],
                [
                    'title' => 'Core workflows',
                    'items' => [
                        'Sell: Sales & Invoicing',
                        'Buy: Purchasing & Accounting',
                        'Make & Move: Inventory & Production',
                        'Run: Projects, HR & Maintenance',
                        'Platform, API & Self-Hosting',
                    ],
                ],
                [
                    'title' => 'Module reference: 12 modules / 69 pages',
                    'items' => [
                        'Invoices, Sales, Purchase, Inventory, Manufacturing, Maintenance',
                        'Contacts, Project, Website, Employees, Recruitments, Time Off',
                    ],
                ],
            ],
        ],
    ],

    'services' => [
        'group' => 'Services',

        'cloud' => [
            'title'       => 'Cloud Hosting',
            'description' => 'Cost-effective, managed cloud hosting — deploy your ERP in minutes, fully optimised, secure and scalable.',
            'button'      => 'See Pricing',
        ],

        'support' => [
            'title'       => 'Support & Maintenance',
            'description' => 'Dedicated technical support and ongoing maintenance plans to keep your ERP secure, updated and running smoothly.',
            'button'      => 'View Details',
        ],

        'paid' => [
            'title'       => 'Paid Services',
            'description' => 'Expert help for module development, customisation, data migration, version upgrades and bespoke integrations.',
            'button'      => 'Get Started',
        ],
    ],

    'resources' => [
        'group' => 'Resources & Documentation',

        'extensions' => [
            'title'       => 'Modules',
            'description' => 'Browse official and community add-ons to extend :app with new modules and features.',
            'button'      => 'Browse Modules',
        ],

        'docs' => [
            'title'       => 'Dev Docs',
            'description' => 'Developer guides and references to help you build plugins, customise modules and extend the platform.',
            'button'      => 'Read Docs',
        ],

        'guide' => [
            'title'       => 'User Guide',
            'description' => 'Step-by-step guides covering setup, configuration and everyday use of every ERP module.',
            'button'      => 'Open Guide',
        ],

        'website' => [
            'title'       => 'Product Site',
            'description' => 'Module overviews, release notes and everything :app can do, on the product website.',
            'button'      => 'Visit Website',
        ],
    ],

    'empty' => 'Help links have not been set up for this :app installation yet. Contact your system administrator for assistance.',

    'contact' => [
        'title'       => 'Still need a hand?',
        'description' => 'Talk to our team about hosting, implementation, custom development or anything else.',
        'live_chat'   => 'Live Chat',
        'button'      => 'Contact Us',
    ],
];
