@php
    use Filament\Support\Enums\MaxWidth;

    $navigation = filament()->getNavigation();
@endphp

<x-filament-panels::layout.base :livewire="$livewire">
    <style>
        nav.fi-topbar {
            background-color: lightblue;
        }

        @media (max-width: 1023px) {
            .fi-main-ctn {
                padding-bottom: 6rem !important;
            }

            .fi-main {
                margin-bottom: 6rem !important;
            }

            /* Hide hamburger icon on mobile */
            .fi-icon-btn-icon.h-6.w-6 {
                display: none !important;
            }


        }
    </style>
    <div class="fi-layout flex min-h-screen w-full flex-row-reverse overflow-x-clip">
        <div @if (filament()->isSidebarCollapsibleOnDesktop()) x-data="{}"
                x-bind:class="{
                    'fi-main-ctn-sidebar-open': $store.sidebar.isOpen,
                }"
                x-bind:style="'display: flex; opacity:1;'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}}
            @elseif (
                !(filament()->isSidebarCollapsibleOnDesktop() ||
                    filament()->isSidebarFullyCollapsibleOnDesktop() ||
                    filament()->hasTopNavigation() ||
                    !filament()->hasNavigation()
                ))
                x-data="{}"
                x-bind:style="'display: flex; opacity:1;'" {{-- Mimics `x-cloak`, as using `x-cloak` causes visual issues with chart widgets --}} @endif
            @class([
                'fi-main-ctn w-screen flex-1 flex-col',
                'h-full opacity-0 transition-all' =>
                    filament()->isSidebarCollapsibleOnDesktop() ||
                    filament()->isSidebarFullyCollapsibleOnDesktop(),
                'opacity-0' => !(
                    filament()->isSidebarCollapsibleOnDesktop() ||
                    filament()->isSidebarFullyCollapsibleOnDesktop() ||
                    filament()->hasTopNavigation() ||
                    !filament()->hasNavigation()
                ),
                'flex' => filament()->hasTopNavigation() || !filament()->hasNavigation(),
                'pb-72 lg:pb-0' => filament()->hasNavigation(), // Add bottom padding for mobile bottom navbar
            ])>
            @if (filament()->hasTopbar())
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_BEFORE, scopes: $livewire->getRenderHookScopes()) }}

                {{-- Mobile Logo Overlay (only shows on mobile) --}}
                <div class="lg:hidden fixed top-0 left-0 z-50 p-4">
                    <a href="/user" class="flex items-center">
                        <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="h-8 w-auto">
                    </a>
                </div>

                {{-- Original Topbar (unchanged for desktop) --}}
                <x-filament-panels::topbar :navigation="$navigation" />

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::TOPBAR_AFTER, scopes: $livewire->getRenderHookScopes()) }}
            @endif

            <main @class([
                'fi-main mx-auto h-full w-full px-4 md:px-6 lg:px-8',
                match (
                    ($maxContentWidth ??=
                        filament()->getMaxContentWidth() ?? MaxWidth::SevenExtraLarge)
                ) {
                    MaxWidth::ExtraSmall, 'xs' => 'max-w-xs',
                    MaxWidth::Small, 'sm' => 'max-w-sm',
                    MaxWidth::Medium, 'md' => 'max-w-md',
                    MaxWidth::Large, 'lg' => 'max-w-lg',
                    MaxWidth::ExtraLarge, 'xl' => 'max-w-xl',
                    MaxWidth::TwoExtraLarge, '2xl' => 'max-w-2xl',
                    MaxWidth::ThreeExtraLarge, '3xl' => 'max-w-3xl',
                    MaxWidth::FourExtraLarge, '4xl' => 'max-w-4xl',
                    MaxWidth::FiveExtraLarge, '5xl' => 'max-w-5xl',
                    MaxWidth::SixExtraLarge, '6xl' => 'max-w-6xl',
                    MaxWidth::SevenExtraLarge, '7xl' => 'max-w-7xl',
                    MaxWidth::Full, 'full' => 'max-w-full',
                    MaxWidth::MinContent, 'min' => 'max-w-min',
                    MaxWidth::MaxContent, 'max' => 'max-w-max',
                    MaxWidth::FitContent, 'fit' => 'max-w-fit',
                    MaxWidth::Prose, 'prose' => 'max-w-prose',
                    MaxWidth::ScreenSmall, 'screen-sm' => 'max-w-screen-sm',
                    MaxWidth::ScreenMedium, 'screen-md' => 'max-w-screen-md',
                    MaxWidth::ScreenLarge, 'screen-lg' => 'max-w-screen-lg',
                    MaxWidth::ScreenExtraLarge, 'screen-xl' => 'max-w-screen-xl',
                    MaxWidth::ScreenTwoExtraLarge, 'screen-2xl' => 'max-w-screen-2xl',
                    default => $maxContentWidth,
                },
            ])>
                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_START, scopes: $livewire->getRenderHookScopes()) }}

                {{ $slot }}

                {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::CONTENT_END, scopes: $livewire->getRenderHookScopes()) }}
            </main>

            {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::FOOTER, scopes: $livewire->getRenderHookScopes()) }}
        </div>

        @if (filament()->hasNavigation())
            {{-- Desktop Sidebar --}}
            <div x-cloak x-data="{}" x-on:click="$store.sidebar.close()" x-show="$store.sidebar.isOpen"
                x-transition.opacity.300ms
                class="fi-sidebar-close-overlay fixed inset-0 z-30  transition duration-500 dark:bg-gray-950/75 lg:hidden">
            </div>

            <x-filament-panels::sidebar :navigation="$navigation" class="fi-main-sidebar hidden lg:block" />

            {{-- Mobile Bottom Navigation --}}
            <div x-data="mobileNav()"
                style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 40; background: rgba(255, 255, 255, 0.95); backdrop-filter: blur(8px); border-top: 1px solid rgb(229 231 235); box-shadow: 0 -2px 4px -1px rgba(0, 0, 0, 0.1);"
                class="lg:hidden  dark:bg-gray-900/95 dark:border-gray-700">

                {{-- Submenu Overlay --}}
                <div x-show="activeSubmenu" x-transition.opacity.300ms style="position: fixed; inset: 0;  z-index: 50;"
                    @click="closeSubmenu()"></div>

                {{-- Submenu Panel --}}
                <div x-show="activeSubmenu" x-transition:enter="transform transition ease-out duration-300"
                    x-transition:enter-start="translate-y-4 opacity-0"
                    x-transition:enter-end="translate-y-0 opacity-100"
                    x-transition:leave="transform transition ease-in duration-200"
                    x-transition:leave-start="translate-y-0 opacity-100"
                    x-transition:leave-end="translate-y-4 opacity-0"
                    style="position: fixed; bottom: 4rem; left: 50%; transform: translateX(-50%); z-index: 50; background: white; border: 1px solid rgb(229 231 235); box-shadow: 0 -4px 6px -1px rgba(0, 0, 0, 0.1); border-radius: 12px; max-width: 320px; width: calc(100% - 2rem);"
                    class="dark:bg-gray-900 dark:border-gray-700">
                    <div class="p-4">
                        {{-- Title + Close --}}
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white" x-text="submenuTitle"></h3>
                            <button @click="closeSubmenu()"
                                class="p-1 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        {{-- Items --}}
                        <div class="space-y-2">
                            <template x-for="item in submenuItems" :key="item.url">
                                <a :href="item.url"
                                    class="flex items-center p-3 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white transition">
                                    <span x-text="item.label" class="font-medium"></span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Navigation Items --}}
                <div
                    style="display: flex; align-items: center; justify-content: space-around; padding: 0.5rem 0.5rem 0.75rem;">

                    {{-- Dashboard --}}
                    <a href="/user/dashboard"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/dashboard') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; text-decoration: none;"
                        class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                        </svg>

                        <span style="font-size: 0.75rem; font-weight: 500;">Dashboard</span>
                    </a>

                    {{-- Transaksi --}}
                    <button
                        @click="openSubmenu('Transaksi', [{label: 'Belanja', url: '/user/pembelian-produk'}, {label: 'RO Bulanan', url: '/user/pembelian-r-o-bulanan'}, {label: 'Aktivasi PIN', url: '/user/aktivasi-pins'}])"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/pembelian-produk') || request()->is('user/pembelian-r-o-bulanan') || request()->is('user/aktivasi-pins') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; border: none; background: none; cursor: pointer;"
                        class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007ZM8.625 10.5a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm7.5 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                        </svg>

                        <span style="font-size: 0.75rem; font-weight: 500;">Transaksi</span>
                    </button>

                    {{-- Aktivitas --}}
                    <a href="/user/aktivitas"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/aktivitas*') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; text-decoration: none;"
                        class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                        </svg>

                        <span style="font-size: 0.75rem; font-weight: 500;">Aktivitas</span>
                    </a>

                    {{-- Laporan --}}
                    <button
                        @click="openSubmenu('Laporan', [{label: 'Pembelian', url: '/user/pembelians'}, {label: 'Penghasilan', url: '/user/penghasilans'}, {label: 'Jaringan', url: '/user/jaringan'}])"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/pembelians*') || request()->is('user/penghasilans*') || request()->is('user/jaringan*') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; border: none; background: none; cursor: pointer;"
                        class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15.666 3.888A2.25 2.25 0 0 0 13.5 2.25h-3c-1.03 0-1.9.693-2.166 1.638m7.332 0c.055.194.084.4.084.612v0a.75.75 0 0 1-.75.75H9a.75.75 0 0 1-.75-.75v0c0-.212.03-.418.084-.612m7.332 0c.646.049 1.288.11 1.927.184 1.1.128 1.907 1.077 1.907 2.185V19.5a2.25 2.25 0 0 1-2.25 2.25H6.75A2.25 2.25 0 0 1 4.5 19.5V6.257c0-1.108.806-2.057 1.907-2.185a48.208 48.208 0 0 1 1.927-.184" />
                        </svg>

                        <span style="font-size: 0.75rem; font-weight: 500;">Laporan</span>
                    </button>

                    {{-- Stokist --}}
                    @if (auth()->user()->isStockis ?? false)
                        <a href="/user/approve-pembelians"
                            style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/approve-pembelians*') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; text-decoration: none;"
                            class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                            <svg style="height: 1.25rem; width: 1.25rem; margin-bottom: 0.25rem;" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span style="font-size: 0.75rem; font-weight: 500;">Stokist</span>
                        </a>
                    @endif
                </div>
            </div>

            <script>
                function mobileNav() {
                    return {
                        activeSubmenu: false,
                        submenuTitle: '',
                        submenuItems: [],

                        openSubmenu(title, items) {
                            this.submenuTitle = title;
                            this.submenuItems = items;
                            this.activeSubmenu = true;
                            document.body.classList.add('submenu-open');
                        },

                        closeSubmenu() {
                            this.activeSubmenu = false;
                            document.body.classList.remove('submenu-open');
                        }
                    }
                }

                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => {
                        let activeSidebarItem = document.querySelector(
                            '.fi-main-sidebar .fi-sidebar-item.fi-active',
                        )

                        if (
                            !activeSidebarItem ||
                            activeSidebarItem.offsetParent === null
                        ) {
                            activeSidebarItem = document.querySelector(
                                '.fi-main-sidebar .fi-sidebar-group.fi-active',
                            )
                        }

                        if (
                            !activeSidebarItem ||
                            activeSidebarItem.offsetParent === null
                        ) {
                            return
                        }

                        const sidebarWrapper = document.querySelector(
                            '.fi-main-sidebar .fi-sidebar-nav',
                        )

                        if (!sidebarWrapper) {
                            return
                        }

                        sidebarWrapper.scrollTo(
                            0,
                            activeSidebarItem.offsetTop -
                            window.innerHeight / 2,
                        )
                    }, 10)
                })
            </script>
        @endif
    </div>
</x-filament-panels::layout.base>
