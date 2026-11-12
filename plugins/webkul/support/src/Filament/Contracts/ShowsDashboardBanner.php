<?php

namespace Webkul\Support\Filament\Contracts;

/**
 * Marks a module overview that is a plain Filament Page (not a Dashboard
 * subclass) as a dashboard, so the welcome banner shows on it too
 * (DashboardBannerContributions). Dashboard subclasses need nothing.
 */
interface ShowsDashboardBanner {}
