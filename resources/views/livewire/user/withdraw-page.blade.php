<div class="space-y-6">
    <!-- Saldo Info -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Rincian Pendapatan Anda</h3>
        <div class="space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Bonus Sponsor:</span>
                <span class="text-sm font-medium text-gray-900">Rp
                    {{ number_format($penghasilanList->where('kategori_bonus', 'Bonus Sponsor')->sum('nominal_bonus'), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Dividen:</span>
                <span class="text-sm font-medium text-gray-900">Rp
                    {{ number_format($penghasilanList->whereIn('kategori_bonus', ['deviden harian', 'deviden bulanan'])->sum('nominal_bonus'), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Bonus Generasi:</span>
                <span class="text-sm font-medium text-gray-900">Rp
                    {{ number_format($penghasilanList->where('kategori_bonus', 'Bonus Generasi')->sum('nominal_bonus'), 0, ',', '.') }}</span>
            </div>
            {{-- <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Reward Karir:</span>
                <span class="text-sm font-medium text-gray-900">Rp
                    {{ number_format($penghasilanList->where('kategori_bonus', 'Bonus Reward')->sum('nominal_bonus'), 0, ',', '.') }}</span>
            </div> --}}
            {{-- <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Royalti Jenjang Karir:</span>
                <span class="text-sm font-medium text-gray-900">Rp
                    {{ number_format($penghasilanList->where('kategori_bonus', 'Pemasukan')->sum('nominal_bonus'), 0, ',', '.') }}</span>
            </div> --}}
            <div class="flex justify-between items-center py-3 border-t-2 border-gray-200">
                <span class="text-base font-semibold text-gray-800">Total Pendapatan:</span>
                <span class="text-base font-bold text-gray-900">Rp
                    {{ number_format($penghasilanList->sum('nominal_bonus'), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                <span class="text-sm text-gray-600">Total Penarikan:</span>
                <span class="text-sm font-medium text-gray-900">Rp
                    {{ number_format($withdrawHistory->sum('nominal'), 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-t-2 border-blue-200 bg-blue-50 px-3 rounded">
                <span class="text-base font-semibold text-blue-800">Penghasilan Belum Ditarik:</span>
                <span class="text-base font-bold text-blue-900">Rp
                    {{ number_format($user->saldo_penghasilan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Withdraw Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Buat Permintaan Withdraw</h3>

        <form wire:submit.prevent="createWithdraw" class="space-y-4">
            <div>
                <label for="nominal_withdraw" class="block text-sm font-medium text-gray-700 mb-2">
                    Nominal Withdraw
                </label>
                <input type="text" id="nominal_withdraw" wire:model.lazy="nominal_withdraw"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Minimal Rp 60.000" min="60000" max="{{ $user->saldo_penghasilan }}"
                    oninput="formatRupiah(this); updateWithdrawInfo();">
                <script>
                    function formatRupiah(input) {
                        let value = input.value.replace(/[^\d]/g, '');
                        if (!value) {
                            input.value = '';
                            return;
                        }
                        let formatted = new Intl.NumberFormat('id-ID').format(value);
                        input.value = 'Rp ' + formatted;
                    }

                    function updateWithdrawInfo() {
                        let input = document.getElementById('nominal_withdraw');
                        let value = input.value.replace(/[^\d]/g, '');
                        let nominal = parseInt(value) || 0;
                        let admFee = nominal * 0.1;
                        let total = nominal * 0.9;
                        document.getElementById('adm_fee').textContent = nominal >= 60000 ? 'Rp ' + new Intl.NumberFormat('id-ID')
                            .format(admFee) : '-';
                        document.getElementById('total_withdraw').textContent = nominal >= 60000 ? 'Rp ' + new Intl.NumberFormat(
                            'id-ID').format(total) : '-';
                    }
                    document.addEventListener('DOMContentLoaded', function() {
                        updateWithdrawInfo();
                    });
                </script>
                <div class="mt-2 text-sm text-gray-600">
                    Potongan admin 10%: <span id="adm_fee">-</span><br>
                    Total diterima: <span id="total_withdraw">-</span>
                </div>
                @error('nominal_withdraw')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Bank Info Display -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="text-sm font-medium text-gray-700 mb-2">Informasi Rekening</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500">Bank:</span>
                        <span class="font-medium ml-2">{{ $user->bank ?: 'Belum diisi' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">No. Rekening:</span>
                        <span class="font-medium ml-2">{{ $user->no_rek ?: 'Belum diisi' }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500">Nama Rekening:</span>
                        <span class="font-medium ml-2">{{ $user->nama_rekening ?: 'Belum diisi' }}</span>
                    </div>
                </div>
            </div>

            <button type="submit"
                class="w-full bg-gray-500 py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                Buat Permintaan Withdraw
            </button>
        </form>
    </div>

    <!-- Penghasilan List -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Penghasilan</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Keterangan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nominal</th>
                   
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($penghasilanList as $penghasilan)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($penghasilan->tgl_dapat_bonus)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $penghasilan->keterangan }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if ($penghasilan->kategori_bonus == 'Bonus Sponsor') bg-blue-100 text-blue-800
                                    @elseif($penghasilan->kategori_bonus == 'Bonus Generasi') bg-green-100 text-green-800
                                    @elseif($penghasilan->kategori_bonus == 'Bonus Reward') bg-purple-100 text-purple-800
                                    @elseif($penghasilan->kategori_bonus == 'Pemasukan') bg-yellow-100 text-yellow-800
                                    @elseif(in_array($penghasilan->kategori_bonus, ['deviden harian', 'deviden bulanan'])) bg-indigo-100 text-indigo-800
                                    @else bg-gray-100 text-gray-800 @endif">
                                    {{ $penghasilan->kategori_bonus }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-green-600">
                                +Rp {{ number_format($penghasilan->nominal_bonus, 0, ',', '.') }}
                            </td>
               
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">
                                Belum ada data penghasilan
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Withdraw History -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Riwayat Withdraw</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nominal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($withdrawHistory as $withdraw)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($withdraw->tgl_withdraw)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-red-600">
                                -Rp {{ number_format($withdraw->nominal, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                    @if ($withdraw->status == 'approved') bg-green-100 text-green-800
                                    @elseif($withdraw->status == 'rejected') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    {{ ucfirst($withdraw->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">
                                Belum ada riwayat withdraw
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
