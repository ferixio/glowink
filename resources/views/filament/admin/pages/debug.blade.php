<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Tab Navigation -->
        {{-- <div class="border-b border-gray-200 dark:border-gray-700">
            <nav class="-mb-px flex space-x-8 gap-8" aria-label="Tabs">
                <button wire:click="$set('activeTab', 'bonus')"
                    class=" py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'bonus' ? 'text-lg ' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Debug Bonus Sponsor & Generasi
                </button>
                <button wire:click="$set('activeTab', 'deviden')"
                    class="py-2 px-1 border-b-2 font-medium text-sm {{ $activeTab === 'deviden' ? 'text-lg ' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    Debug Deviden Basic & RO Bulanan
                </button>

            </nav>
        </div> --}}

        {{-- @if ($activeTab === 'bonus')
            <!-- Tab 2: Debug Bonus Sponsor dan Generasi -->
            <div class="space-y-6">
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                        Debug untuk Bonus Sponsor dan Generasi
                    </h2>

                    <form wire:submit="save" class="space-y-4">
                        {{ $this->form }}

                        <div class="flex justify-end gap-4">
                            <x-filament::button type="submit" wire:click="makePurchaseForAktivasiMember"
                                color="success">
                                Lakukan Pembelian untuk Aktivasi Member
                            </x-filament::button>
                            <x-filament::button type="submit" wire:click="makePurchaseForRepeatOrder" color="warning">
                                Lakukan Pembelian untuk Repeat Order
                            </x-filament::button>
                        </div>
                    </form>
                </div>

                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                    <h3 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                        Penjelasan Proses Debug Bonus
                    </h3>

                    <div class="space-y-6">
                        <!-- Aktivasi Member Section -->
                        <div class="border-l-4 border-purple-500 pl-4">
                            <h4 class="text-lg font-semibold text-purple-600 mb-3">Aktivasi Member</h4>
                            <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p><strong>Kategori:</strong> aktivasi member</p>
                                <p><strong>Status:</strong> diterima</p>
                                <p><strong>Stockist:</strong> User ID 2</p>
                                <p><strong>Produk:</strong> ID 1 (Paket Aktivasi)</p>
                                <p><strong>Harga:</strong> Rp 500.000</p>

                                <div class="mt-3">
                                    <p class="font-medium text-gray-800 dark:text-gray-200">Bonus yang Akan Ditrigger:
                                    </p>
                                    <ul class="list-disc list-inside space-y-1 mt-2 ml-4">
                                        <li><strong>BonusSponsor:</strong> Bonus untuk sponsor user</li>
                                        <li><strong>BonusGenerasi:</strong> Bonus untuk generasi upline</li>
                                        <li>Update status member user</li>
                                        <li>Update level karir user</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Repeat Order Section -->
                        <div class="border-l-4 border-orange-500 pl-4">
                            <h4 class="text-lg font-semibold text-orange-600 mb-3">Repeat Order</h4>
                            <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <p><strong>Kategori:</strong> repeat order</p>
                                <p><strong>Status:</strong> diterima</p>
                                <p><strong>Stockist:</strong> User ID 2</p>
                                <p><strong>Produk:</strong> ID 1 (Produk Regular)</p>
                                <p><strong>Harga:</strong> Rp 100.000</p>

                                <div class="mt-3">
                                    <p class="font-medium text-gray-800 dark:text-gray-200">Bonus yang Akan Ditrigger:
                                    </p>
                                    <ul class="list-disc list-inside space-y-1 mt-2 ml-4">
                                        <li><strong>BonusReward:</strong> Bonus reward untuk repeat order</li>
                                        <li>Update poin reward user</li>
                                        <li>Update stok produk</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <!-- Bonus Events Info -->
                        <div class="border-l-4 border-red-500 pl-4">
                            <h4 class="text-lg font-semibold text-red-600 mb-3">Event Listener yang Terlibat</h4>
                            <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                                <ul class="list-disc list-inside space-y-1 ml-4">
                                    <li><strong>BonusSponsorListener:</strong> Menangani bonus sponsor</li>
                                    <li><strong>BonusGenerasiListener:</strong> Menangani bonus generasi</li>
                                    <li><strong>BonusRewardListener:</strong> Menangani bonus reward</li>
                                    <li><strong>ChangeLevelUserListener:</strong> Update level karir user</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif --}}

        <div class="space-y-6">
            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                    Debug untuk Deviden Basic dan RO Bulanan
                </h2>

                <form wire:submit="save" class="space-y-4">
                    {{ $this->form }}

                    <div class="flex justify-end gap-4">
                        {{-- <x-filament::button type="submit" wire:click="makePurchaseForROBasic" color="primary">
                            Lakukan Pembelian untuk Kategori RO Basic (hari ini)
                        </x-filament::button> --}}
                        <x-filament::button type="submit" wire:click="makePurchaseForROBulanan" color="primary">
                            Lakukan Pembelian untuk Kategori RO Bulanan
                        </x-filament::button>
                    </div>
                </form>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
                <h3 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                    Penjelasan Proses Debug Pembelian
                </h3>

                <div class="space-y-6">
                    <!-- RO Basic Section -->
                    <div class="border-l-4 border-blue-500 pl-4">
                        <h4 class="text-lg font-semibold text-blue-600 mb-3">RO Basic (Stock Pribadi)</h4>
                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p><strong>Kategori:</strong> repeat order</p>
                            <p><strong>Status:</strong> diterima</p>
                            <p><strong>Stockist:</strong> User ID 2</p>
                            <p><strong>Produk:</strong> ID 1 (RO Basic)</p>
                            <p><strong>Harga:</strong> Rp 100.000</p>

                            <div class="mt-3">
                                <p class="font-medium text-gray-800 dark:text-gray-200">Proses yang Akan Berjalan:
                                </p>
                                <ul class="list-disc list-inside space-y-1 mt-2 ml-4">
                                    <li>Membuat record pembelian dengan status 'diterima'</li>
                                    <li>Membuat detail pembelian dengan bonus sponsor & generasi</li>
                                    <li>Menambah stok produk ke user pembeli</li>
                                    <li>Mengurangi stok dari stockist (ID 2)</li>
                                    <li>Menambah saldo penghasilan stockist</li>
                                    <li>Membuat record penghasilan untuk stockist</li>
                                    <li>Trigger event BonusReward (karena kategori 'repeat order')</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- RO Bulanan Section -->
                    <div class="border-l-4 border-green-500 pl-4">
                        <h4 class="text-lg font-semibold text-green-600 mb-3">RO Bulanan (Repeat Order Bulanan)</h4>
                        <div class="space-y-2 text-sm text-gray-700 dark:text-gray-300">
                            <p><strong>Kategori:</strong> repeat order bulanan</p>
                            <p><strong>Status:</strong> diterima</p>
                            <p><strong>Stockist:</strong> User ID 2</p>
                            <p><strong>Produk:</strong> ID 2 (RO Bulanan)</p>
                            <p><strong>Harga:</strong> Rp 100.000</p>

                            <div class="mt-3">
                                <p class="font-medium text-gray-800 dark:text-gray-200">Proses yang Akan Berjalan:
                                </p>
                                <ul class="list-disc list-inside space-y-1 mt-2 ml-4">
                                    <li>Membuat record pembelian dengan status 'diterima'</li>
                                    <li>Membuat detail pembelian dengan bonus sponsor & generasi</li>
                                    <li>Menambah stok produk ke user pembeli</li>
                                    <li>Mengurangi stok dari stockist (ID 2)</li>
                                    <li>Menambah saldo penghasilan stockist</li>
                                    <li>Membuat record penghasilan untuk stockist</li>
                                    <li>Update jml_ro_bulanan user (+1)</li>
                                    <li>Trigger event BonusReward (karena kategori 'repeat order bulanan')</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Tab Content -->
        @if ($activeTab === 'deviden')
            <!-- Tab 1: Debug Deviden Basic dan RO Bulanan -->
        @endif


    </div>
</x-filament-panels::page>
