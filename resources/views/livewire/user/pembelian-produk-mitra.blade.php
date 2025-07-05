<div class="flex gap-6 ">
    {{-- Flash Messages --}}
    @if (session()->has('success'))
        <div class="fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded z-50"
            role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="fixed top-4 right-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded z-50"
            role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    @endif

    {{-- Produk --}}
    <div class="w-8/12">
        <h1 class="text-3xl my-4 font-bold">Pembelian Produk oleh Mitra</h1>
        <section class="w-full ">
            <form class="" wire:submit.prevent>
      
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                        {{-- <div>
                            <label for="kota" class="block text-sm font-medium text-gray-700 mb-1">Pilih kota
                                terdekat</label>
                            <select id="kota" name="kota"
                                class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select an option</option>
             
                                @foreach ($kotaList as $kota)
                                    <option value="{{ $kota->id }}">{{ $kota->nama }}</option>
                                @endforeach
                            </select>
                        </div> --}}

                        <div>
                            <label for="stockist" class="block text-sm font-medium text-gray-700 mb-1">Pilih
                                Stockist</label>
                            <select id="stockist" name="stockist"
                                class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                <option value="">Select an option</option>
                                @foreach ($stockistList as $stockist)
                                    <option value="{{ $stockist->id }}">{{ $stockist->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-1">Data Produk</label>
                    <div class="relative">
                        <input type="text" wire:model.live="search"
                            placeholder="Cari produk berdasarkan nama, paket, atau deskripsi..."
                            class="block w-full pl-4 pr-10 py-2 border border-gray-300 rounded-md shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M21 21l-4.35-4.35M16.65 10.35a6.3 6.3 0 11-12.6 0 6.3 6.3 0 0112.6 0z" />
                            </svg>
                        </div>
                    </div>
                    @if (!empty($search))
                        <div class="mt-2 text-sm text-gray-600">
                            Menampilkan {{ $produks->count() }} hasil untuk "{{ $search }}"
                        </div>
                    @endif
                </div>
            </form>
        </section>
        <section class="w-full mx-auto py-4">
            {{-- Tabs --}}
            <div class="flex space-x-4 border-b mb-6">
                <button wire:click="filterByPaket('all')"
                    class="px-4 py-2 font-semibold text-sm border-b-2 transition-colors {{ $activeFilter === 'all' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
                    All Products
                </button>
                <button wire:click="filterByPaket('aktivasi')"
                    class="px-4 py-2 font-semibold text-sm border-b-2 transition-colors {{ $activeFilter === 'aktivasi' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
                    Produk Aktivasi
                </button>
                <button wire:click="filterByPaket('quick_reward')"
                    class="px-4 py-2 font-semibold text-sm border-b-2 transition-colors {{ $activeFilter === 'quick_reward' ? 'border-black text-black' : 'border-transparent text-gray-500 hover:text-black' }}">
                    Produk Quick Reward
                </button>
            </div>
            {{-- Grid Product --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                @forelse ($produks as $produk)
                    <button wire:click="addToCart({{ $produk->id }})" class="flex flex-col items-start">
                        {{-- Card: Gambar + Gradient + Teks --}}
                        <div class="rounded-xl shadow-md overflow-hidden relative w-full">
                            {{-- Gambar --}}
                            <img src="{{ $produk->gambar ? asset('storage/' . $produk->gambar) : asset('images/empty.webp') }}"
                                alt="Product Image" class="w-full h-48 object-cover rounded-t-xl">
                            {{-- Gradient hitam dari bawah --}}
                            <div
                                class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-black/50 via-black/20 to-transparent z-10 rounded-t-xl">
                            </div>
                            {{-- Teks di atas gradient --}}
                            <div class="absolute bottom-3 left-2 z-20 text-white">
                                <h3 class="text-sm font-bold leading-tight">
                                    {{ $produk->nama }}<br>
                                </h3>
                                <p class="text-xs mt-1">
                                    {{ $produk->paket == 1 ? 'Paket Aktivasi' : 'Paket Quick Reward' }}</p>
                            </div>
                            {{-- Glow effect (di bawah semuanya) --}}
                            <div class="absolute inset-0 rounded-t-xl ring-4 ring-white opacity-80 z-0"></div>
                        </div>
                        {{-- Harga di luar card --}}
                        <div class="mt-2 px-1">
                            <p class="text-orange-600 font-semibold text-sm">
                                Rp. {{ number_format($produk->harga_member, 0, ',', '.') }},-
                            </p>
                        </div>
                    </button>
                @empty
                    <div class="col-span-2 md:col-span-4 text-center py-8">
                        <div class="text-gray-500">
                            @if (!empty($search))
                                Tidak ada produk yang cocok dengan pencarian "{{ $search }}"
                            @else
                                Tidak ada produk tersedia
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>
        </section>
    </div>
    {{-- keranjang --}}
    <div class="w-4/12">
        {{-- Bagian cart yang sudah ada sebelumnya --}}
        <div class="sticky top-12 w-full max-w-md  shadow p-5 space-y-4 h-[calc(100vh-3rem)] flex flex-col">
            <h2 class="text-base font-semibold text-gray-800">Detail Pembelian</h2>
            {{-- Daftar Produk Scrollable --}}
            <div class="overflow-y-auto flex-1 space-y-4 pr-2">
                @forelse ($cart as $item)
                    <div class="flex items-center justify-between border-b pb-4">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $item['gambar'] ? asset('storage/' . $item['gambar']) : asset('images/empty.webp') }}"
                                alt="Product" class="w-12 h-12 rounded-full object-cover">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-800">{{ $item['nama'] }}</h4>
                                <p class="text-sm text-red-600 font-semibold">Rp.
                                    {{ number_format($item['harga'], 0, ',', '.') }}</p>
                                {{-- Increment-Decrement --}}
                                <div class="flex items-center mt-2 space-x-2">
                                    <button type="button"
                                        class="w-7 h-7 flex items-center justify-center bg-gray-200 rounded-full hover:bg-gray-300 text-gray-700 text-sm font-bold"
                                        wire:click="decrement({{ $item['id'] }})">−</button>
                                    <span class="text-sm font-medium w-6 text-center">{{ $item['qty'] }}</span>
                                    <button type="button"
                                        class="w-7 h-7 flex items-center justify-center bg-gray-200 rounded-full hover:bg-gray-300 text-gray-700 text-sm font-bold"
                                        wire:click="increment({{ $item['id'] }})">+</button>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="text-red-600 hover:text-red-800 text-lg font-bold"
                            wire:click="remove({{ $item['id'] }})">×</button>
                    </div>
                @empty
                    <div class="text-gray-500 text-center py-8">Keranjang kosong</div>
                @endforelse
            </div>
            {{-- Total & Tombol --}}
            <div class="pt-4 border-t">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Total Pembelian</h3>
                <div class="flex justify-between text-sm text-gray-700">
                    <span>Total Quantity</span>
                    <span>{{ $totalQty }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-700 mb-4">
                    <span>Total Pembelian</span>
                    <span class="font-semibold text-black">Rp. {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
                <button
                    class="w-full bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition"
                    wire:click="checkout" @if ($totalQty == 0) disabled @endif>
                    Proses Pembelian
                </button>
            </div>
        </div>
    </div>
</div>
