<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}
    </x-filament-panels::form>
    @if ($devidenHarian || $searchResults)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <a href="{{ route('filament.admin.pages.list-detail-deviden-harian', ['selectedDate' => $data['selectedDate'] ?? now()->format('Y-m-d')]) }}"
                class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-currency-dollar class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Omzet Aktivasi</div>
                    <div class="text-2xl font-bold">
                        <div class="text-blue-600 hover:underline">
                            Rp
                            {{ number_format($devidenHarian ? $devidenHarian->omzet_aktivasi : $searchResults->omzet_aktivasi, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </a>
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-green-100 text-green-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-currency-dollar class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Omzet RO Basic</div>
                    <div class="text-2xl font-bold">Rp
                        {{ number_format($devidenHarian ? $devidenHarian->omzet_ro_basic : $searchResults->omzet_ro_basic, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <a href="{{ route('filament.admin.pages.list-mitra-deviden-harian', ['selectedDate' => $data['selectedDate'] ?? now()->format('Y-m-d')]) }}"
                class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-users class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Total Member</div>
                    <div class="text-2xl font-bold">
                        <div class="text-yellow-600 hover:underline">
                            {{ number_format($devidenHarian ? $devidenHarian->total_member : $searchResults->total_member, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </a>
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-purple-100 text-purple-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-gift class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Deviden Diterima</div>
                    <div class="text-2xl font-bold">Rp
                        {{ number_format($devidenHarian ? $devidenHarian->deviden_diterima : $searchResults->deviden_diterima, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 text-right text-gray-400 text-sm">
            @if ($devidenHarian)
                Data per {{ \Carbon\Carbon::parse($devidenHarian->created_at)->translatedFormat('d F Y H:i') }}
                <div class="mt-2 text-green-600 font-medium">✓ Data tersimpan di database</div>
            @elseif($searchResults)
                Data per {{ \Carbon\Carbon::parse($searchResults->created_at)->translatedFormat('d F Y H:i') }}
                <div class="mt-2 text-orange-600 font-medium">⚠ Data hasil pencarian (belum tersimpan)</div>
            @endif
        </div>
    @else
        <div class="p-8 text-center text-gray-400">
            {{-- Data deviden harian belum tersedia pada tanggal : {{ $this->data['selectedDate'] }}. --}}
            <div class="mt-2 text-sm">Klik tombol "Cari Data" untuk melihat perhitungan data.</div>
        </div>
    @endif
</x-filament-panels::page>
