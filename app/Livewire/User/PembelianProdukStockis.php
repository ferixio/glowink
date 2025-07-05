<?php

namespace App\Livewire\User;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PembelianProdukStockis extends Component
{
    public $cart = [];
    public $totalQty = 0;
    public $totalPrice = 0;
    public $produks = [];
    public $search = '';
    public $filteredProduks = [];
    public $activeFilter = 'all';

    // Form properties
    public $nama = '';
    public $telepon = '';
    public $alamat = '';
    public $tanggal = '';

    public function mount()
    {
        $this->cart = Session::get('cart', []);
        $this->loadProduks();
        $this->updateTotals();
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
    }

    public function addToCart($produkId)
    {
        try {
            Log::info('Adding to cart: ' . $produkId);

            $produk = Produk::find($produkId);
            if (!$produk) {
                Log::error('Product not found: ' . $produkId);
                session()->flash('error', 'Produk tidak ditemukan');
                return;
            }

            $cart = Session::get('cart', []);

            if (isset($cart[$produkId])) {
                $cart[$produkId]['qty'] += 1;
            } else {
                $cart[$produkId] = [
                    'id' => $produk->id,
                    'nama' => $produk->nama,
                    'harga' => $produk->harga_member,
                    'qty' => 1,
                    'gambar' => $produk->gambar,
                ];
            }

            Session::put('cart', $cart);
            $this->cart = $cart;
            $this->updateTotals();

            Log::info('Cart updated successfully');
            // session()->flash('success', 'Produk berhasil ditambahkan ke keranjang');
        } catch (\Exception $e) {
            Log::error('Error in addToCart: ' . $e->getMessage());
            session()->flash('error', 'Terjadi kesalahan saat menambahkan produk');
        }
    }

    public function increment($produkId)
    {
        Log::info('Increment called for product: ' . $produkId);
        $cart = Session::get('cart', []);

        // Cari item berdasarkan id produk, bukan key array
        foreach ($cart as $key => $item) {
            if ($item['id'] == $produkId) {
                $cart[$key]['qty'] += 1;
                Session::put('cart', $cart);
                $this->cart = $cart;
                $this->updateTotals();
                Log::info('Increment successful. New qty: ' . $cart[$key]['qty']);
                return;
            }
        }
        Log::error('Product not found in cart for increment: ' . $produkId);
    }

    public function decrement($produkId)
    {
        Log::info('Decrement called for product: ' . $produkId);
        $cart = Session::get('cart', []);

        // Cari item berdasarkan id produk, bukan key array
        foreach ($cart as $key => $item) {
            if ($item['id'] == $produkId && $item['qty'] > 1) {
                $cart[$key]['qty'] -= 1;
                Session::put('cart', $cart);
                $this->cart = $cart;
                $this->updateTotals();
                Log::info('Decrement successful. New qty: ' . $cart[$key]['qty']);
                return;
            }
        }
        Log::error('Product not found in cart or qty <= 1 for decrement: ' . $produkId);
    }

    public function remove($produkId)
    {
        Log::info('Remove called for product: ' . $produkId);
        $cart = Session::get('cart', []);

        // Cari item berdasarkan id produk, bukan key array
        foreach ($cart as $key => $item) {
            if ($item['id'] == $produkId) {
                unset($cart[$key]);
                Session::put('cart', $cart);
                $this->cart = $cart;
                $this->updateTotals();
                Log::info('Remove successful');
                return;
            }
        }
        Log::error('Product not found in cart for remove: ' . $produkId);
    }

    private function updateTotals()
    {
        $this->totalQty = collect($this->cart)->sum('qty');
        $this->totalPrice = collect($this->cart)->sum(function ($item) {
            return $item['qty'] * $item['harga'];
        });
    }

    public function checkout()
    {
        // Validate form data
        $this->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'tanggal' => 'required|date',
        ], [
            'nama.required' => 'Nama pemesan harus diisi',
            'telepon.required' => 'Nomor telepon harus diisi',
            'alamat.required' => 'Alamat pengiriman harus diisi',
            'tanggal.required' => 'Tanggal transaksi harus diisi',
        ]);

        DB::beginTransaction();
        try {
            $cart = Session::get('cart', []);
            if (empty($cart)) {
                session()->flash('error', 'Keranjang kosong!');
                return;
            }

            $pembelian = Pembelian::create([
                'tgl_beli' => $this->tanggal,
                'user_id' => Auth::id(),
                'beli_dari' => 1, // isi jika ada logic stokis/mitra
                'tujuan_beli' => 'null', // isi jika ada
                'nama_penerima' => $this->nama,
                'no_telp' => $this->telepon,
                'alamat_tujuan' => $this->alamat,
                'total_beli' => collect($cart)->sum(function ($item) {
                    return $item['qty'] * $item['harga'];
                }),
                'total_bonus' => 0, // isi sesuai logic bonus
                'status_pembelian' => 'pending',
                'jumlah_poin_qr' => 0, // isi jika ada logic poin
            ]);

            // 2. Simpan detail produk ke pembelian_details
            foreach ($cart as $item) {
                // Ambil data produk untuk field paket
                $produk = Produk::find($item['id']);
                PembelianDetail::create([
                    'pembelian_id' => $pembelian->id,
                    'produk_id' => $item['id'],
                    'nama_produk' => $item['nama'],
                    'paket' => $produk ? $produk->paket : '',
                    'jml_beli' => $item['qty'],
                    'harga_beli' => $item['harga'],
                    'nominal_bonus_sponsor' => 0, // isi sesuai logic bonus
                    'nominal_bonus_generasi' => 0, // isi sesuai logic bonus
                    'user_id_get_bonus_sponsor' => null, // isi jika ada
                    'group_user_id_get_bonus_generasi' => null, // isi jika ada
                ]);
            }

            // 3. Kosongkan cart dan form
            Session::forget('cart');
            $this->cart = [];
            $this->nama = '';
            $this->telepon = '';
            $this->alamat = '';
            $this->tanggal = '';
            $this->updateTotals();

            DB::commit();

            return redirect()->route('filament.user.resources.pembelians.detail', ['record' => $pembelian->id]);

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'Checkout gagal: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.user.pembelian-produk-stockis');
    }
}
