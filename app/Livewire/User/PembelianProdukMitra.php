<?php

namespace App\Livewire\User;

use App\Models\Produk;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PembelianProdukMitra extends Component
{

    public $cart = [];
    public $stockistList = [];
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
        $this->loadStockis();
    }

    public function loadStockis()
    {
        $this->stockistList = User::where('isStockis', true)->get();
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

            $produk = Produk::find($produkId);
            if (!$produk) {

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

            // session()->flash('success', 'Produk berhasil ditambahkan ke keranjang');
        } catch (\Exception $e) {
            session()->flash('error', 'Terjadi kesalahan saat menambahkan produk');
        }
    }

    public function increment($produkId)
    {
        $cart = Session::get('cart', []);

        // Cari item berdasarkan id produk, bukan key array
        foreach ($cart as $key => $item) {
            if ($item['id'] == $produkId) {
                $cart[$key]['qty'] += 1;
                Session::put('cart', $cart);
                $this->cart = $cart;
                $this->updateTotals();
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

    public function render()
    {
        return view('livewire.user.pembelian-produk-mitra');
    }
}
