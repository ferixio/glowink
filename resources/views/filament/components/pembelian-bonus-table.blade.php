<div class="space-y-3">
    @forelse($pembelianBonuses as $bonus)
        <div class="bg-white rounded-lg shadow p-4 flex items-start justify-between">
            <div>

                <div class="text-sm {{ $bonus->tipe == 'bonus' ? 'text-green-600' : 'text-red-500' }} font-semibold">
                    {{ $bonus->keterangan }}
                </div>
            </div>
            <div class="text-xs text-gray-500 whitespace-nowrap">
                {{ $bonus->created_at->format('d/m/Y H:i') }}
            </div>
        </div>
    @empty
        <div class="bg-gray-50 p-4 text-center text-sm text-gray-500 rounded">
            Tidak ada data bonus tersedia
        </div>
    @endforelse
</div>
