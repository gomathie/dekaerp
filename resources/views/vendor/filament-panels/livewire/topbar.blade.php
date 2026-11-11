<div class="fi-topbar-ctn">
    @php
        $navigation = filament()->getNavigation();
        $isRtl = __('filament-panels::layout.direction') === 'rtl';
        $isSidebarCollapsibleOnDesktop = filament()->isSidebarCollapsibleOnDesktop();
        $isSidebarFullyCollapsibleOnDesktop = filament()->isSidebarFullyCollapsibleOnDesktop();
        $hasTopNavigation = filament()->hasTopNavigation();
        $hasNavigation = filament()->hasNavigation();
        $hasTenancy = filament()->hasTenancy();
        $isAdminPanel = filament()->getCurrentPanel()->getId() === 'admin';
    @endphp

    <nav class="fi-topbar">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_START) }}

        @if ($hasNavigation)
            <x-filament::icon-button
                color="gray"
                :icon="\Filament\Support\Icons\Heroicon::OutlinedBars3"
                :icon-alias="\Filament\View\PanelsIconAlias::TOPBAR_OPEN_SIDEBAR_BUTTON"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.open()"
                x-show="! $store.sidebar.isOpen"
                class="fi-topbar-open-sidebar-btn"
            />

            <x-filament::icon-button
                color="gray"
                :icon="\Filament\Support\Icons\Heroicon::OutlinedXMark"
                :icon-alias="\Filament\View\PanelsIconAlias::TOPBAR_CLOSE_SIDEBAR_BUTTON"
                icon-size="lg"
                :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                x-cloak
                x-data="{}"
                x-on:click="$store.sidebar.close()"
                x-show="$store.sidebar.isOpen"
                class="fi-topbar-close-sidebar-btn"
            />

            @if ($isAdminPanel)
                {{-- Module menu trigger: toggles sidebar collapse on desktop/tablet, opens the drawer on mobile --}}
                <x-filament::icon-button
                    icon="icon-menu"
                    color="gray"
                    x-data="{}"
                    x-on:click="$store.moduleSidebar.primaryToggle()"
                    :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                    class="fi-topbar-module-sidebar-toggle"
                />

                {{-- Phones (2026-11-10, mobile plan P2): one labelled 44px Menu
                     button replaces the two unlabelled 24px icons. It opens the
                     module drawer, which also lists this module's pages. --}}
                <button
                    type="button"
                    x-data="{}"
                    x-on:click="$store.moduleSidebar.primaryToggle()"
                    class="module-mobile-menu-btn"
                    aria-label="{{ __('admin.mobile-menu.menu') }}"
                >
                    <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::OutlinedBars3" class="module-mobile-menu-icon" />
                    <span>{{ __('admin.mobile-menu.menu') }}</span>
                </button>
            @endif
        @endif

        <div class="fi-topbar-start" style="margin-right: 0">
            @if ($isSidebarCollapsibleOnDesktop)
                <x-filament::icon-button
                    color="gray"
                    :icon="$isRtl ? \Filament\Support\Icons\Heroicon::OutlinedChevronLeft : \Filament\Support\Icons\Heroicon::OutlinedChevronRight"
                    :icon-alias="
                        $isRtl
                            ? [
                                \Filament\View\PanelsIconAlias::SIDEBAR_EXPAND_BUTTON_RTL,
                                \Filament\View\PanelsIconAlias::SIDEBAR_EXPAND_BUTTON,
                            ]
                            : \Filament\View\PanelsIconAlias::SIDEBAR_EXPAND_BUTTON
                    "
                    icon-size="lg"
                    :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                    x-cloak
                    x-data="{}"
                    x-on:click="$store.sidebar.open()"
                    x-show="! $store.sidebar.isOpen"
                    class="fi-topbar-open-collapse-sidebar-btn"
                />
            @endif

            @if ($isSidebarCollapsibleOnDesktop || $isSidebarFullyCollapsibleOnDesktop)
                <x-filament::icon-button
                    color="gray"
                    :icon="$isRtl ? \Filament\Support\Icons\Heroicon::OutlinedChevronRight : \Filament\Support\Icons\Heroicon::OutlinedChevronLeft"
                    :icon-alias="
                        $isRtl
                            ? [
                                \Filament\View\PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON_RTL,
                                \Filament\View\PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON,
                            ]
                            : \Filament\View\PanelsIconAlias::SIDEBAR_COLLAPSE_BUTTON
                    "
                    icon-size="lg"
                    :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                    x-cloak
                    x-data="{}"
                    x-on:click="$store.sidebar.close()"
                    x-show="$store.sidebar.isOpen"
                    class="fi-topbar-close-collapse-sidebar-btn"
                />
            @endif

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_LOGO_BEFORE) }}

            @if ($homeUrl = filament()->getHomeUrl())
                <a {{ \Filament\Support\generate_href_html($homeUrl) }}>
                    <x-filament-panels::logo />
                </a>
            @else
                <x-filament-panels::logo />
            @endif

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_LOGO_AFTER) }}
        </div>

        @if ($hasTopNavigation || (! $hasNavigation))
            @if ($hasTenancy && filament()->hasTenantMenu())
                <x-filament-panels::tenant-menu />
            @endif

            @if ($hasNavigation)
                <ul class="fi-topbar-nav-groups">
                    @foreach ($navigation as $group)
                        @php
                            $groupLabel = $group->getLabel();
                            $groupExtraTopbarAttributeBag = $group->getExtraTopbarAttributeBag();
                            $isGroupActive = $group->isActive();
                            $groupIcon = $group->getIcon();

                            if ($isAdminPanel && ! $isGroupActive) {
                                continue;
                            }
                        @endphp

                        @if ($groupLabel)
                            @if ($isAdminPanel)
                                {{-- Admin panel: show active group name as a plain bold heading --}}
                                <li class="fi-topbar-item">
                                    <span class="px-3 py-2 text-xl font-bold">
                                        <a {{ \Filament\Support\generate_href_html($group->getItems()->first()->getUrl()) }}>
                                            {{ $groupLabel }}
                                        </a>
                                    </span>
                                </li>

                                @foreach ($group->getItems() as $item)
                                    @php
                                        $isItemActive = $item->isActive();
                                        $itemActiveIcon = $item->getActiveIcon();
                                        $itemBadge = $item->getBadge();
                                        $itemBadgeColor = $item->getBadgeColor();
                                        $itemBadgeTooltip = $item->getBadgeTooltip();
                                        $itemIcon = $item->getIcon();
                                        $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                        $itemUrl = $item->getUrl();
                                    @endphp

                                    <x-filament-panels::topbar.item
                                        :active="$isItemActive"
                                        :active-icon="$itemActiveIcon"
                                        :badge="$itemBadge"
                                        :badge-color="$itemBadgeColor"
                                        :badge-tooltip="$itemBadgeTooltip"
                                        :icon="$itemIcon"
                                        :should-open-url-in-new-tab="$shouldItemOpenUrlInNewTab"
                                        :url="$itemUrl"
                                    >
                                        {{ $item->getLabel() }}
                                    </x-filament-panels::topbar.item>
                                @endforeach
                            @else
                                <x-filament::dropdown
                                    placement="bottom-start"
                                    teleport
                                    :attributes="\Filament\Support\prepare_inherited_attributes($groupExtraTopbarAttributeBag)"
                                >
                                    <x-slot name="trigger">
                                        <x-filament-panels::topbar.item
                                            :active="$isGroupActive"
                                            :icon="$groupIcon"
                                        >
                                            {{ $groupLabel }}
                                        </x-filament-panels::topbar.item>
                                    </x-slot>

                                    @php
                                        $lists = [];

                                        foreach ($group->getItems() as $item) {
                                            if ($childItems = $item->getChildItems()) {
                                                $lists[] = [$item, ...$childItems];
                                                $lists[] = [];

                                                continue;
                                            }

                                            if (empty($lists)) {
                                                $lists[] = [$item];

                                                continue;
                                            }

                                            $lists[count($lists) - 1][] = $item;
                                        }

                                        if (! empty($lists) && empty($lists[count($lists) - 1])) {
                                            array_pop($lists);
                                        }
                                    @endphp

                                    @foreach ($lists as $list)
                                        <x-filament::dropdown.list>
                                            @foreach ($list as $item)
                                                @php
                                                    $isItemActive = $item->isActive();
                                                    $itemBadge = $item->getBadge();
                                                    $itemBadgeColor = $item->getBadgeColor();
                                                    $itemBadgeTooltip = $item->getBadgeTooltip();
                                                    $itemUrl = $item->getUrl();
                                                    $itemIcon = $isItemActive
                                                        ? ($item->getActiveIcon() ?? $item->getIcon())
                                                        : $item->getIcon();
                                                    $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                                @endphp

                                                <x-filament::dropdown.list.item
                                                    :badge="$itemBadge"
                                                    :badge-color="$itemBadgeColor"
                                                    :badge-tooltip="$itemBadgeTooltip"
                                                    :color="$isItemActive ? 'primary' : 'gray'"
                                                    :href="$itemUrl"
                                                    :icon="$itemIcon"
                                                    tag="a"
                                                    :target="$shouldItemOpenUrlInNewTab ? '_blank' : null"
                                                >
                                                    {{ $item->getLabel() }}
                                                </x-filament::dropdown.list.item>
                                            @endforeach
                                        </x-filament::dropdown.list>
                                    @endforeach
                                </x-filament::dropdown>
                            @endif
                        @else
                            @foreach ($group->getItems() as $item)
                                @php
                                    $isItemActive = $item->isActive();
                                    $itemActiveIcon = $item->getActiveIcon();
                                    $itemBadge = $item->getBadge();
                                    $itemBadgeColor = $item->getBadgeColor();
                                    $itemBadgeTooltip = $item->getBadgeTooltip();
                                    $itemIcon = $item->getIcon();
                                    $shouldItemOpenUrlInNewTab = $item->shouldOpenUrlInNewTab();
                                    $itemUrl = $item->getUrl();
                                @endphp

                                <x-filament-panels::topbar.item
                                    :active="$isItemActive"
                                    :active-icon="$itemActiveIcon"
                                    :badge="$itemBadge"
                                    :badge-color="$itemBadgeColor"
                                    :badge-tooltip="$itemBadgeTooltip"
                                    :icon="$itemIcon"
                                    :should-open-url-in-new-tab="$shouldItemOpenUrlInNewTab"
                                    :url="$itemUrl"
                                >
                                    {{ $item->getLabel() }}
                                </x-filament-panels::topbar.item>
                            @endforeach
                        @endif
                    @endforeach
                </ul>
            @endif
        @endif

        <div
            @if ($hasTenancy)
                x-persist="topbar.end.panel-{{ filament()->getId() }}.tenant-{{ filament()->getTenant()?->getKey() }}"
            @else
                x-persist="topbar.end.panel-{{ filament()->getId() }}"
            @endif
            class="fi-topbar-end"
        >
            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_BEFORE) }}

            @if (filament()->isGlobalSearchEnabled())
                @livewire(Filament\Livewire\GlobalSearch::class)
            @endif

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::GLOBAL_SEARCH_AFTER) }}

            @if (filament()->auth()->check())
                @if (filament()->hasDatabaseNotifications())
                    @livewire(Filament\Livewire\DatabaseNotifications::class, [
                        'lazy' => filament()->hasLazyLoadedDatabaseNotifications(),
                    ])
                @endif

                @if (filament()->hasUserMenu())
                    <x-filament-panels::user-menu />
                @endif
            @endif
        </div>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_END) }}
    </nav>

    <x-filament-actions::modals />

@if ($isAdminPanel && $hasNavigation)
        {{-- No-flash: apply persisted collapsed state before the main content paints --}}
        <script>
            (function () {
                try {
                    var collapsed = localStorage.getItem('moduleSidebarCollapsed') === '1';
                    var el = document.documentElement;
                    el.classList.add('module-sidebar-ready');
                    el.classList.toggle('module-sidebar-collapsed', collapsed);
                } catch (e) {}
            })();
        </script>

        {{-- Modern collapsible module sidebar (replaces the app-launcher dropdown grid) --}}
        <aside
            x-data="{}"
            :class="{
                'is-collapsed': $store.moduleSidebar.collapsed,
                'is-mobile-open': $store.moduleSidebar.mobileOpen,
            }"
            class="module-sidebar"
        >
            <div class="module-sidebar-header">
                <button
                    type="button"
                    class="module-sidebar-close"
                    x-on:click="$store.moduleSidebar.closeMobile()"
                    aria-label="{{ __('filament-panels::layout.actions.sidebar.collapse.label') }}"
                >
                    <x-filament::icon :icon="\Filament\Support\Icons\Heroicon::OutlinedXMark" />
                </button>
            </div>

            <nav class="module-sidebar-nav">
                @php
                    $previousSection = null;
                    $activeModuleGroup = collect($navigation)->first(fn ($group) => $group->isActive());
                @endphp

                {{-- Phones only (mobile plan P2): the current module's pages, which
                     the top bar hides under 768px. Desktop shows them in the top bar. --}}
                @if ($activeModuleGroup && $activeModuleGroup->getItems()->isNotEmpty())
                    <div class="module-sidebar-current">
                        <div class="module-sidebar-section" role="presentation">
                            <span class="module-sidebar-section-label">{{ $activeModuleGroup->getLabel() }}</span>
                        </div>

                        @foreach ($activeModuleGroup->getItems() as $pageItem)
                            @foreach ([$pageItem, ...$pageItem->getChildItems()] as $entry)
                                @if ($entry->getUrl())
                                    <div @class(['module-sidebar-item', 'fi-active' => $entry->isActive()])>
                                        <a href="{{ $entry->getUrl() }}" class="module-sidebar-link module-sidebar-page-link">
                                            <span class="module-sidebar-label">{{ $entry->getLabel() }}</span>
                                        </a>
                                    </div>
                                @endif
                            @endforeach
                        @endforeach

                        <div class="module-sidebar-section" role="presentation">
                            <span class="module-sidebar-section-label">{{ __('admin.mobile-menu.modules') }}</span>
                        </div>
                    </div>
                @endif

                @foreach ($navigation as $group)
                    @php
                        $groupLabel = $group->getLabel();
                        $groupIcon = $group->getIcon();
                        $itemUrl = $group->getItems()->first()?->getUrl();
                        $groupCase = $group->getItems()->first()?->getGroup();
                        $section = $groupCase instanceof \Webkul\Support\Enums\NavigationGroup ? $groupCase->section() : null;
                    @endphp

                    @if (! $groupLabel || ! $itemUrl || ! $groupIcon)
                        @continue
                    @endif

                    {{-- Section heading (user, 2026-11-10): modules keep their place; a heading groups them. --}}
                    @if ($section !== null && $section !== $previousSection)
                        <div class="module-sidebar-section" role="presentation">
                            <span class="module-sidebar-section-label">{{ $groupCase->getSectionLabel() }}</span>
                        </div>
                    @endif

                    @php
                        $previousSection = $section;
                    @endphp

                    <div
                        @class([
                            'module-sidebar-item',
                            'fi-active' => $group->isActive(),
                        ])
                    >
                        <a
                            href="{{ $itemUrl }}"
                            title="{{ $groupLabel }}"
                            class="module-sidebar-link"
                        >
                            <x-filament::icon :icon="$groupIcon" class="module-sidebar-icon" />

                            <span class="module-sidebar-label">{{ $groupLabel }}</span>
                        </a>
                    </div>
                @endforeach
            </nav>
        </aside>

        {{-- Mobile drawer backdrop --}}
        <div
            x-data="{}"
            x-show="$store.moduleSidebar.mobileOpen"
            x-on:click="$store.moduleSidebar.closeMobile()"
            x-transition.opacity
            x-cloak
            class="module-sidebar-backdrop"
        ></div>

        @assets
            <style>
                @verbatim
                :root {
                    /* 240px, up from 200px: customers found module names
                       too cramped (user, 2026-11-11). */
                    --module-sidebar-width: 240px;
                    --module-sidebar-width-collapsed: 72px;
                    --module-topbar-height: 4rem;
                }

                /* The module's page strip wrapped onto a second line between
                   about 1024px and 1200px, putting "Logistics" under the
                   module title (user, 2026-11-11). Keep it on one line and
                   let it scroll sideways; its entries are plain links, so
                   there is no dropdown to clip. */
                .fi-topbar-nav-groups {
                    flex-wrap: nowrap;
                    min-width: 0;
                    overflow-x: auto;
                    overflow-y: hidden;
                    scrollbar-width: none;
                }

                .fi-topbar-nav-groups::-webkit-scrollbar {
                    display: none;
                }

                .fi-topbar-nav-groups > li {
                    flex-shrink: 0;
                    white-space: nowrap;
                }

                /* The profile avatar was a dark circle on a dark bar in dark
                   mode, so people could not find the profile menu (user,
                   2026-11-11). A ring in the accent colour marks it in both
                   themes; a brighter one on hover and keyboard focus. */
                .fi-topbar .fi-user-menu-trigger .fi-avatar {
                    box-shadow: 0 0 0 2px var(--color-white, #fff), 0 0 0 4px #F59E0B;
                }

                .dark .fi-topbar .fi-user-menu-trigger .fi-avatar {
                    box-shadow: 0 0 0 2px var(--color-gray-900, #111827), 0 0 0 4px #FBBF24;
                }

                .fi-topbar .fi-user-menu-trigger:hover .fi-avatar,
                .fi-topbar .fi-user-menu-trigger:focus-visible .fi-avatar {
                    box-shadow: 0 0 0 2px var(--color-white, #fff), 0 0 0 4px #D97706;
                }

                .dark .fi-topbar .fi-user-menu-trigger:hover .fi-avatar,
                .dark .fi-topbar .fi-user-menu-trigger:focus-visible .fi-avatar {
                    box-shadow: 0 0 0 2px var(--color-gray-900, #111827), 0 0 0 4px #FDE68A;
                }

                /* Topbar stays full-width and sits above the sidebar; only the content column is pushed over (tablet & up) */
                @media (min-width: 768px) {
                    /* Lists always show on wider screens, so their phone-only
                       toggle is not offered there (2026-11-11). */
                    .dashboard-lists-toggle {
                        display: none !important;
                    }

                    html.module-sidebar-ready .fi-layout {
                        padding-inline-start: var(--module-sidebar-width);
                        transition: padding-inline-start 0.25s ease;
                    }

                    html.module-sidebar-ready.module-sidebar-collapsed .fi-layout {
                        padding-inline-start: var(--module-sidebar-width-collapsed);
                    }
                }

                /* Sidebar shell — starts below the fixed topbar, not behind it */
                .module-sidebar {
                    position: fixed;
                    top: var(--module-topbar-height);
                    height: calc(100vh - var(--module-topbar-height));
                    inset-inline-start: 0;
                    z-index: 20;
                    display: flex;
                    flex-direction: column;
                    width: var(--module-sidebar-width);
                    background-color: var(--gray-50, #f9fafb);
                    border-inline-end: 1px solid var(--gray-200, #e5e7eb);
                    transition: width 0.25s ease, transform 0.25s ease;
                    overflow: hidden;
                }

                .dark .module-sidebar {
                    background-color: var(--gray-900, #111827);
                    border-inline-end-color: var(--gray-800, #1f2937);
                }

                .module-sidebar.is-collapsed {
                    width: var(--module-sidebar-width-collapsed);
                }

                /* Header — only holds the mobile drawer close button (hidden on desktop/tablet) */
                .module-sidebar-header {
                    display: none;
                    align-items: center;
                    justify-content: flex-end;
                    min-height: 4rem;
                    padding: 0.5rem 0.75rem;
                    border-block-end: 1px solid var(--gray-200, #e5e7eb);
                }

                .dark .module-sidebar-header {
                    border-block-end-color: var(--gray-800, #1f2937);
                }

                .module-sidebar-close {
                    display: inline-flex;
                    align-items: center;
                    justify-content: center;
                    width: 2rem;
                    height: 2rem;
                    border-radius: 0.5rem;
                    color: var(--gray-500, #6b7280);
                    cursor: pointer;
                    transition: background-color 0.15s ease, color 0.15s ease;
                }

                .module-sidebar-close:hover {
                    background-color: var(--gray-100, #f3f4f6);
                    color: var(--gray-700, #374151);
                }

                .dark .module-sidebar-close:hover {
                    background-color: var(--gray-800, #1f2937);
                    color: var(--gray-200, #e5e7eb);
                }

                /* Navigation list */
                .module-sidebar-nav {
                    flex: 1;
                    overflow-x: hidden;
                    overflow-y: auto;
                    display: flex;
                    flex-direction: column;
                    gap: 0.125rem;
                    padding: 0.5rem;
                }

                /* Sidebars still scroll (wheel, touch, keyboard) but show no
                   scrollbar (user, 2026-11-11). */
                .module-sidebar-nav,
                .fi-sidebar-nav {
                    scrollbar-width: none;
                    -ms-overflow-style: none;
                }

                .module-sidebar-nav::-webkit-scrollbar,
                .fi-sidebar-nav::-webkit-scrollbar {
                    display: none;
                }

                .module-sidebar-link {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    padding: 0.5rem 0.625rem;
                    border-radius: 0.5rem;
                    color: var(--gray-700, #374151);
                    font-size: 0.875rem;
                    font-weight: 500;
                    line-height: 1.25rem;
                    white-space: nowrap;
                    text-decoration: none;
                    transition: background-color 0.15s ease, color 0.15s ease;
                }

                .module-sidebar-link:hover {
                    background-color: var(--gray-100, #f3f4f6);
                    color: var(--gray-900, #111827);
                }

                .dark .module-sidebar-link {
                    color: var(--gray-300, #d1d5db);
                }

                .dark .module-sidebar-link:hover {
                    background-color: var(--gray-800, #1f2937);
                    color: #fff;
                }

                /* Outline module icons (resources/svg, user 2026-11-11):
                   they fill their whole box, unlike the old tiled ones
                   whose artwork showed at ~15px, so 22px reads clearly.
                   Main strokes use currentColor (dark, light in dark mode,
                   brand when active); the accent stays #F59E0B. */
                .module-sidebar-icon {
                    width: 1.375rem;
                    height: 1.375rem;
                    flex-shrink: 0;
                }

                /* Active item — reuse the app's primary color with a subtle highlight */
                .module-sidebar-item.fi-active .module-sidebar-link {
                    background-color: var(--primary-50, #eff6ff);
                    color: var(--primary-600, #2563eb);
                    font-weight: 600;
                }

                .dark .module-sidebar-item.fi-active .module-sidebar-link {
                    background-color: color-mix(in srgb, var(--primary-500, #3b82f6) 18%, transparent);
                    color: var(--primary-300, #93c5fd);
                }

                /* Section headings */
                .module-sidebar-section {
                    margin-block-start: 0.75rem;
                    padding: 0.25rem 0.625rem;
                    border-block-start: 1px solid var(--gray-200, #e5e7eb);
                }

                .dark .module-sidebar-section {
                    border-block-start-color: var(--gray-800, #1f2937);
                }

                .module-sidebar-section-label {
                    display: block;
                    padding-block-start: 0.5rem;
                    color: var(--gray-500, #6b7280);
                    font-size: 0.6875rem;
                    font-weight: 600;
                    letter-spacing: 0.06em;
                    text-transform: uppercase;
                    white-space: nowrap;
                }

                .dark .module-sidebar-section-label {
                    color: var(--gray-400, #9ca3af);
                }

                /* Collapsed: icons only; a heading shrinks to its divider */
                .module-sidebar.is-collapsed .module-sidebar-label,
                .module-sidebar.is-collapsed .module-sidebar-section-label {
                    display: none;
                }

                .module-sidebar.is-collapsed .module-sidebar-section {
                    margin-block-start: 0.375rem;
                    padding-block: 0.375rem 0;
                }

                .module-sidebar.is-collapsed .module-sidebar-link {
                    justify-content: center;
                    padding-inline: 0;
                }

                /* Mobile: slide-out drawer (labels always visible, collapse disabled) */
                .module-sidebar-backdrop {
                    position: fixed;
                    top: var(--module-topbar-height);
                    inset-inline: 0;
                    bottom: 0;
                    z-index: 15;
                    background-color: rgba(0, 0, 0, 0.5);
                }

                @media (min-width: 768px) {
                    .module-sidebar-backdrop {
                        display: none !important;
                    }
                }

                @media (max-width: 767px) {
                    .module-sidebar {
                        width: var(--module-sidebar-width);
                        transform: translateX(-100%);
                        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -4px rgba(0, 0, 0, 0.1);
                    }

                    [dir="rtl"] .module-sidebar {
                        transform: translateX(100%);
                    }

                    .module-sidebar.is-mobile-open,
                    [dir="rtl"] .module-sidebar.is-mobile-open {
                        transform: translateX(0);
                    }

                    .module-sidebar.is-collapsed {
                        width: var(--module-sidebar-width);
                    }

                    .module-sidebar.is-collapsed .module-sidebar-label,
                    .module-sidebar.is-collapsed .module-sidebar-section-label {
                        display: inline;
                    }

                    .module-sidebar.is-collapsed .module-sidebar-link {
                        justify-content: flex-start;
                        padding-inline: 0.625rem;
                    }

                    .module-sidebar-header {
                        display: flex;
                    }
                }

                /* ---- Mobile plan P1-P6 (2026-11-10). Phones only; desktop from
                   768px up is untouched. See docs/mobile-view-plan.md. ---- */

                /* P2: the labelled Menu button and "This module" exist on phones only. */
                .module-mobile-menu-btn,
                .module-sidebar-current {
                    display: none;
                }

                @media (max-width: 767px) {
                    /* P2: one Menu button replaces the two unlabelled 24px icons.
                       Filament's own page-sidebar toggles go: the drawer now lists
                       this module's pages too. */
                    .fi-topbar-open-sidebar-btn,
                    .fi-topbar-close-sidebar-btn,
                    .fi-topbar-module-sidebar-toggle {
                        display: none !important;
                    }

                    .module-mobile-menu-btn {
                        display: inline-flex;
                        align-items: center;
                        gap: 0.375rem;
                        min-block-size: 44px;
                        min-inline-size: 44px;
                        padding-inline: 0.5rem;
                        border-radius: 0.5rem;
                        color: var(--gray-700, #374151);
                        font-size: 0.875rem;
                        font-weight: 600;
                        flex-shrink: 0;
                    }

                    .dark .module-mobile-menu-btn {
                        color: var(--gray-200, #e5e7eb);
                    }

                    .module-mobile-menu-icon {
                        inline-size: 1.5rem;
                        block-size: 1.5rem;
                    }

                    .module-sidebar-current {
                        display: block;
                    }

                    .module-sidebar-current .module-sidebar-section:first-child {
                        margin-block-start: 0;
                        border-block-start: 0;
                    }

                    .module-sidebar-page-link {
                        padding-inline-start: 0.875rem;
                        min-block-size: 44px;
                    }

                    .module-sidebar-link {
                        min-block-size: 44px;
                    }

                    .module-sidebar {
                        inline-size: min(85vw, 300px);
                    }

                    /* P3: the top bar fits; bell and avatar stay whole. */
                    .fi-topbar {
                        gap: 0.25rem;
                        padding-inline: 0.5rem;
                    }

                    .fi-topbar-start {
                        min-inline-size: 0;
                    }

                    .fi-topbar-end {
                        min-inline-size: 0;
                        gap: 0.25rem;
                    }

                    .company-switcher-name {
                        display: inline-block;
                        max-inline-size: 5.5rem;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        vertical-align: middle;
                    }

                    /* Search is an icon until tapped, then takes the room it needs. */
                    .fi-global-search-field {
                        max-inline-size: 2.75rem;
                        transition: max-inline-size 0.2s ease;
                    }

                    .fi-global-search-field input::placeholder {
                        color: transparent;
                    }

                    /* Capped by the screen width, and the company name steps
                       aside while searching, so the bell and avatar stay on
                       screen at 390px (review, 2026-11-11). */
                    .fi-global-search-field:focus-within {
                        max-inline-size: min(11rem, 40vw);
                    }

                    .fi-topbar:has(.fi-global-search-field:focus-within) .company-switcher-name {
                        display: none;
                    }

                    /* The company switcher's 320px panel opened from the
                       button's left edge and ran past the right of a 390px
                       screen, cutting off its Reset button (user, 2026-11-11).
                       On phones it is placed from the right with the page
                       gutter, and never starts left of the screen. Only
                       `left` is overridden: Filament sets its width inside a
                       cascade layer, which this sheet cannot outrank. */
                    /* Card lists (contentGrid: contacts, customers, vendors,
                       employees, departments, candidates, jobs, projects,
                       plugins) on phones (user, 2026-11-11; mobile plan P7).
                       Cards were 212px with an empty picture row, and over
                       300px with a logo drawn 200px tall, so a screen held
                       about three. Now: the checkbox sits in the corner,
                       a picture is a 48px thumbnail beside the name, and a
                       missing picture takes no space. Only the card's outer
                       stack turns sideways; its text column stays stacked. */
                    .fi-ta-content-grid .fi-ta-record {
                        position: relative;
                    }

                    .fi-ta-content-grid .fi-ta-record > .fi-ta-record-checkbox {
                        position: absolute;
                        top: 0.75rem;
                        inset-inline-end: 0.75rem;
                        margin: 0;
                    }

                    .fi-ta-content-grid .fi-ta-record-content > .fi-grid-col > .fi-ta-stack {
                        flex-direction: row;
                        align-items: flex-start;
                        gap: 0.75rem;
                        padding-inline-end: 1.5rem;
                    }

                    .fi-ta-content-grid .fi-ta-record-content > .fi-grid-col > .fi-ta-stack > .fi-ta-col:has(.fi-ta-image) {
                        flex: 0 0 auto;
                        width: auto;
                    }

                    .fi-ta-content-grid .fi-ta-record-content > .fi-grid-col > .fi-ta-stack > .fi-ta-col:has(.fi-ta-image):not(:has(img)) {
                        display: none;
                    }

                    .fi-ta-content-grid .fi-ta-record-content > .fi-grid-col > .fi-ta-stack > .fi-grid-col:not(:has(.fi-ta-image)) {
                        min-width: 0;
                        flex: 1 1 auto;
                    }

                    .fi-ta-content-grid .fi-ta-image img {
                        width: 3rem !important;
                        height: 3rem !important;
                        max-width: 3rem !important;
                        object-fit: contain;
                        border-radius: 0.5rem;
                    }

                    /* Dashboards on phones (user, 2026-11-11: "don't make the
                       dashboard too long"). The Invoice dashboard was about
                       4.5 screens: stat cards now sit two per row, and the
                       "Top" lists stay hidden until the page's Show lists
                       button adds .dashboard-show-lists. Filters fold by
                       themselves (DashboardFilters). Charts stay. */
                    [data-dashboard-widgets] .fi-wi-stats-overview .fi-section-content.fi-grid {
                        grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                        gap: 0.5rem;
                    }

                    /* Overview only (it has the Show details button): on a
                       phone just "At a glance" shows; charts, detailed stats
                       and lists wait for the button (user, 2026-11-11).
                       Scoped to [data-overview]: other dashboards have no
                       button, so hiding there would make lists unreachable. */
                    [data-overview]:not(.dashboard-show-lists) .fi-wi-table,
                    [data-overview]:not(.dashboard-show-lists) .fi-wi-chart,
                    [data-overview]:not(.dashboard-show-lists) .fi-wi-stats-overview:not(:has(.overview-headline-stat)) {
                        display: none;
                    }

                    .fi-dropdown-panel:has(.company-switcher-form) {
                        left: max(0px, calc(100% - 20rem - 1rem)) !important;
                    }

                    .fi-topbar-end .fi-icon-btn,
                    .fi-topbar-end .fi-user-menu-trigger {
                        flex-shrink: 0;
                    }

                    /* P1: 16px fields, so iPhone Safari does not zoom in on focus. */
                    .fi-body input:not([type='checkbox']):not([type='radio']):not([type='range']),
                    .fi-body select,
                    .fi-body textarea {
                        font-size: 16px !important;
                    }

                    /* P4: tab strips scroll on one line, with a fade showing more. */
                    .fi-tabs {
                        flex-wrap: nowrap;
                        overflow-x: auto;
                        scrollbar-width: none;
                        -webkit-overflow-scrolling: touch;
                        mask-image: linear-gradient(to right, #000 85%, transparent);
                    }

                    [dir='rtl'] .fi-tabs {
                        mask-image: linear-gradient(to left, #000 85%, transparent);
                    }

                    .fi-tabs::-webkit-scrollbar {
                        display: none;
                    }

                    .fi-tabs-item {
                        flex-shrink: 0;
                        white-space: nowrap;
                    }

                    /* P5: fingertip-sized hit areas (padding, not bigger icons). */
                    .fi-icon-btn {
                        min-inline-size: 40px;
                        min-block-size: 40px;
                    }

                    .fi-ta-actions .fi-link,
                    .fi-pagination-item-btn {
                        min-block-size: 40px;
                        padding-block: 0.5rem;
                    }

                    /* P6: the record status bar scrolls on one line, not wraps. */
                    .state-container {
                        flex-wrap: nowrap !important;
                        justify-content: flex-start !important;
                        overflow-x: auto;
                        scrollbar-width: none;
                    }

                    .state-container::-webkit-scrollbar {
                        display: none;
                    }

                    .state-container .state {
                        flex-shrink: 0;
                    }
                }
                @endverbatim
            </style>

            <script>
                (function () {
                    function registerModuleSidebarStore() {
                        if (! window.Alpine || Alpine.store('moduleSidebar')) {
                            return;
                        }

                        Alpine.store('moduleSidebar', {
                            collapsed: false,
                            mobileOpen: false,

                            init() {
                                try {
                                    this.collapsed = localStorage.getItem('moduleSidebarCollapsed') === '1';
                                } catch (e) {}

                                this.sync();
                            },

                            toggle() {
                                this.collapsed = ! this.collapsed;
                                this.save();
                                this.sync();
                            },

                            primaryToggle() {
                                if (window.matchMedia('(max-width: 767px)').matches) {
                                    this.mobileOpen = ! this.mobileOpen;
                                } else {
                                    this.toggle();
                                }
                            },

                            closeMobile() {
                                this.mobileOpen = false;
                            },

                            save() {
                                try {
                                    localStorage.setItem('moduleSidebarCollapsed', this.collapsed ? '1' : '0');
                                } catch (e) {}
                            },

                            sync() {
                                var el = document.documentElement;
                                el.classList.add('module-sidebar-ready');
                                el.classList.toggle('module-sidebar-collapsed', this.collapsed);
                            },
                        });
                    }

                    document.addEventListener('alpine:init', registerModuleSidebarStore);
                    registerModuleSidebarStore();
                })();
            </script>
        @endassets
@endif
</div>
