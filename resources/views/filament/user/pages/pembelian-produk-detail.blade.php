<x-filament::page>
    <div class="w-full mx-auto ">

        @if ($pembelian)

            @if (!$isApprovePage)
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6 ">
                    {{-- Section Kiri - Proses Pembelian --}}
                    <div class="bg-green-100 border border-green-400 text-green-800 px-6 py-4 rounded">
                        <h2 class="font-bold text-lg">Proses Pembelian telah masuk ke system</h2>
                        <p class="text-sm">Silahkan melakukan proses pembayaran ke rekening di bawah ini dan upload bukti
                            transfer anda agar segera diproses oleh {{ $company ? 'Admin' : 'Stockis' }}</p>
                        <div class="mt-2 font-semibold">
                            @if ($company)
                                {{ $company->bank_name }} <br>
                                a.n. {{ $company->bank_atas_nama }} <br>
                            @else
                                {{ $stockis->nama }} <br>
                                a.n. {{ $stockis->nama_rekening }} <br>
                            @endif
                        </div>
                    </div>

                    {{-- Section Kanan - Informasi User Baru (untuk aktivasi member) --}}
                    @if ($pembelian->kategori_pembelian === 'aktivasi member' && isset($userBaru))
                        <div class="bg-blue-100 border border-blue-400 text-blue-800 px-6 py-4 rounded">
                            <h2 class="font-bold text-lg">🎉 Aktivasi Member Berhasil!</h2>
                            <p class="text-sm mb-3">User baru telah berhasil dibuat dengan detail sebagai berikut:</p>
                            <div class="grid grid-cols-1 gap-4 text-sm">
                                <div class="text-3xl font-semibold">
                                    <div class="flex items-center gap-2 ">
                                        <p><span class="font-normal">ID Mitra : </span>
                                            {{ $userBaru->id_mitra }}</p>
                                        <button onclick="copyToClipboard('{{ $userBaru->id_mitra }}', this)"
                                            class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm transition-colors duration-200"
                                            title="Copy ID Mitra">
                                            📋 Copy
                                        </button>
                                    </div>
                                    <p><span class="font-normal">Password : </span> password</p>
                                </div>
                                {{-- <div>
                                    <p><span class="font-semibold">Nama:</span> {{ $userBaru->nama }}</p>
                                    <p><span class="font-semibold">Alamat:</span> {{ $userBaru->alamat }}</p>
                                    <p><span class="font-semibold">No. Telepon:</span> {{ $userBaru->no_telp }}</p>
                                    <p><span class="font-semibold">Bank:</span> {{ $userBaru->bank }}</p>
                                    <p><span class="font-semibold">No. Rekening:</span> {{ $userBaru->no_rek }}</p>
                                    <p><span class="font-semibold">Nama Rekening:</span> {{ $userBaru->nama_rekening }}
                                    </p>
                                </div> --}}
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Informasi User Baru (untuk aktivasi member) - Fallback jika tidak ada section kiri --}}
            @if ($pembelian->kategori_pembelian === 'aktivasi member' && isset($userBaru) && $isApprovePage)
                <div class="bg-blue-100 border border-blue-400 text-blue-800 px-6 py-4 rounded mb-6">
                    <h2 class="font-bold text-lg">🎉 Aktivasi Member Berhasil!</h2>
                    <p class="text-sm mb-3">User baru telah berhasil dibuat dengan detail sebagai berikut:</p>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                        <div class="text-3xl font-semibold">
                            <div class="flex items-center gap-2 ">
                                <p><span class="font-normal">ID Mitra : </span>
                                    {{ $userBaru->id_mitra }}</p>
                                <button onclick="copyToClipboard('{{ $userBaru->id_mitra }}', this)"
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-2 py-1 rounded text-sm transition-colors duration-200"
                                    title="Copy ID Mitra">
                                    📋 Copy
                                </button>
                            </div>
                            <p><span class="font-normal">Password : </span> password</p>
                        </div>
                        {{-- <div>
                            <p><span class="font-semibold">Nama:</span> {{ $userBaru->nama }}</p>
                            <p><span class="font-semibold">Alamat:</span> {{ $userBaru->alamat }}</p>
                            <p><span class="font-semibold">No. Telepon:</span> {{ $userBaru->no_telp }}</p>
                            <p><span class="font-semibold">Bank:</span> {{ $userBaru->bank }}</p>
                            <p><span class="font-semibold">No. Rekening:</span> {{ $userBaru->no_rek }}</p>
                            <p><span class="font-semibold">Nama Rekening:</span> {{ $userBaru->nama_rekening }}</p>
                        </div> --}}
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div class="space-y-4">
                    <div>
                        <p class="text-sm"><span class="font-semibold">Nomor Pembelian:</span> {{ $pembelian->id }}</p>
                        <p class="text-sm"><span class="font-semibold">Tanggal Pembelian:</span>
                            {{ $pembelian->tgl_beli }}</p>
                    </div>

                    <div>
                        <p class="text-sm font-semibold">Detail Pemesan :</p>
                        <p class="text-sm">{{ $pembelian->nama_penerima }}</p>
                        <p class="text-sm">Alamat Pengiriman : {{ $pembelian->alamat_tujuan }}</p>
                        <p class="text-sm">Telp. {{ $pembelian->no_telp }}</p>
                    </div>

                    <div>
                        <p class="text-sm"><span class="font-semibold">Status Pembelian:</span>
                            @php
                                $displayStatus =
                                    $pembelian->images === 'menunggu' && !empty($pembelian->images)
                                        ? 'transfer'
                                        : $pembelian->status_pembelian;
                                $statusColor =
                                    [
                                        'menunggu' => 'text-gray-500',
                                        'transfer' => 'text-yellow-500',
                                        'proses' => 'text-blue-500',
                                        'ditolak' => 'text-red-500',
                                        'selesai' => 'text-green-500',
                                    ][$displayStatus] ?? 'text-gray-500';
                                $statusText =
                                    [
                                        'menunggu' => 'Menunggu Pembayaran',
                                        'transfer' => 'Sudah Di Transfer',
                                        'proses' => 'Diproses admin',
                                        'ditolak' => 'Ditolak / Dibatalkan',
                                        'selesai' => 'Selesai',
                                    ][$displayStatus] ?? ucfirst($displayStatus);
                            @endphp
                            <span class="font-semibold {{ $statusColor }}">
                                {{ $statusText }}
                            </span>
                        </p>
                    </div>

                    {{-- Upload Bukti Transfer --}}
                    <div class="pt-4">
                        <livewire:user.upload-bukti-transfer :pembelian="$pembelian" :isApprovePage="$isApprovePage ?? false" />
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">DETAIL PEMBELIAN</h3>
                    <div class="space-y-4">
                        @foreach ($pembelian->details as $detail)
                            <div class="flex items-center justify-between border-b pb-2">
                                <div class="flex items-center gap-4">
                                    <img src="{{ $detail->produk->gambar ? asset('storage/' . $detail->produk->gambar) : asset('images/empty.webp') }}"
                                        alt="produk" class="w-16 h-16 object-cover rounded">
                                    <div>
                                        {{-- <a href="">{{ asset('storage/' . $detail->produk->gambar) }}</a> --}}
                                        <p class="text-sm font-semibold">{{ $detail->nama_produk ?? 'Nama Produk' }}
                                        </p>
                                        <p class="text-sm text-red-500">
                                            Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}
                                        </p>
                                        <p class="text-xs text-green-600">Cashback: Rp
                                            {{ number_format($detail->jml_beli * 10000, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                <p class="text-sm">Qty: {{ $detail->jml_beli }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t pt-4 text-sm">
                        <div class="flex justify-between">
                            <span>Total Quantity</span>
                            <span>{{ $pembelian->details->sum('jml_beli') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-green-700">Total Cashback</span>
                            <span class="font-semibold text-green-700">Rp
                                {{ number_format($pembelian->total_cashback, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between font-bold">
                            <span>Total Pembelian</span>
                            <span>Rp {{ number_format($pembelian->total_beli, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-8">
                <p class="text-gray-500">Pembelian tidak ditemukan</p>
            </div>
        @endif

        {{-- Section Pembelian Bonuses --}}
        @if ($pembelian && isset($pembelianBonuses) && $pembelianBonuses->count() > 0)
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">BONUS PEMBELIAN</h3>
                <div class="space-y-3">
                    @foreach ($pembelianBonuses as $bonus)
                        <div
                            class="bg-white border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow duration-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">

                                    <div>

                                        @if ($bonus->keterangan)
                                            <div class="flex items-center space-x-2 mt-1">
                                                <span class=" text-gray-600">{{ $bonus->keterangan }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    <div class="text-xs text-gray-500">
                                        {{ $bonus->created_at ? \Carbon\Carbon::parse($bonus->created_at)->format('d/m/Y') : '-' }}
                                    </div>
                                    <div class="text-xs text-gray-400">
                                        {{ $bonus->created_at ? \Carbon\Carbon::parse($bonus->created_at)->format('H:i') : '-' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @elseif ($pembelian)
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-4 text-gray-800">BONUS PEMBELIAN</h3>
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
                    <div class="w-16 h-16 bg-gray-200 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500">Belum ada data bonus untuk pembelian ini</p>
                    <p class="text-sm text-gray-400 mt-1">Bonus akan muncul setelah transaksi selesai</p>
                </div>
            </div>
        @endif
    </div>

    <script>
        function copyToClipboard(text, button) {
            // Try to use the modern Clipboard API first
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    showCopyFeedback(button);
                }).catch(() => {
                    // Fallback to old method
                    fallbackCopyToClipboard(text, button);
                });
            } else {
                // Fallback to old method
                fallbackCopyToClipboard(text, button);
            }
        }

        function fallbackCopyToClipboard(text, button) {
            const tempInput = document.createElement('input');
            tempInput.value = text;
            document.body.appendChild(tempInput);
            tempInput.select();
            tempInput.setSelectionRange(0, 99999);
            document.execCommand('copy');
            document.body.removeChild(tempInput);
            showCopyFeedback(button);
        }

        function showCopyFeedback(button) {
            const originalText = button.innerHTML;
            button.innerHTML = '✅ Copied!';
            button.classList.remove('bg-blue-500', 'hover:bg-blue-600');
            button.classList.add('bg-green-500');

            setTimeout(() => {
                button.innerHTML = originalText;
                button.classList.remove('bg-green-500');
                button.classList.add('bg-blue-500', 'hover:bg-blue-600');
            }, 2000);
        }
    </script>
</x-filament::page>
