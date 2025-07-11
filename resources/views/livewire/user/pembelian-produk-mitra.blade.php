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
                <h2 class="text-lg font-semibold text-gray-800">Keranjang Belanja</h2>
                <button wire:click="toggleCartSidebar" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>
            <div class="flex flex-col h-full">
                {{-- Cart Items --}}
                <div class="flex-1 overflow-y-auto max-h-96 px-4 pt-2">
                    @forelse ($cart as $item)
                        <div class="flex items-center  justify-between border-b pb-4">
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
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">Total Pembelian</h3>
                    <div class="flex justify-between text-sm text-gray-700 mb-2">
                        <span>Total Quantity</span>
                        <span>{{ $totalQty }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-700 mb-4">
                        <span>Total Pembelian</span>
                        <span class="font-semibold text-black">Rp. {{ number_format($totalPrice, 0, ',', '.') }}</span>
                    </div>

                    @if ($currentPage === 0)
                        <button
                            class="w-full mb-2 bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition"
                            wire:click="changePage(1)" @if ($totalQty == 0) disabled @endif>
                            Aktivasi Member Baru
                        </button>
                        <button
                            class="w-full mb-2 bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition"
                            wire:click="stockPribadi" @if ($totalQty == 0) disabled @endif>
                            Stock Pribadi
                        </button>
                        <button
                            class="w-full mb-2 bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition"
                            wire:click="changePage(3)" @if ($totalQty == 0) disabled @endif>
                            Repeat Order / Dropshop
                        </button>
                        {{-- <button
                            class="w-full bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-2 px-4 rounded-md transition"
                            wire:click="changePage(3)" @if ($totalQty == 0) disabled @endif>
                            Repeat Order / Dropshop
                        </button> --}}
                    @endif
                </div>
            </div>
        </div>
    @endif

    {{-- Produk --}}
    <section class="w-full lg:w-8/12 ">
        @php
            switch ($currentPage) {
                case 0:
                    $title = 'Pembelian Produk oleh Mitra';
                    break;
                case 1:
                    $title = 'Aktivasi Member Baru';
                    break;
                case 2:
                    $title = 'Stock Pribadi';
                    break;
                case 3:
                    $title = 'Repeat Order / Dropshop';
                    break;
                default:
                    $title = 'Pembelian Produk oleh Mitra';
            }
        @endphp

        <h1 class="md:text-3xl text-2xl my-4 font-bold">{{ $title }}</h1>
        @if ($currentPage === 0)

            <section class="w-full">
                <section class="w-full ">
                    <form class="" wire:submit.prevent>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div class="relative">
                                <label for="kabupaten" class="block text-sm font-medium text-gray-700 mb-1">Cari
                                    Kabupaten/Kota</label>
                                <div class="relative">
                                    <input type="text" id="kabupaten" wire:model.live="kabupatenSearch"
                                        wire:click="showAllKabupaten"
                                        placeholder="Ketik untuk mencari kabupaten/kota..."
                                        class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400 pr-10">

                                    @if (!empty($kabupatenSearch))
                                        <button type="button" wire:click="clearKabupatenSearch"
                                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    @endif
                                </div>

                                @if ($showKabupatenDropdown && !empty($filteredKabupatenList))
                                    <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto"
                                        x-data x-init="$nextTick(() => {
                                            $el.addEventListener('click', (e) => e.stopPropagation());
                                            document.addEventListener('click', () => {
                                                @this.closeKabupatenDropdown();
                                            });
                                        })">
                                        @foreach ($filteredKabupatenList as $kabupaten)
                                            <button type="button"
                                                wire:click="selectKabupaten('{{ $kabupaten['nama'] }}')"
                                                class="w-full text-left px-4 py-2 text-sm hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
                                                {{ $kabupaten['nama'] }}
                                            </button>
                                        @endforeach
                                    </div>
                                @endif

                            </div>

                            <div>
                                <label for="stockist" class="block text-sm font-medium text-gray-700 mb-1">Pilih
                                    Stockist</label>
                                <select id="stockist" name="stockist" wire:model.live="selectedStockist"
                                    class="w-full border border-gray-300 rounded px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-yellow-400">
                                    @if (empty($selectedKabupaten))
                                        <option value="">Pilih Kabupaten/Kota terlebih dahulu</option>
                                    @elseif ($stockistList->isEmpty())
                                        <option value="">Tidak ada Stockist di {{ $selectedKabupaten }}</option>
                                    @else
                                        <option value="">Pilih Stockist untuk melihat stok produk</option>
                                        @foreach ($stockistList as $stockist)
                                            <option value="{{ $stockist->id }}">{{ $stockist->nama }}</option>
                                        @endforeach
                                    @endif
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
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor"
                                        stroke-width="2" viewBox="0 0 24 24">
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
                    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 lg:gap-6">
                        @forelse ($produks as $produk)
                            <button wire:click="addToCart({{ $produk->id }})" class="flex flex-col items-start">
                                {{-- Card: Gambar + Gradient + Teks --}}
                                <div class="rounded-xl shadow-md overflow-hidden relative w-full">
                                    {{-- Gambar --}}
                                    <img src="{{ $produk->gambar ? asset('storage/' . $produk->gambar) : asset('images/empty.webp') }}"
                                        alt="Product Image"
                                        class="w-full h-32 md:h-40 lg:h-48 object-cover rounded-t-xl">
                                    {{-- Gradient hitam dari bawah --}}
                                    <div
                                        class="absolute bottom-0 left-0 right-0 h-16 md:h-20 lg:h-20 bg-gradient-to-t from-black/50 via-black/20 to-transparent z-10 rounded-t-xl">
                                    </div>
                                    {{-- Teks di atas gradient --}}
                                    <div class="absolute bottom-2 md:bottom-3 left-2 z-20 text-white">
                                        <h3 class="text-xs md:text-sm font-bold leading-tight">
                                            {{ $produk->nama }}<br>
                                        </h3>
                                        <p class="text-xs mt-1">
                                            {{ $produk->paket == 1 ? 'Paket Aktivasi' : 'Paket Quick Reward' }}</p>
                                        @if (isset($produk->stok_tersedia))
                                            <p class="text-xs mt-1 bg-green-600 px-2 py-1 rounded">
                                                Stok: {{ $produk->stok_tersedia }}
                                            </p>
                                        @endif
                                    </div>
                                    {{-- Glow effect (di bawah semuanya) --}}
                                    <div class="absolute inset-0 rounded-t-xl ring-4 ring-white opacity-80 z-0"></div>
                                </div>
                                {{-- Harga di luar card --}}
                                <div class="mt-2 px-1">
                                    <p class="text-orange-600 font-semibold text-xs md:text-sm">
                                        Rp. {{ number_format($produk->harga_member, 0, ',', '.') }},-
                                    </p>
                                </div>
                            </button>
                        @empty
                            <div class="col-span-2 md:col-span-3 lg:col-span-4 text-center py-8">
                                <div class="text-gray-500">
                                    @if (!empty($search))
                                        Tidak ada produk yang cocok dengan pencarian "{{ $search }}"
                                    @elseif (empty($selectedKabupaten))
                                        Silakan pilih Kabupaten/Kota terlebih dahulu
                                    @elseif (empty($selectedStockist))
                                        Silakan pilih Stockist untuk melihat stok produk yang tersedia
                                    @else
                                        Stockist ini tidak memiliki stok produk yang tersedia
                                    @endif
                                </div>
                            </div>
                        @endforelse
                    </div>

                </section>
            </section>


            {{-- halaman aktivasi member --}}
        @elseif ($currentPage === 1)
            <section class="w-full">
                <form class="space-y-6">

                    <div>
                        <label for="nama" class="block text-sm font-medium text-gray-700">Nama Member (Sesuai Nama
                            di Rekening)</label>
                        <input type="text" id="nama" wire:model="nama"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Masukkan nama Anda sesuai rekening">
                        @error('nama')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                        <div>
                            <label for="nama_bank" class="block text-sm font-medium text-gray-700">Nama Bank</label>
                            <input type="text" id="nama_bank" wire:model="nama_bank"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                placeholder="Masukkan nama bank Anda">
                            @error('nama_bank')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="no_rekening" class="block text-sm font-medium text-gray-700">No.
                                Rekening</label>
                            <input type="text" id="no_rekening" wire:model="no_rekening"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                placeholder="Masukkan nomor rekening Anda">
                            @error('no_rekening')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700">No telepon /
                                whatsapp</label>
                            <input type="text" id="telepon" wire:model="telepon"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                placeholder="Masukkan nomor telepon atau whatsapp Anda">
                            @error('telepon')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat
                                Pengiriman</label>
                            <input type="text" id="alamat" wire:model="alamat"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                placeholder="Masukkan alamat pengiriman">
                            @error('alamat')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                    </div>
                </form>
                <button
                    class="w-full mt-6  bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-1.5 px-4 rounded-md transition"
                    wire:click="aktivasiMember" @if ($totalQty == 0) disabled @endif>
                    Proses Pesanan
                </button>
            </section>

            {{-- halaman stock pribadi --}}
        @elseif ($currentPage === 3)
            <section class="w-full">
                <form class="space-y-6">
                    <div>
                        <label for="namaPenerima" class="block text-sm font-medium text-gray-700">Nama
                            Penerima</label>
                        <input type="text" id="namaPenerima" wire:model="namaPenerima"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                            placeholder="Masukkan nama penerima">
                        @error('namaPenerima')
                            <span class="text-red-500 text-sm">{{ $message }}</span>
                        @enderror
                    </div>


                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="telepon" class="block text-sm font-medium text-gray-700">No telepon /
                                whatsapp</label>
                            <input type="text" id="telepon" wire:model="telepon"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                placeholder="Masukkan nomor telepon atau whatsapp Pemesan">
                            @error('telepon')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>
                        <div>
                            <label for="alamat" class="block text-sm font-medium text-gray-700">Alamat
                                Pengiriman</label>
                            <input type="text" id="alamat" wire:model="alamat"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring-orange-500 focus:border-orange-500 sm:text-sm"
                                placeholder="Masukkan alamat pengiriman">
                            @error('alamat')
                                <span class="text-red-500 text-sm">{{ $message }}</span>
                            @enderror
                        </div>

                        <button
                            class="w-full mt-6  bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-1.5 px-4 rounded-md transition"
                            wire:click="repeatOrder" @if ($totalQty == 0) disabled @endif>
                            Proses Pesanan
                        </button>

                    </div>
                </form>
            </section>
        @else
            <section>
                <h2>Default</h2>
                <!-- Konten default -->
            </section>
        @endif

    </section>
    {{-- keranjang --}}
    <section class="hidden lg:block w-4/12">
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
                @if ($currentPage === 0)
                    <button
                        class="w-full mb-2 bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-1.5 px-4 rounded-md transition"
                        wire:click="changePage(1)" @if ($totalQty == 0) disabled @endif>
                        Aktivasi Member Baru
                    </button>
                    <button
                        class="w-full mb-2 bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-1.5 px-4 rounded-md transition"
                        wire:click="stockPribadi" @if ($totalQty == 0) disabled @endif>
                        Stock Pribadi
                    </button>
                    <button
                        class="w-full  bg-orange-200 hover:bg-orange-300 text-gray-800 font-semibold py-1.5 px-4 rounded-md transition"
                        wire:click="changePage(3)" @if ($totalQty == 0) disabled @endif>
                        Repeat Order / Dropshop
                    </button>
                @endif
            </div>
        </div>
    </section>
</div>
