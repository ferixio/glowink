<div class="grid grid-cols-1 gap-6 ">
    <h2 class="text-3xl mt-6 font-bold text-gray-800">Halo, {{ $user->nama }} </h2>

    {{-- Group 1: Info Akun --}}
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-semibold text-gray-800">Info Akun</h2>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">ID</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">{{ $user->id_mitra }}</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Karir Level</div>
                <div
                    class="{{ $isCompletedRObulanan ? 'text-green-500' : 'text-red-500' }} mt-1 text-lg font-semibold text-gray-700">
                    {{ $user->plan_karir_sekarang ?? 'Belum ada plan' }}
                </div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Status QR</div>
                <div class="mt-1 text-lg font-semibold {{ $user->status_qr ? ' text-green-500' : ' text-red-500' }}">
                    {{ $user->status_qr ? 'QR AKTIF' : 'QR NONAKTIF' }}
                </div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Total Penghasilan</div>
                <div class="mt-1 text-lg font-semibold text-green-600">Rp.
                    {{ number_format($user->saldo_withdraw, 0, ',', '.') }}</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Pending Income</div>
                <div class="mt-1 text-lg font-semibold text-blue-600">Rp.
                    {{ number_format($user->saldo_penghasilan, 0, ',', '.') }}</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Point</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">
                    {{ number_format($user->poin_reward, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Group 2: Dividen Omset Nasional --}}
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-semibold text-gray-800">Dividen Omset Nasional</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-2 gap-4">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Deviden Hari Ini</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp 0</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Total Penerima</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">629 Member</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Total Deviden Bulan Lalu</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp 0</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Total Penerima Bulan Lalu</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">531 Member</div>
            </div>
        </div>
    </div>

    {{-- Group 3: Royalti Quick Reward --}}
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-semibold text-gray-800">Royalti Quick Reward</h2>
        <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-2 gap-4">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Potensi Omset QR</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">7 QR</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Potensi Penerima</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">1 Member</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Omset QR Bulan Lalu</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">4 QR</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                <div class="text-sm text-gray-500">Penerima Bulan Lalu</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">1 Member</div>
            </div>
        </div>
    </div>

    {{-- Group 4: Member Quick Reward --}}
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-semibold text-gray-800">Member Quick Reward</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm text-center">
                <div class="text-sm text-gray-500">Bronze</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">50</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm text-center">
                <div class="text-sm text-gray-500">Silver</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">15</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm text-center">
                <div class="text-sm text-gray-500">Gold</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">3</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm text-center">
                <div class="text-sm text-gray-500">Platinum</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">0</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm text-center">
                <div class="text-sm text-gray-500">Titanium</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">0</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm text-center">
                <div class="text-sm text-gray-500">Ambassador</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">0</div>
            </div>
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm text-center">
                <div class="text-sm text-gray-500">Chairman</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">0</div>
            </div>
        </div>
    </div>

    {{-- Group 5: Tabel Aktivitas --}}
    <div class="bg-white rounded-xl shadow p-6 space-y-4">
        <h2 class="text-xl font-semibold text-gray-800">Aktivitas Terbaru</h2>

        @if ($aktivitas->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tanggal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Judul
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Keterangan
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Status
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Nominal
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tipe
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($aktivitas as $activity)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $activity->judul }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $activity->keterangan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                        {{ $activity->status === 'success'
                                            ? 'bg-green-100 text-green-800'
                                            : ($activity->status === 'pending'
                                                ? 'bg-yellow-100 text-yellow-800'
                                                : ($activity->status === 'failed'
                                                    ? 'bg-red-100 text-red-800'
                                                    : 'bg-gray-100 text-gray-800')) }}">
                                        {{ $activity->status ?? 'N/A' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    @if ($activity->nominal)
                                        <span
                                            class="{{ $activity->tipe === 'plus' ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $activity->tipe === 'plus' ? '+' : '-' }} Rp
                                            {{ number_format($activity->nominal, 0, ',', '.') }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td>
                                {{-- <td class="px-6 py-4 whitespace-nowrap">
                                    @if ($activity->tipe)
                                        <span
                                            class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $activity->tipe === 'plus' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                            {{ ucfirst($activity->tipe) }}
                                        </span>
                                    @else
                                        -
                                    @endif
                                </td> --}}
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-500 text-sm">Belum ada aktivitas</div>
            </div>
        @endif
    </div>

</div>
