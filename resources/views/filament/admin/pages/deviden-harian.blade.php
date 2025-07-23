<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}
    </x-filament-panels::form>
    @if ($devidenHarian)
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-8">
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-blue-100 text-blue-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-currency-dollar class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Omzet Aktivasi</div>
                    <div class="text-2xl font-bold">Rp {{ number_format($devidenHarian->omzet_aktivasi, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-green-100 text-green-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-currency-dollar class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Omzet RO Basic</div>
                    <div class="text-2xl font-bold">Rp {{ number_format($devidenHarian->omzet_ro_basic, 0, ',', '.') }}
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-yellow-100 text-yellow-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-users class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Total Member</div>
                    <div class="text-2xl font-bold">{{ number_format($devidenHarian->total_member, 0, ',', '.') }}</div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow p-6 flex items-center">
                <div class="bg-purple-100 text-purple-600 rounded-full p-3 mr-4">
                    <x-heroicon-o-gift class="w-8 h-8" />
                </div>
                <div>
                    <div class="text-gray-500 text-sm">Deviden Diterima</div>
                    <div class="text-2xl font-bold">Rp
                        {{ number_format($devidenHarian->deviden_diterima, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>
        <div class="mt-8 text-right text-gray-400 text-sm">
            Data per {{ \Carbon\Carbon::parse($devidenHarian->created_at)->translatedFormat('d F Y H:i') }}
        </div>
    @else
        <div class="p-8 text-center text-gray-400">
            Data deviden harian belum tersedia {{ $this->data['selectedDate'] }}.
        </div>
    @endif
</x-filament-panels::page>
