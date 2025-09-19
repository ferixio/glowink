<div class="grid grid-cols-1 gap-6 ">
    <img src="{{asset('images/logo.svg')}}" alt="" srcset="" style="margin:20px auto;max-width:300px;">
    <h2 class="mt-4 text-xl font-bold text-blue-500 md:text-3xl ">Halo, {{ $user->nama }} </h2>

    {{-- Group 1: Info Akun --}}
    @if ($isStockis)
        <div class="p-6 space-y-4 bg-white shadow rounded-xl">
            <h2 class="text-xl font-semibold text-gray-800">Info Stokis</h2>
            <div class="grid ">

                <a href="/user/approve-pembelians?tableFilters[status_pembelian][value]=menunggu"
                    class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                    <div class="text-sm text-gray-500">Butuh Approve Pembelian</div>
                    <div class="mt-1 text-lg font-semibold text-green-600">{{ $totalPembelianStockis }}</div>
                </a>


            </div>
        </div>
    @endif
    {{-- Group 1: Info Akun --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Info Akun</h2>
        <div class="grid grid-cols-2 gap-4 auto-rows-max sm:grid-cols-3 lg:grid-cols-4">

            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">ID</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">{{ $user->id_mitra }}</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Karir Level</div>
                <div
                    class="{{ $isCompletedRObulanan ? 'text-green-500' : 'text-red-500' }} mt-1 text-lg font-semibold text-gray-700">
                    {{ $user->plan_karir_sekarang ?? 'Belum ada plan' }}
                </div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Status QR</div>
                <div class="mt-1 text-lg font-semibold {{ $user->status_qr ? ' text-green-500' : ' text-red-500' }}">
                    {{ $user->status_qr ? 'QR AKTIF' : 'QR NONAKTIF' }}
                </div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Point</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">
                    {{ number_format($user->poin_reward, 0, ',', '.') }}</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Total </div>
                <div class="mt-1 text-lg font-semibold text-green-600">Rp.
                    {{ number_format($user->saldo_withdraw + $user->saldo_penghasilan, 0, ',', '.') }}</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Pending Income</div>
                <div class="mt-1 text-lg font-semibold text-blue-600">Rp.
                    {{ number_format($user->saldo_penghasilan, 0, ',', '.') }}</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Withdraw Income</div>
                <div class="mt-1 text-lg font-semibold text-green-600">Rp.
                    {{ number_format($user->saldo_withdraw, 0, ',', '.') }}</div>
            </div>

        </div>
    </div>

    {{-- Group 2: Dividen Omset Nasional --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Dividen Omset Nasional</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-2">
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Deviden Hari Ini</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp 0</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Total Penerima</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">629 Member</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Total Deviden Bulan Lalu</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp 0</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Total Penerima Bulan Lalu</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">531 Member</div>
            </div>
        </div>
    </div>

    {{-- Group 3: Royalti Quick Reward --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Royalti Quick Reward</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-2">
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Potensi Omset QR</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">7 QR</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Potensi Penerima</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">1 Member</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Omset QR Bulan Lalu</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">4 QR</div>
            </div>
            <div class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Penerima Bulan Lalu</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">1 Member</div>
            </div>
        </div>
    </div>

    {{-- Group 4: Member Quick Reward --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Member Quick Reward</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-7">
            <div class="p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Bronze</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">50</div>
            </div>
            <div class="p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Silver</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">15</div>
            </div>
            <div class="p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Gold</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">3</div>
            </div>
            <div class="p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Platinum</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">0</div>
            </div>
            <div class="p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Titanium</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">0</div>
            </div>
            <div class="p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Ambassador</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">0</div>
            </div>
            <div class="p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <div class="text-sm text-gray-500">Chairman</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">0</div>
            </div>
        </div>
    </div>

    {{-- Group 5: Tabel Aktivitas --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Aktivitas Terbaru</h2>

        @if ($aktivitas->count() > 0)
            <div class="space-y-3">
                @foreach ($aktivitas as $activity)
                    <div class="p-4 transition bg-white rounded-lg shadow hover:shadow-md">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-xs text-gray-500">
                                    {{ \Carbon\Carbon::parse($activity->created_at)->format('d/m/Y H:i') }}
                                </p>
                                <p class="text-sm font-semibold text-gray-900">
                                    {{ $activity->judul }}
                                </p>
                                <p class="w-64 text-xs text-gray-500 md:w-full">
                                    {{ $activity->keterangan ?? '-' }}
                                </p>
                            </div>
                            <div class="text-right">
                                @if ($activity->nominal)
                                    <p
                                        class="text-sm font-bold {{ $activity->tipe === 'plus' ? 'text-green-600' : ($activity->tipe === 'minus' ? 'text-red-600' : 'text-gray-700') }}">
                                        {{ $activity->tipe === 'plus' ? '+' : ($activity->tipe === 'minus' ? '-' : '') }}
                                        {{ $activity->nominal > 50 ? 'Rp ' : '' }}
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
            <div class="py-8 text-center">
                <div class="text-sm text-gray-500">Belum ada aktivitas</div>
            </div>
        @endif
    </div>

</div>
