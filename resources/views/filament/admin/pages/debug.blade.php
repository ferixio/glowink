<x-filament-panels::page>
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-white mb-4">
                Debug Tools
            </h2>

            <form wire:submit="save" class="space-y-4">
                {{ $this->form }}

                <div class="flex justify-end gap-4">
                    <x-filament::button type="submit" color="primary">
                        Lakukan Pembelian untuk Kategori RO Basic (hari ini)
                    </x-filament::button>
                    <x-filament::button type="submit" wire:click="makePurchaseForROBasic" color="primary">
                        Lakukan Pembelian untuk Kategori RO Bulanan
                    </x-filament::button>
                </div>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
            <h3 class="text-md font-medium text-gray-900 dark:text-white mb-4">
                Seeding Actions
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        Seed RO Basic untuk user yang dipilih
                    </p>
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                        Seed RO Bulanan untuk user yang dipilih
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
