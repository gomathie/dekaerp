# Multi-company support

DekaERP can run multiple legal entities or branches from a single installation. The multi-company feature lets administrators create companies, assign users to them, and switch the active company context from the admin panel.

## Overview

Each company can be created as a parent company or as a branch of another company. Users can be granted access to one or more companies, and each user can define a default company for the workspace.

## What administrators can do

- Create and manage companies from the Companies section.
- Define parent/branch relationships between companies.
- Assign allowed companies and a default company to each user.
- Use the company switcher in the admin top bar to change the active company context.

## How the feature works

- The active company selection is stored in the user session.
- The system checks the user's allowed companies and default company to determine what they can access.
- When a specific company is selected, most admin screens are filtered to that company automatically.
- The option "All companies" shows data from all allowed companies, while still keeping records with no company assigned visible as shared records.

## User experience

When a user logs into the admin panel:

1. They can choose a company from the company switcher in the top bar.
2. The current selection becomes the active company context for the session.
3. New records inherit that company automatically when no company is explicitly set.
4. Existing lists and forms are scoped to the selected company context.

## Configuration for developers

The scoping behavior is configured in the company scope configuration file:

- [plugins/webkul/support/config/company-scope.php](../plugins/webkul/support/config/company-scope.php)

Any model that should be scoped by company must have a `company_id` column and should be added to the configured model list. Models without a company field remain unaffected.

## Notes

- Records with `company_id = null` are treated as shared and stay visible across companies.
- This behavior is primarily applied in the admin panel. Console commands, queues, and APIs keep their normal behavior unless they explicitly use the company context.
