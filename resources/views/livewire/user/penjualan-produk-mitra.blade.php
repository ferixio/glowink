<div class="flex gap-6">
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

    {{-- Mobile/Tablet Cart Icon --}}
    <div class="lg:hidden fixed bottom-4 right-4 z-40">
        <button wire:click="toggleCartSidebar"
            class="relative bg-orange-200 hover:bg-orange-300 text-gray-800  p-3 rounded-full shadow-lg transition-colors">
            <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" id="cart">
                <path
                    d="M8.5,19A1.5,1.5,0,1,0,10,20.5,1.5,1.5,0,0,0,8.5,19ZM19,16H7a1,1,0,0,1,0-2h8.49121A3.0132,3.0132,0,0,0,18.376,11.82422L19.96143,6.2749A1.00009,1.00009,0,0,0,19,5H6.73907A3.00666,3.00666,0,0,0,3.92139,3H3A1,1,0,0,0,3,5h.92139a1.00459,1.00459,0,0,1,.96142.7251l.15552.54474.00024.00506L6.6792,12.01709A3.00006,3.00006,0,0,0,7,18H19a1,1,0,0,0,0-2ZM17.67432,7l-1.2212,4.27441A1.00458,1.00458,0,0,1,15.49121,12H8.75439l-.25494-.89221L7.32642,7ZM16.5,19A1.5,1.5,0,1,0,18,20.5,1.5,1.5,0,0,0,16.5,19Z">
                </path>
            </svg>
            @if ($totalQty > 0)
                <span
                    class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full h-6 w-6 flex items-center justify-center font-bold">
                    {{ $totalQty }}
                </span>
            @endif
        </button>
    </div>

    {{-- Mobile/Tablet Cart Sidebar --}}
    @if ($showCartSidebar)
        <div class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-50" wire:click="toggleCartSidebar"></div>
        <div class="lg:hidden fixed top-0 right-0 h-full w-80 bg-white shadow-xl z-50 transform transition-transform duration-300 ease-in-out"
            x-data x-init="$nextTick(() => {
                $el.addEventListener('click', (e) => e.stopPropagation());
            })">
            <div class="flex items-center justify-between p-4 border-b">
                <h2 class="text-lg font-semibold text-gray-800">Keranjang Penjualan</h2>
                <button wire:click="toggleCartSidebar" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="flex flex-col h-full">
                {{-- Cart Items --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-4">
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

                {{-- Cart Summary --}}
                <div class="p-4 border-t bg-gray-50">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">Total Penjualan</h3>
                    <div class="flex justify-between text-sm text-gray-700 mb-2">
                        <span>Total Quantity</span>
                        <span>{{ $totalQty }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-700 mb-4">
                        <span>Total Penjualan</span>
                        <span class="font-semibold text-black">Rp. {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>
                    <button
                        class="w-full bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition"
                        wire:click="checkout" @if ($totalQty == 0) disabled @endif>
                        Proses Penjualan
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Produk --}}
    <div class="w-full lg:w-8/12 pt-0">
        <h1 class="text-3xl my-4 font-bold">Penjualan Produk Mitra</h1>
        <section class="w-full py-2">
            <form class="space-y-6" wire:submit.prevent>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Pembeli</label>
                        <input type="text" id="nama" wire:model="nama"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Nama pembeli">
                        @error('nama')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="telepon" class="block text-sm font-medium text-gray-700">No Telp</label>
                        <input type="text" id="telepon" wire:model="telepon"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Nomor telepon pembeli">
                        @error('telepon')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat Pembeli</label>
                        <input type="text" id="alamat" wire:model="alamat"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Alamat pembeli">
                        @error('alamat')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        <label for="tanggal" class="block text-sm font-medium text-gray-700">Tanggal Transaksi</label>
                        <input type="date" id="tanggal" wire:model="tanggal"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm">
                        @error('tanggal')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
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
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                @forelse ($produks as $produk)
                    <button wire:click="addToCart({{ $produk->id }})"
                        class="flex flex-col items-start {{ ($stokData[$produk->id] ?? 0) == 0 ? 'opacity-50 cursor-not-allowed' : 'hover:scale-105 transition-transform' }}"
                        @if (($stokData[$produk->id] ?? 0) == 0) disabled @endif>
                        {{-- Card Gambar --}}
                        <div class="rounded-xl shadow-md overflow-hidden relative w-full">
                            {{-- Gambar --}}
                            <img src="{{ $produk->gambar ? asset('storage/' . $produk->gambar) : asset('images/empty.webp') }}"
                                alt="Product Image" class="w-full h-32 md:h-40 lg:h-48 object-cover rounded-t-xl">

                            {{-- Info Stok di atas gambar --}}
                            @if (($stokData[$produk->id] ?? 0) > 0)
                                <div
                                    class="absolute bottom-2 left-2 bg-green-600 text-white text-xs px-2 py-1 rounded z-20 shadow">
                                    Stok: {{ $stokData[$produk->id] ?? 0 }}
                                </div>
                            @else
                                <div
                                    class="absolute bottom-2 left-2 bg-red-600 text-white text-xs px-2 py-1 rounded z-20 shadow">
                                    Stok Habis
                                </div>
                            @endif

                            {{-- Glow (jika masih ingin pakai) --}}
                            <div class="absolute inset-0 rounded-t-xl ring-4 ring-white opacity-80 z-0">
                            </div>
                        </div>

                        {{-- Info Produk di bawah gambar --}}
                        <div class="mt-2 px-1 text-left w-full">
                            <h3 class="text-xs md:text-sm font-bold leading-tight text-gray-800">
                                {{ $produk->nama }}
                            </h3>
                            <p class="text-xs text-gray-600 mt-1">
                                {{ $produk->paket == 1 ? 'Paket Aktivasi' : 'Paket Quick Reward' }}
                            </p>
                            <p class="text-orange-600 font-semibold text-xs md:text-sm mt-2">
                                Rp. {{ number_format($produk->harga_member, 0, ',', '.') }},-
                            </p>
                        </div>
                    </button>
                @empty
                    <div class="col-span-2 md:col-span-3 lg:col-span-4 text-center py-8">
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
    <div class="hidden lg:block w-4/12">
        <div class="sticky top-12 w-full max-w-md  shadow p-5 space-y-4 h-[calc(100vh-3rem)] flex flex-col">
            <h2 class="text-base font-semibold text-gray-800">Detail Penjualan</h2>
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
            <div class="pt-4 border-t">
                <h3 class="text-sm font-semibold text-gray-800 mb-2">Total Penjualan</h3>
                <div class="flex justify-between text-sm text-gray-700">
                    <span>Total Quantity</span>
                    <span>{{ $totalQty }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-700 mb-4">
                    <span>Total Penjualan</span>
                    <span class="font-semibold text-black">Rp. {{ number_format($totalPrice, 0, ',', '.') }}</span>
                </div>
                <button
                    class="w-full bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition"
                    wire:click="checkout" @if ($totalQty == 0) disabled @endif>
                    Proses Penjualan
                </button>
            </div>
        </div>
    </div>
</div>
