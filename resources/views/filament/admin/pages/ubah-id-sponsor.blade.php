<x-filament-panels::page>
    <div class="space-y-6">
        <!-- Header -->
        {{-- <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">Ubah ID Sponsor</h2>
                <p class="text-muted-foreground">
                    Pilih multiple users dan ubah ID sponsor mereka
                </p>
            </div>
        </div> --}}

        <!-- Form -->
        <form wire:submit="updateSponsor" class="space-y-6">
            {{ $this->form }}

            <!-- Action Buttons -->
            @if (!empty($selectedUserIds))
                <div class="flex items-center gap-4 pt-6 border-t">
                    <x-filament::button type="submit" size="lg" color="primary">
                        <x-heroicon-m-check class="w-5 h-5 mr-2" />
                        Update Sponsor
                    </x-filament::button>

                    <x-filament::button type="button" size="lg" color="gray" wire:click="clearSelection">
                        <x-heroicon-m-x-mark class="w-5 h-5 mr-2" />
                        Clear Selection
                    </x-filament::button>
                </div>
            @endif
        </form>

        <!-- Selected Users Info -->
        @if (!empty($selectedUserIds))
            <div class="bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-100 mb-2">
                    Selected Users ({{ count($selectedUserIds) }})
                </h3>
                <div class="space-y-2">
                    @foreach ($selectedUserIds as $userId)
                        @php
                            $user = \App\Models\User::find($userId);
                        @endphp
                        @if ($user)
                            <div
                                class="flex items-center justify-between bg-white dark:bg-gray-800 rounded-md p-3 border">
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                                        <span class="text-blue-600 dark:text-blue-400 font-medium text-sm">
                                            {{ strtoupper(substr($user->nama, 0, 1)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-gray-100">
                                            {{ $user->nama }}
                                        </p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $user->id_mitra }} • {{ $user->email }}
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        Current Sponsor:
                                    </p>
                                    <p class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                        @if ($user->sponsor)
                                            {{ $user->sponsor->nama }} ({{ $user->sponsor->id_mitra }})
                                        @else
                                            <span class="text-red-500">No Sponsor</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Instructions -->
        <div class="bg-gray-50 dark:bg-gray-900/50 border border-gray-200 dark:border-gray-800 rounded-lg p-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-2">
                Cara Penggunaan
            </h3>
            <div class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                <div class="flex items-start space-x-2">
                    <span
                        class="w-5 h-5 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-xs font-medium mt-0.5">1</span>
                    <p>Pilih multiple users menggunakan field "Pilih Users" di atas</p>
                </div>
                <div class="flex items-start space-x-2">
                    <span
                        class="w-5 h-5 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-xs font-medium mt-0.5">2</span>
                    <p>Setelah users dipilih, field "Pilih Mitra Sponsor" akan muncul</p>
                </div>
                <div class="flex items-start space-x-2">
                    <span
                        class="w-5 h-5 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-xs font-medium mt-0.5">3</span>
                    <p>Pilih satu mitra yang akan dijadikan sponsor untuk semua user yang dipilih</p>
                </div>
                <div class="flex items-start space-x-2">
                    <span
                        class="w-5 h-5 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-xs font-medium mt-0.5">4</span>
                    <p>Klik tombol "Update Sponsor" untuk mengubah id_sponsor semua user menjadi mitra yang dipilih</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
