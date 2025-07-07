<x-filament::page>
    <div class="w-full mx-auto ">

        @if ($pembelian )
            {{-- Informasi Transfer --}}

            @if(!$isApprovePage)
            <div class="bg-green-100 border border-green-400 text-green-800 px-6 py-4 rounded mb-6">
                <h2 class="font-bold text-lg">Proses Pembelian telah masuk ke system</h2>
                <p class="text-sm">Silahkan melakukan proses pembayaran ke rekening di bawah ini dan upload bukti
                    transfer anda agar segera diproses oleh stockis</p>
                <div class="mt-2 font-semibold">
                    {{ $stockis->nama }} <br>
                    a.n. {{ $stockis->nama_rekening }} <br>
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
                                    $pembelian->images === "menunggu" && !empty($pembelian->images)
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
                                        alt="produk" class="w-12 h-12 object-cover rounded">
                                    <div>
                                        <p class="text-sm font-semibold">{{ $detail->nama_produk ?? 'Nama Produk' }}
                                        </p>
                                        <p class="text-sm text-red-500">
                                            Rp {{ number_format($detail->harga_beli, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                                <p class="text-sm">{{ $detail->jml_beli }}</p>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-6 border-t pt-4 text-sm">
                        <div class="flex justify-between">
                            <span>Total Quantity</span>
                            <span>{{ $pembelian->details->sum('jml_beli') }}</span>
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
</x-filament::page>
