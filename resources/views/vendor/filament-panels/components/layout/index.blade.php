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
    <div class="flex flex-row-reverse w-full min-h-screen fi-layout overflow-x-clip">
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
                <div class="fixed z-50 p-4 top-3 left-4 lg:hidden">
                    <a href="/user" class="flex items-center">
                        {{-- <img src="{{ asset('images/logo.svg') }}" alt="Logo" class="w-auto h-8"> --}}
                        <h1 style="font-size:18px"><b>Glowink System</b></h1>
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
                class="fixed inset-0 z-30 transition duration-500 fi-sidebar-close-overlay dark:bg-gray-950/75 lg:hidden">
            </div>

            {{-- Mobile Sidebar (hidden by default, shown when Menu clicked) --}}
            <x-filament-panels::sidebar :navigation="$navigation" class="fi-main-sidebar lg:hidden"
                x-show="$store.sidebar.isOpen" style="z-index: 60;" />

            {{-- Desktop Sidebar --}}
            <x-filament-panels::sidebar :navigation="$navigation" class="hidden fi-main-sidebar lg:block" />

            {{-- Mobile Bottom Navigation --}}
            <div x-data="mobileNav()"
                style="position: fixed; bottom: 0; left: 0; right: 0; z-index: 40; backdrop-filter: blur(8px);"
                class="lg:hidden dark:bg-gray-900/95 dark:border-gray-700">

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
                                class="p-1 text-gray-500 transition rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white">
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
                                    class="flex items-center p-3 text-gray-700 transition rounded-lg dark:text-gray-300 hover:bg-gray-50 hover:text-gray-900 dark:hover:bg-gray-800 dark:hover:text-white">
                                    <span x-text="item.label" class="font-medium"></span>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                {{-- Navigation Items --}}
                <div
                    style="border-radius:50px  50px  0 0;display: flex; align-items: center; justify-content: space-around; padding: 0.5rem 0.5rem 0.5rem;    background: linear-gradient(to bottom , #2ec4e2 , #034fb3) !important;;box-shadow: 0 -2px 4px -1px rgba(0, 0, 0, 0.1);">

                    {{-- Dashboard --}}
                    <a href="/user/dashboard"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/dashboard') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; text-decoration: none;"
                        class=" fi-nav-item hover:text-gray-900 dark:text-white-400 dark:hover:text-white dark:hover:bg-gray-800">
                        <svg style="height: 1.25rem; width: 1.25rem; margin-bottom: 0.25rem;" fill="none"
                            stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z"></path>
                        </svg>
                        <span style="font-size: 0.75rem; font-weight: 500;" class="text-white">Dashboard</span>
                    </a>

                    {{-- Transaksi --}}
                    <button
                        @click="openSubmenu('Transaksi', [
                            {label: 'Belanja', url: '/user/pembelian-produk'},
                            @if (auth()->user()->poin_reward >= 20) {label: 'RO Bulanan', url: '/user/pembelian-r-o-bulanan'}, @endif
                            {label: 'Aktivasi PIN', url: '/user/aktivasi-pins'}
                        ])"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/pembelian-produk') || request()->is('user/pembelian-r-o-bulanan') || request()->is('user/aktivasi-pins') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; border: none; background: none; cursor: pointer;"
                        class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                        <svg style="height: 1.25rem; width: 1.25rem; margin-bottom: 0.25rem;" fill="none"
                            stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                            </path>
                        </svg>
                        <span style="font-size: 0.75rem; font-weight: 500;"  class="text-white">Transaksi</span>
                    </button>



                    {{-- Laporan --}}
                    <button
                        @click="openSubmenu('Laporan', [{label: 'Aktivitas', url: '/user/aktivitas'},{label: 'Pembelian', url: '/user/pembelians'}, {label: 'Penghasilan', url: '/user/penghasilans'}, {label: 'Jaringan', url: '/user/jaringan'}])"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/pembelians*') || request()->is('user/penghasilans*') || request()->is('user/jaringan*') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; border: none; background: none; cursor: pointer;"
                        class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                        <svg style="height: 1.25rem; width: 1.25rem; margin-bottom: 0.25rem;" fill="none"
                            stroke="white" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z">
                            </path>
                        </svg>
                        <span style="font-size: 0.75rem; font-weight: 500;"  class="text-white">Laporan</span>
                    </button>

                    {{-- Stokist --}}
                    @if (auth()->user()->isStockis ?? false)
                        {{-- <a href="/user/approve-pembelians"
                            style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/approve-pembelians*') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; text-decoration: none;"
                            class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                            <svg style="height: 1.25rem; width: 1.25rem; margin-bottom: 0.25rem;" fill="none"
                                stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span style="font-size: 0.75rem; font-weight: 500;">Stokist</span>
                        </a> --}}

                        <button
                            @click="openSubmenu('Stockis', [{label: 'Terima Pembelian', url: '/user/approve-pembelians'}, {label: 'Belanja Stok', url: '/user/pembelian-produk-stokis'}])"
                            style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: {{ request()->is('user/approve-pembelians*') || request()->is('user/pembelian-produk-stokis*') ? 'rgb(37 99 235)' : 'rgb(75 85 99)' }}; border: none; background: none; cursor: pointer;"
                            class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                            <svg style="height: 1.25rem; width: 1.25rem; margin-bottom: 0.25rem;" fill="none"
                                stroke="white" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                            <span style="font-size: 0.75rem; font-weight: 500;"  class="text-white">Stockis</span>
                        </button>
                    @endif

                    {{-- Menu (Sidebar Toggle) --}}
                    <button @click="toggleSidebar()"
                        style="display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 0.5rem; border-radius: 0.75rem; transition: all 0.2s; min-width: 60px; color: rgb(75 85 99); border: none; background: none; cursor: pointer;"
                        class="fi-nav-item hover:text-gray-900 hover:bg-gray-50 dark:text-gray-400 dark:hover:text-white dark:hover:bg-gray-800">
                        <svg xmlns="http://www.w3.org/2000/svg"
                            style="height: 1.28rem; width: 1.28rem; margin-bottom: 0.25rem;" fill="none"
                            viewBox="0 0 24 24" stroke-width="1.5" stroke="white" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <span style="font-size: 0.75rem; font-weight: 500;"  class="text-white">Menu</span>
                    </button>
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
                        },

                        toggleSidebar() {
                            if (this.$store.sidebar.isOpen) {
                                this.$store.sidebar.close();
                            } else {
                                this.$store.sidebar.open();
                            }
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
