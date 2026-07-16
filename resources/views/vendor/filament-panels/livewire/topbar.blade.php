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
                    x-data="{}"
                    x-on:click="$store.moduleSidebar.primaryToggle()"
                    :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                    class="fi-topbar-module-sidebar-toggle"
                />
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
                @foreach ($navigation as $group)
                    @php
                        $groupLabel = $group->getLabel();
                        $groupIcon = $group->getIcon();
                        $itemUrl = $group->getItems()->first()?->getUrl();
                    @endphp

                    @if (! $groupLabel || ! $itemUrl || ! $groupIcon)
                        @continue
                    @endif

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
                    --module-sidebar-width: 280px;
                    --module-sidebar-width-collapsed: 72px;
                    --module-topbar-height: 4rem;
                }

                /* Topbar stays full-width and sits above the sidebar; only the content column is pushed over (tablet & up) */
                @media (min-width: 768px) {
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

                .module-sidebar-icon {
                    width: 1.5rem;
                    height: 1.5rem;
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

                /* Collapsed: icons only */
                .module-sidebar.is-collapsed .module-sidebar-label {
                    display: none;
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

                    .module-sidebar.is-collapsed .module-sidebar-label {
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
