<div class="grid grid-cols-1 gap-6 ">
    <img src="{{asset('images/logo.svg')}}" alt="" srcset="" style="margin:20px auto;max-width:300px;">
    <h2 class="mt-4 text-xl font-bold text-blue-500 md:text-3xl ">Halo, {{ $user->nama }} </h2>

    {{-- Group menu --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Menu Utama</h2>
        <div class="grid grid-cols-2 gap-4 text-center auto-rows-max xs:grid-cols-4 sm:grid-cols-4 lg:grid-cols-4">

            <a href="/user/pembelian-produk">
                <div class="p-8 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                    <img src="/images/icon/shopping.png" alt="" srcset="">
                    <p class="py-2 font-bold font-lg"">Belanja</p>
                </div>
            </a>
            <a href="/user/penghasilans">
                <div class="p-8 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                   <img src="/images/icon/earning.png" alt="" srcset="">
                   <p class="py-2 font-bold font-lg"">Penghasilan</p>
                </div>
            </a>
            <a href="/user/jaringan">
                <div class="p-8 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                   <img src="/images/icon/networking.png" alt="" srcset="">
                   <p class="py-2 font-bold font-lg"">Jaringan</p>
                </div>
            </a>
            <a href="/user/product-stocks">
                <div class="p-8 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                   <img src="/images/icon/stok.png" alt="" srcset="">
                   <p class="py-2 font-bold font-lg"">Stok</p>
                </div>
            </a>


        </div>
    </div>

    {{-- Group 1: Info Akun --}}
    @if ($isStockis)
        <div class="p-6 space-y-4 bg-white shadow rounded-xl">
            <h2 class="text-xl font-semibold text-gray-800">Info Stokis</h2>
            <div class="grid ">

                <a href="/user/approve-pembelians?tableFilters[status_pembelian][value]=menunggu"
                    class="p-4 border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                   <div class="flex">
                       <div class="pt-2">
                         <img src="/images/icon/stockis.png" alt="" srcset=""  style="max-width: 96px;">
                       </div>
                       <div style="padding: 5px 20px" class="">
                            <div class="text-sm font-bold text-gray-500">Approve Pembelian Mitra</div>
                            <div class="mt-1 text-xl font-bold text-blue-500 ">{{ $totalPembelianStockis }} Transaksi</div>
                            <p class="px-4 py-2 mt-2 font-bold text-center text-white bg-blue-500 rounded-md">Lihat Detail</p>
                       </div>
                   </div>
                </a>


            </div>
        </div>
    @endif


    {{-- Group 1: Info Akun --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Info Akun Mitra</h2>
        <div class="grid grid-cols-2 gap-4 auto-rows-max sm:grid-cols-3 lg:grid-cols-4">

            <div class="">
                <div class="text-sm text-gray-500">ID Mitra</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">{{ $user->id_mitra }}</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">ID System</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">{{ $user->id }}</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Karir Level</div>
                <div
                    class="{{ $isCompletedRObulanan ? 'text-green-500' : 'text-red-500' }} mt-1 text-lg font-semibold text-gray-700">
                    {{ $user->plan_karir_sekarang ?? 'Belum ada plan' }}
                </div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Status QR</div>
                <div class="mt-1 text-lg font-semibold {{ $user->status_qr ? ' text-green-500' : ' text-red-500' }}">
                    {{ $user->status_qr ? 'QR AKTIF' : 'QR NONAKTIF' }}
                </div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Point</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">
                    {{ number_format($user->poin_reward, 0, ',', '.') }}</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Total Penghasilan</div>
                <div class="mt-1 text-lg font-semibold text-green-600">Rp.
                    {{ number_format($user->saldo_withdraw + $user->saldo_penghasilan, 0, ',', '.') }}</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Pending Income</div>
                <div class="mt-1 text-lg font-semibold text-blue-600">Rp.
                    {{ number_format($user->saldo_penghasilan, 0, ',', '.') }}</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Withdraw Income</div>
                <div class="mt-1 text-lg font-semibold text-green-600">Rp.
                    {{ number_format($user->saldo_withdraw, 0, ',', '.') }}</div>
            </div>

        </div>
    </div>

    {{-- Group 3: Royalti Quick Reward --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Detail Bonus Karir</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-2">
            <div class="">
                <div class="text-sm text-gray-500">Bonus Sponsor</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp. 0</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Bonus Generasi</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp. 0</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Cashback</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp. 0</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Total Bonus Diterima</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp. 0</div>
            </div>
        </div>
    </div>

    {{-- Group 2: Dividen Omset Nasional --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Detail Dividen</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div class="">
                <div class="text-sm text-gray-500">Deviden Harian</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp. 0</div>
            </div>
            <div class="">
                <div class="text-sm text-gray-500">Deviden Bulanan</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp. 0</div>
            </div>
            <div class="p-4 bg-blue-100 rounded-lg col-span-full">
                <div class="text-sm text-gray-500">Total Deviden</div>
                <div class="mt-1 text-lg font-semibold text-gray-700">Rp. 0</div>
            </div>

        </div>
    </div>



    {{-- Group 4: Member Quick Reward --}}
    <div class="p-6 space-y-4 bg-white shadow rounded-xl">
        <h2 class="text-xl font-semibold text-gray-800">Level Karir</h2>
        <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 lg:grid-cols-7">
            <div class="flex flex-col items-center p-4 border border-gray-200 shadow-sm bg-gray-10 rounded-xl">
                <img src="/images/icon/level/01.png" alt="" srcset="" style="width: 48px">
                <div class="mt-2 text-sm font-bold text-gray-500">Bronze ( <span class="text-blue-500 rounded-full ">{{ $level['bronze'] }} </span> )</div>

            </div>
            <div class="flex flex-col items-center p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <img src="/images/icon/level/02.png" alt="" srcset="" style="width: 48px">
                <div class="mt-2 text-sm font-bold text-gray-500">Silver ( <span class="text-blue-500 rounded-full ">{{ $level['silver'] }} </span> )</div>
            </div>
            <div class="flex flex-col items-center p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <img src="/images/icon/level/03.png" alt="" srcset="" style="width: 48px">
                <div class="mt-2 text-sm font-bold text-gray-500">Gold ( <span class="text-blue-500 rounded-full ">{{ $level['gold'] }} </span> )</div>

            </div>
            <div class="flex flex-col items-center p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <img src="/images/icon/level/04.png" alt="" srcset="" style="width: 48px">
                <div class="mt-2 text-sm font-bold text-gray-500">Platinum ( <span class="text-blue-500 rounded-full ">{{ $level['platinum'] }} </span> )</div>

            </div>
            <div class="flex flex-col items-center p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <img src="/images/icon/level/05.png" alt="" srcset="" style="width: 48px">
                <div class="mt-2 text-sm font-bold text-gray-500">Titanium ( <span class="text-blue-500 rounded-full ">{{ $level['titanium'] }} </span> )</div>
            </div>
            <div class="flex flex-col items-center p-4 text-center border border-gray-200 shadow-sm bg-gray-50 rounded-xl">
                <img src="/images/icon/level/06.png" alt="" srcset="" style="width: 48px">
                <div class="mt-2 text-sm font-bold text-gray-500">Ambassador ( <span class="text-blue-500 rounded-full ">{{ $level['ambassador'] }} </span> )</div>

            </div>
            <div class="flex flex-col items-center p-4 text-center border border-gray-200 shadow-sm col-span-full bg-gray-50 rounded-xl">
                <img src="/images/icon/level/07.png" alt="" srcset="" style="width: 48px">
                <div class="mt-2 text-sm font-bold text-gray-500">Chairman ( <span class="text-blue-500 rounded-full ">{{ $level['chairman'] }} </span> )</div>

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
