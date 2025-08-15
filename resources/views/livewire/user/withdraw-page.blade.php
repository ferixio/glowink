<div class="space-y-6">


    <!-- Withdraw Form -->
    @if ($showWithdrawForm)
        <section id="withdraw-form" class="bg-white rounded-lg shadow p-6">
            <div class="flex justify-between">

                <h3 class="text-lg font-semibold text-gray-900 mb-4">Buat Permintaan Withdraw</h3>
                <button type="button" wire:click="toggleWithdrawForm"
                    style="width: 80px; height: 30px; background-color: #6B7280; color: #ffffff; padding: 4px 2px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    Kembali
                </button>
            </div>

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
                        Total diterima: <span id="total_withdraw">-</span><br><br>
                        Tambahkan Biaya Administrasi Rp 2.500 jika Rekening Tujuan selain BRI
                    </div>
                    @error('nominal_withdraw')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Info Display -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="text-sm font-medium text-gray-700 mb-2">Tujuan Transfer</h4>

                    @if ($user && $user->bank && $user->no_rek && $user->nama_rekening)
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-500">Bank:</span>
                                <span class="font-medium ml-2">{{ $user->bank }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">No. Rekening:</span>
                                <span class="font-medium ml-2">{{ $user->no_rek }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Nama Rekening:</span>
                                <span class="font-medium ml-2">{{ $user->nama_rekening }}</span>
                            </div>
                        </div>
                    @else
                        <div class="bg-yellow-50 border border-yellow-200 rounded-md p-3">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-medium text-yellow-800">
                                        Informasi Bank Anda Belum Lengkap
                                    </h3>
                                    <div class="mt-2 text-sm text-yellow-700">
                                        <p>Silakan lengkapi data rekening bank Anda pada halaman profil sebelum membuat
                                            permintaan withdraw.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <button type="submit" @if (!($user && $user->bank && $user->no_rek && $user->nama_rekening)) disabled @endif
                    style="width: 100%; background-color: {{ $user && $user->bank && $user->no_rek && $user->nama_rekening ? '#6B7280' : '#D1D5DB' }}; color: #ffffff; padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: {{ $user && $user->bank && $user->no_rek && $user->nama_rekening ? 'pointer' : 'not-allowed' }};"
                    @if (!($user && $user->bank && $user->no_rek && $user->nama_rekening)) title="Tidak dapat membuat withdraw karena data bank Anda belum lengkap" @endif>
                    {{ $user && $user->bank && $user->no_rek && $user->nama_rekening ? 'Buat Permintaan Withdraw' : 'Lengkapi Data Bank Anda' }}
                </button>
            </form>
        </section>
    @else
        <!-- Saldo Info -->
        <section id="saldo-info" class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Rincian Pendapatan Anda</h3>
            <div class="space-y-3">
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Bonus Sponsor:</span>
                    <span class="text-sm font-medium text-gray-900">Rp
                        {{ number_format($totalSponsor, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Dividen:</span>
                    <span class="text-sm font-medium text-gray-900">Rp
                        {{ number_format($totalDividen, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Bonus Generasi:</span>
                    <span class="text-sm font-medium text-gray-900">Rp
                        {{ number_format($totalGenerasi, 0, ',', '.') }}</span>
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
                        {{ number_format($totalPenghasilan, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                    <span class="text-sm text-gray-600">Total Penarikan:</span>
                    <span class="text-sm font-medium text-gray-900">Rp
                        {{ number_format($totalWithdraw, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center py-3 border-t-2 border-blue-200 bg-blue-50 px-3 rounded">
                    <span class="text-base font-semibold text-blue-800">Penghasilan Belum Ditarik:</span>
                    <span class="text-base font-bold text-blue-900">Rp
                        {{ number_format($user->saldo_penghasilan, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Tombol untuk menampilkan/menyembunyikan withdraw form -->
            <div class="mt-6">
                <button type="button" wire:click="toggleWithdrawForm"
                    style="width: 100%; background-color: #6B7280; color: #ffffff; padding: 8px 16px; border: none; border-radius: 6px; font-weight: 600; cursor: pointer;">
                    {{ $showWithdrawForm ? 'Sembunyikan Form Withdraw' : 'Buat Withdraw Baru' }}
                </button>
            </div>
        </section>
    @endif




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
