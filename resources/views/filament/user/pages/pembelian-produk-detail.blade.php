<x-filament::page>
    <div class="w-full mx-auto ">

        @if ($pembelian)

            @if (!$isApprovePage)
                <div class="bg-green-100 border border-green-400 text-green-800 px-6 py-4 rounded mb-6">
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
            @endif

            {{-- Informasi User Baru (untuk aktivasi member) --}}
            @if ($pembelian->kategori_pembelian === 'aktivasi member' && isset($userBaru))
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
                        <div>
                            <p><span class="font-semibold">Nama:</span> {{ $userBaru->nama }}</p>
                            <p><span class="font-semibold">Alamat:</span> {{ $userBaru->alamat }}</p>
                            <p><span class="font-semibold">No. Telepon:</span> {{ $userBaru->no_telp }}</p>
                            <p><span class="font-semibold">Bank:</span> {{ $userBaru->bank }}</p>
                            <p><span class="font-semibold">No. Rekening:</span> {{ $userBaru->no_rek }}</p>
                            <p><span class="font-semibold">Nama Rekening:</span> {{ $userBaru->nama_rekening }}</p>
                        </div>
                    </div>

                    <p class="text-xs mt-3 text-blue-600">
                        <strong>Catatan:</strong> User baru ini telah terdaftar sebagai member dengan sponsor:
                        {{ auth()->user()->nama }}
                    </p>
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
                                    <img src="{{ $detail->gambar ? asset('storage/' . $detail->gambar) : asset('images/empty.webp') }}"
                                        alt="produk" class="w-8 h-8 object-cover rounded">
                                    <div>
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
