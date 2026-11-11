<?php

namespace Webkul\Support\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum NavigationGroup: string implements HasIcon, HasLabel
{
    case Dashboard = 'dashboard';

    case Contact = 'contact';

    case Sale = 'sale';

    case Purchase = 'purchase';

    case Invoice = 'invoice';

    case Accounting = 'accounting';

    case Maintenance = 'maintenance';

    case Manufacturing = 'manufacturing';

    case Inventory = 'inventory';

    case Logistics = 'logistics';

    case Barcode = 'barcode';

    case Project = 'project';

    case Employee = 'employee';

    case TimeOff = 'time-off';

    case Recruitment = 'recruitment';

    case Plugin = 'plugin';

    case Setting = 'setting';

    case Help = 'help';

    public function getLabel(): string
    {
        return __('admin.navigation.'.$this->value);
    }

    /**
     * The heading this module sits under in the module sidebar (user,
     * 2026-11-10). Modules stay where users know them; the heading only
     * groups them. Case order is the sidebar order, so the cases of one
     * section must stay next to each other, or its heading prints twice.
     * Null means no heading (the top of the list).
     */
    public function section(): ?string
    {
        return match ($this) {
            self::Dashboard, self::Contact => null,
            self::Sale, self::Purchase, self::Invoice => 'sales-purchases',
            self::Accounting => 'finance',
            self::Maintenance, self::Manufacturing, self::Inventory, self::Logistics, self::Barcode => 'operations',
            self::Project => 'projects',
            self::Employee, self::TimeOff, self::Recruitment => 'human-resources',
            self::Plugin, self::Setting, self::Help => 'administration',
        };
    }

    public function getSectionLabel(): ?string
    {
        $section = $this->section();

        return $section === null ? null : __('admin.navigation-sections.'.$section);
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Dashboard     => 'icon-dashboard',
            self::Contact       => 'icon-contacts',
            self::Sale          => 'icon-sales',
            self::Purchase      => 'icon-purchases',
            self::Maintenance   => 'icon-maintenance',
            self::Manufacturing => 'icon-manufacturing',
            self::Inventory     => 'icon-inventories',
            self::Logistics     => 'icon-logistics',
            self::Invoice       => 'icon-invoices',
            self::Accounting    => 'icon-accounting',
            self::Project       => 'icon-projects',
            self::Employee      => 'icon-employees',
            self::TimeOff       => 'icon-time-offs',
            self::Recruitment   => 'icon-recruitments',
            self::Barcode       => 'icon-barcode',
            self::Plugin        => 'icon-plugin',
            self::Setting       => 'icon-settings',
            self::Help          => 'icon-help',
        };
    }
}
