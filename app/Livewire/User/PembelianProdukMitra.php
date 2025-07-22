<?php

namespace App\Livewire\User;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use App\Models\ProdukStok;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PembelianProdukMitra extends Component
{

    public $cart = [];
    public $stockistList = [];
    public $selectedStockist = '';
    public $totalQty = 0;
    public $totalPrice = 0;
    public $produks = [];
    public $search = '';
    public $filteredProduks = [];
    public $activeFilter = 'all';

    public $currentPage = 0;

    // Kabupaten filtering properties
    public $kabupatenList = [];
    public $selectedKabupaten = '';

    // Form properties
    public $nama = '';
    public $namaPenerima = '';
    public $telepon = '';
    public $alamat = '';
    public $tanggal = '';
    public $nama_bank = '';
    public $no_rekening = '';

    // Cart sidebar properties
    public $showCartSidebar = false;

    public function mount()
    {
        $this->cart = Session::get('cart', []);
        $this->currentPage = 0;
        // $this->loadProduks();
        $this->updateTotals();
        $this->loadKabupatenList();
        $this->loadStockis();
    }

    public function loadKabupatenList()
    {
        // Ambil kabupaten unik dari user yang isStockis = true
        $kabupatenList = \App\Models\User::where('isStockis', true)
            ->whereNotNull('kabupaten')
            ->pluck('kabupaten')
            ->unique()
            ->filter(function ($value) {
                return !empty($value);
            })
            ->sort()
            ->values()
            ->all();

        $this->kabupatenList = collect($kabupatenList)->map(function ($nama, $idx) {
            return [
                'id' => $idx, // id tidak penting di sini, hanya untuk keperluan dropdown
                'nama' => $nama,
            ];
        })->toArray();
    }

    public function updatedSelectedKabupaten()
    {
        $this->selectedStockist = ''; // Reset selected stockist when kabupaten changes
        $this->loadStockis();

    }

    public function selectKabupaten($kabupatenName)
    {
        $this->selectedKabupaten = $kabupatenName;
        $this->loadStockis();

    }

    public function loadStockis()
    {
        $query = User::where('isStockis', true);

        // Filter by kabupaten if selected
        if (!empty($this->selectedKabupaten)) {
            $query->where('kabupaten', $this->selectedKabupaten);
        }

        $this->stockistList = $query->get();
    }

    public function updatedSelectedStockist()
    {
        // Kosongkan keranjang setiap ganti stockist
        $this->cart = [];
        Session::forget('cart');
        $this->updateTotals();
        $this->loadProduks();
    }

    public function updatedSearch()
    {
        $this->searchProduk();
    }

    public function filterByPaket($filter)
    {
        $this->activeFilter = $filter;
        $this->loadProduks();
    }

    public function searchProduk()
    {
        $this->loadProduks();
    }

    private function loadProduks()
    {
        // Jika tidak ada stockist yang dipilih, tampilkan semua produk tanpa stok
        if (empty($this->selectedStockist)) {
            $query = Produk::query();

            // Filter by package type
            if ($this->activeFilter === 'aktivasi') {
                $query->where('paket', 1);
            } elseif ($this->activeFilter === 'quick_reward') {
                $query->where('paket', 2);
            }

            if (!empty($this->search)) {
                $searchTerm = trim($this->search);

                $query->where(function ($q) use ($searchTerm) {
                    $q->where('nama', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('paket', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('deskripsi', 'LIKE', "%{$searchTerm}%")
                        ->orWhere('id', 'LIKE', "%{$searchTerm}%");
                });
            }

            $this->produks = $query->get();
            $this->filteredProduks = $this->produks;
            return;
        }

        // Jika ada stockist yang dipilih, tampilkan produk dengan stok
        $query = Produk::join('produk_stoks', 'produks.id', '=', 'produk_stoks.produk_id')
            ->where('produk_stoks.user_id', $this->selectedStockist)
            ->where('produk_stoks.stok', '>', 0) // Hanya produk dengan stok > 0
            ->select('produks.*', 'produk_stoks.stok as stok_tersedia');

        // Filter by package type
        if ($this->activeFilter === 'aktivasi') {
            $query->where('produks.paket', 1);
        } elseif ($this->activeFilter === 'quick_reward') {
            $query->where('produks.paket', 2);
        }

        if (!empty($this->search)) {
            $searchTerm = trim($this->search);

            $query->where(function ($q) use ($searchTerm) {
                $q->where('produks.nama', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('produks.paket', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('produks.deskripsi', 'LIKE', "%{$searchTerm}%")
                    ->orWhere('produks.id', 'LIKE', "%{$searchTerm}%");
            });
        }

        $this->produks = $query->get();
        $this->filteredProduks = $this->produks;

        // Kurangi stok_tersedia dengan qty di cart jika stockist dipilih
        if (!empty($this->selectedStockist)) {
            $cart = Session::get('cart', []);
            foreach ($this->produks as $produk) {
                $qtyInCart = isset($cart[$produk->id]) ? $cart[$produk->id]['qty'] : 0;
                // Pastikan stok_tersedia tidak minus
                $produk->stok_tersedia = max(0, $produk->stok_tersedia - $qtyInCart);
            }
        }
    }

    public function addToCart($produkId)
    {
        try {
            // Validasi apakah kabupaten sudah dipilih
            if (empty($this->selectedKabupaten)) {
                session()->flash('error', 'Silakan pilih Kabupaten terlebih dahulu');
                return;
            }

            // Validasi apakah stockist sudah dipilih
            if (empty($this->selectedStockist)) {
                session()->flash('error', 'Silakan pilih Stockist terlebih dahulu');
                return;
            }

            // Validasi stok tersedia
            $produkStok = ProdukStok::where('user_id', $this->selectedStockist)
                ->where('produk_id', $produkId)
                ->first();

            if (!$produkStok || $produkStok->stok <= 0) {
                session()->flash('error', 'Stok produk tidak tersedia');
                return;
            }

            $produk = Produk::find($produkId);
            if (!$produk) {
                session()->flash('error', 'Produk tidak ditemukan');
                return;
            }

            $cart = Session::get('cart', []);

            // Cek apakah sudah ada di cart dan validasi stok
            $currentQty = isset($cart[$produkId]) ? $cart[$produkId]['qty'] : 0;
            if ($currentQty >= $produkStok->stok) {
                session()->flash('error', 'Stok tidak mencukupi untuk menambahkan produk ini');
                return;
            }

            if (isset($cart[$produkId])) {
                $cart[$produkId]['qty'] += 1;
            } else {
                $cart[$produkId] = [
                    'id' => $produk->id,
                    'nama' => $produk->nama,
                    'harga' => $produk->harga_member,
                    'qty' => 1,
                    'gambar' => $produk->gambar,
                    'stockist_id' => $this->selectedStockist,
                ];
            }

            Session::put('cart', $cart);
            $this->cart = $cart;
            $this->updateTotals();
            $this->loadProduks(); // Tambahkan ini

        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menambahkan produk');
        }
    }

    public function increment($produkId)
    {
        $cart = Session::get('cart', []);

        // Validasi stok sebelum increment
        $produkStok = ProdukStok::where('user_id', $this->selectedStockist)
            ->where('produk_id', $produkId)
            ->first();

        if (!$produkStok) {
            session()->flash('error', 'Stok produk tidak tersedia');
            return;
        }

        // Cari item berdasarkan id produk, bukan key array
        foreach ($cart as $key => $item) {
            if ($item['id'] == $produkId) {
                // Cek apakah increment masih dalam batas stok
                if ($item['qty'] >= $produkStok->stok) {
                    session()->flash('error', 'Stok tidak mencukupi');
                    return;
                }

                $cart[$key]['qty'] += 1;
                Session::put('cart', $cart);
                $this->cart = $cart;
                $this->updateTotals();
                $this->loadProduks(); // Tambahkan ini
                return;
            }
        }
    }

    public function decrement($produkId)
    {
        $cart = Session::get('cart', []);

        // Cari item berdasarkan id produk, bukan key array
        foreach ($cart as $key => $item) {
            if ($item['id'] == $produkId && $item['qty'] > 1) {
                $cart[$key]['qty'] -= 1;
                Session::put('cart', $cart);
                $this->cart = $cart;
                $this->updateTotals();
                $this->loadProduks(); // Tambahkan ini
                return;
            }
        }
    }

    public function remove($produkId)
    {
        $cart = Session::get('cart', []);

        // Cari item berdasarkan id produk, bukan key array
        foreach ($cart as $key => $item) {
            if ($item['id'] == $produkId) {
                unset($cart[$key]);
                Session::put('cart', $cart);
                $this->cart = $cart;
                $this->updateTotals();
                $this->loadProduks(); // Tambahkan ini
                return;
            }
        }
    }

    private function updateTotals()
    {
        $this->totalQty = collect($this->cart)->sum('qty');
        $this->totalPrice = collect($this->cart)->sum(function ($item) {
            return $item['qty'] * $item['harga'];
        });
    }

    public function changePage($page)
    {
        $this->currentPage = $page;
        $this->showCartSidebar = false; // Tutup sidebar setelah pindah halaman
    }

    public function aktivasiMember()
    {
        // Validasi kabupaten dipilih
        if (empty($this->selectedKabupaten)) {
            session()->flash('error', 'Silakan pilih Kabupaten terlebih dahulu');
            return;
        }

        // Validasi stockist dipilih
        if (empty($this->selectedStockist)) {
            session()->flash('error', 'Silakan pilih Stockist terlebih dahulu');
            return;
        }

        // Validate form data
        $this->validate([
            'nama' => 'required|string|max:255',
            'nama_bank' => 'required|string|max:255',
            'no_rekening' => 'required|string|max:50',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
        ], [
            'nama.required' => 'Nama pemesan harus diisi',
            'nama_bank.required' => 'Nama bank harus diisi',
            'no_rekening.required' => 'Nomor rekening harus diisi',
            'telepon.required' => 'Nomor telepon harus diisi',
            'alamat.required' => 'Alamat pengiriman harus diisi',
        ]);

        DB::beginTransaction();
        try {
            // Buat user baru
            $userBaru = User::create([
                'nama' => $this->nama,
                'isStockis' => false,
                'nama_rekening' => $this->nama,
                'no_rek' => $this->no_rekening,
                'bank' => $this->nama_bank,
                'no_telp' => $this->telepon,
                'alamat' => $this->alamat,
                'provinsi' => null, // bisa diisi dari form jika ada
                'kabupaten' => $this->selectedKabupaten,
                'email' => null, // bisa diisi dari form jika ada
                'username' => null, // bisa diisi dari form jika ada
                'password' => bcrypt('password'),
                'id_sponsor' => Auth::id(), // ID sponsor diisi dari user yang login
            ]);

            // Tambahan logika MLM: status_qr, id_sponsor, group_sponsor
            // Cek apakah ada produk dengan paket == 2 di cart
            $cart = Session::get('cart', []);
            $adaPaketQR = false;
            foreach ($cart as $item) {
                $produk = \App\Models\Produk::find($item['id']);
                if ($produk && $produk->paket == 2) {
                    $adaPaketQR = true;
                    break;
                }
            }

            // Siapkan group_sponsor
            $sponsor = Auth::user();
            $groupSponsor = [];
            if ($sponsor) {
                // Ambil group_sponsor sponsor jika ada, lalu tambahkan id sponsor
                if (is_array($sponsor->group_sponsor)) {
                    $groupSponsor = $sponsor->group_sponsor;
                } elseif (!empty($sponsor->group_sponsor)) {
                    // Jika group_sponsor disimpan sebagai string JSON
                    $groupSponsor = json_decode($sponsor->group_sponsor, true) ?? [];
                }
                $groupSponsor[] = $sponsor->id;
            }

            // Update userBaru
            $userBaru->status_qr = $adaPaketQR;
            $userBaru->id_sponsor = $sponsor ? $sponsor->id : null;
            $userBaru->group_sponsor = $groupSponsor;
            $userBaru->save();

            // Set tanggal ke hari ini
            $this->tanggal = now()->format('Y-m-d');

            // Siapkan data user baru untuk checkout
            $userData = [
                'nama' => $this->nama,
                'telepon' => $this->telepon,
                'alamat' => $this->alamat,
                'tanggal' => $this->tanggal,
                'nama_bank' => $this->nama_bank,
                'no_rekening' => $this->no_rekening,
                'nama_rekening' => $this->nama,
                'user_id' => $userBaru->id,
                'username' => $userBaru->username,
                'email' => $userBaru->email,
                'provinsi' => $userBaru->provinsi,
                'kabupaten' => $userBaru->kabupaten,
                'no_telp' => $userBaru->no_telp,
                'alamat_user' => $userBaru->alamat,
            ];

            $this->processCheckout($userData, 'aktivasi member');

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Aktivasi member gagal: ' . $e->getMessage());
        }
    }

    public function stockPribadi()
    {
        // Validasi kabupaten dipilih
        if (empty($this->selectedKabupaten)) {
            session()->flash('error', 'Silakan pilih Kabupaten terlebih dahulu');
            return;
        }

        // Validasi stockist dipilih
        if (empty($this->selectedStockist)) {
            session()->flash('error', 'Silakan pilih Stockist terlebih dahulu');
            return;
        }

        $user = Auth::user();

        // Set tanggal to current date if empty
        $tanggal = !empty($this->tanggal) ? $this->tanggal : now()->format('Y-m-d');

        // Siapkan data user untuk dikirim ke processCheckout
        $userData = [
            'nama' => $this->nama,
            'telepon' => $this->telepon,
            'alamat' => $this->alamat,
            'tanggal' => $tanggal,
            'nama_bank' => $user->bank ?? '',
            'no_rekening' => $user->no_rek ?? '',
            'nama_rekening' => $user->nama_rekening ?? $user->nama ?? '',
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'provinsi' => $user->provinsi ?? '',
            'kabupaten' => $user->kabupaten ?? '',
            'no_telp' => $user->no_telp ?? '',
            'alamat_user' => $user->alamat ?? '',
        ];

        $this->processCheckout($userData, 'stock pribadi');
    }

    private function processCheckout($userData = null, $kategoriPembelian = 'stock pribadi')
    {
        DB::beginTransaction();
        try {
            // Validasi kabupaten dipilih
            if (empty($this->selectedKabupaten)) {
                session()->flash('error', 'Silakan pilih Kabupaten terlebih dahulu');
                return;
            }

            $cart = Session::get('cart', []);
            if (empty($cart)) {
                session()->flash('error', 'Keranjang kosong!');
                return;
            }

            // Validasi stok untuk semua item di cart
            foreach ($cart as $item) {
                $produkStok = ProdukStok::where('user_id', $this->selectedStockist)
                    ->where('produk_id', $item['id'])
                    ->first();

                if (!$produkStok || $produkStok->stok < $item['qty']) {
                    session()->flash('error', 'Stok produk ' . $item['nama'] . ' tidak mencukupi');
                    return;
                }
            }

            // Gunakan data user jika tersedia, jika tidak gunakan data dari form
            $namaPenerima = $userData['nama'] ?? $this->namaPenerima ?? $this->nama;
            $noTelp = $userData['telepon'] ?? $this->telepon;
            $alamatTujuan = $userData['alamat'] ?? $this->alamat;
            $tanggalBeli = $userData['tanggal'] ?? (!empty($this->tanggal) ? $this->tanggal : now()->format('Y-m-d'));
            $userId = $userData['user_id'] ?? Auth::id();

            // Jika data form kosong, gunakan data dari user yang login
            if (empty($namaPenerima)) {
                $namaPenerima = $userData['nama_rekening'] ?? $userData['no_telp'] ?? Auth::user()->nama ?? '';
            }
            if (empty($noTelp)) {
                $noTelp = $userData['no_telp'] ?? Auth::user()->no_telp ?? '';
            }
            if (empty($alamatTujuan)) {
                $alamatTujuan = $userData['alamat_user'] ?? Auth::user()->alamat ?? '';
            }

            // Validasi data sebelum insert
            if (empty($namaPenerima) || empty($noTelp) || empty($alamatTujuan)) {
                session()->flash('error', 'Data penerima tidak lengkap. Silakan lengkapi data profil Anda terlebih dahulu.');
                return;
            }

            $pembelian = Pembelian::create([
                'tgl_beli' => $tanggalBeli,
                'user_id' => $userId,
                'beli_dari' => $this->selectedStockist,
                'tujuan_beli' => 'null',
                'nama_penerima' => $namaPenerima,
                'no_telp' => $noTelp,
                'alamat_tujuan' => $alamatTujuan,
                'total_beli' => collect($cart)->sum(function ($item) {
                    return $item['qty'] * $item['harga'];
                }),
                'total_bonus' => 0,
                'status_pembelian' => 'menunggu',
                'jumlah_poin_qr' => 0,
                'kategori_pembelian' => $kategoriPembelian,
            ]);

            // Simpan detail produk (STOK STOCKIST AKAN DIKURANGI SAAT ADMIN APPROVE)
            foreach ($cart as $item) {
                $produk = Produk::find($item['id']);
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'produk_id' => $item['id'],
                    'nama_produk' => $item['nama'],
                    'paket' => $produk ? $produk->paket : '',
                    'jml_beli' => $item['qty'],
                    'harga_beli' => $item['harga'],
                    'nominal_bonus_sponsor' => 0,
                    'nominal_bonus_generasi' => 0,
                    'user_id_get_bonus_sponsor' => null,
                    'group_user_id_get_bonus_generasi' => null,
                ]);

                // STOK STOCKIST AKAN DIKURANGI SAAT ADMIN APPROVE DI HALAMAN APPROVAL
                // Tidak perlu mengurangi stok stockist di sini
            }

            // Kosongkan cart dan form
            Session::forget('cart');
            $this->cart = [];
            $this->nama = '';
            $this->namaPenerima = '';
            $this->telepon = '';
            $this->alamat = '';
            $this->tanggal = '';
            $this->nama_bank = '';
            $this->no_rekening = '';
            $this->updateTotals();
            $this->showCartSidebar = false;

            $this->loadProduks();

            DB::commit();

            session()->flash('success', 'Pembelian berhasil! Menunggu approval dari admin.');
            return redirect()->route('filament.user.resources.pembelians.detail', ['record' => $pembelian->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }

    public function toggleCartSidebar()
    {
        $this->showCartSidebar = !$this->showCartSidebar;
    }

    public function repeatOrder()
    {
        // Validasi kabupaten dipilih
        if (empty($this->selectedKabupaten)) {
            session()->flash('error', 'Silakan pilih Kabupaten terlebih dahulu');
            return;
        }

        // Validasi stockist dipilih
        if (empty($this->selectedStockist)) {
            session()->flash('error', 'Silakan pilih Stockist terlebih dahulu');
            return;
        }

        // Validasi cart tidak kosong
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong! Silakan pilih produk terlebih dahulu.');
            return;
        }

        // Validate form data
        $this->validate([
            'namaPenerima' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
        ], [
            'namaPenerima.required' => 'Nama penerima harus diisi',
            'telepon.required' => 'Nomor telepon harus diisi',
            'alamat.required' => 'Alamat pengiriman harus diisi',
        ]);

        $user = Auth::user();

        // Set tanggal to current date
        $tanggal = now()->format('Y-m-d');

        // Siapkan data untuk dikirim ke processCheckout
        $userData = [
            'nama' => $this->namaPenerima, // Gunakan namaPenerima dari form
            'telepon' => $this->telepon,
            'alamat' => $this->alamat,
            'tanggal' => $tanggal,
            'nama_bank' => $user->bank ?? '',
            'no_rekening' => $user->no_rek ?? '',
            'nama_rekening' => $user->nama_rekening ?? $user->nama ?? '',
            'user_id' => $user->id,
            'username' => $user->username,
            'email' => $user->email,
            'provinsi' => $user->provinsi ?? '',
            'kabupaten' => $user->kabupaten ?? '',
            'no_telp' => $user->no_telp ?? '',
            'alamat_user' => $user->alamat ?? '',
        ];

        $this->processCheckout($userData, 'repeat order');
    }

    public function render()
    {
        return view('livewire.user.pembelian-produk-mitra');
    }
}
