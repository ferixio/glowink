<div class="grid grid-cols-1 gap-6 ">
    <h2 class="md:text-3xl text-xl mt-4 text-blue-500 font-bold ">Halo, {{ $user->nama }} </h2>

    {{-- Group 1: Info Akun --}}
    @if ($isStockis)
        <div class="bg-white rounded-xl shadow p-6 space-y-4">
            <h2 class="text-xl font-semibold text-gray-800">Info Stokis</h2>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-3 gap-4">

                <a href="/user/approve-pembelians?tableFilters[status_pembelian][value]=menunggu" class="bg-gray-50 border border-gray-200 rounded-xl p-4 shadow-sm">
                    <div class="text-sm text-gray-500">Butuh Approve Pembelian</div>
                    <div class="mt-1 text-lg font-semibold text-green-600">{{ $totalPembelianStockis }}</div>
                </a>


            </div>
        </div>
    @endif
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
            <div class="space-y-3">
                @foreach ($aktivitas as $activity)
                    <div class="p-4 bg-white rounded-lg shadow hover:shadow-md transition">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $activity->judul }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    {{ $activity->keterangan ?? '-' }}
                                </p>
                            </div>
                            <div class="text-right">
                                @if ($activity->nominal)
                                    <p
                                        class="text-sm font-bold {{ $activity->tipe === 'plus' ? 'text-green-600' : 'text-red-600' }}">
                                        {{ $activity->tipe === 'plus' ? '+' : '-' }} Rp
                                        {{ number_format($activity->nominal, 0, ',', '.') }}
                                    </p>
                                @else
                                    <p class="text-sm text-gray-500">-</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8">
                <div class="text-gray-500 text-sm">Belum ada aktivitas</div>
            </div>
        @endif
    </div>

</div>
