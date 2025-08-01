<?php

namespace App\Livewire\User;

use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class PenjualanProdukMitra extends Component
{
    public $cart = [];
    public $totalQty = 0;
    public $totalPrice = 0;
    public $produks = [];
    public $stokData = [];
    public $search = '';
    public $activeFilter = 'all';
    public $showCartSidebar = false;

    public $nama = '';
    public $telepon = '';
    public $alamat = '';
    public $tanggal = '';

    public function mount()
    {
        $this->cart = Session::get('cart_mitra', []);
        $this->updateTotals();
        $this->loadProduks();
    }

    public function updatedSearch()
    {
        $this->loadProduks();
    }

    public function filterByPaket($filter)
    {
        $this->activeFilter = $filter;
        $this->loadProduks();
    }

    private function loadProduks()
    {
        $userId = Auth::id();
        $query = Produk::query();
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
        $produks = $query->get();
        // Ambil stok untuk setiap produk berdasarkan user login
        $this->stokData = [];
        foreach ($produks as $produk) {
            $stokDb = $produk->produkStoks()->where('user_id', $userId)->value('stok') ?? 0;
            $qtyInCart = isset($this->cart[$produk->id]) ? $this->cart[$produk->id]['qty'] : 0;
            $this->stokData[$produk->id] = max(0, $stokDb - $qtyInCart);
        }
        $this->produks = $produks;
    }

    public function addToCart($produkId)
    {
        $produk = Produk::find($produkId);
        if (!$produk) {
            session()->flash('error', 'Produk tidak ditemukan');
            return;
        }
        $cart = Session::get('cart_mitra', []);
        $userId = Auth::id();
        $produkStok = $produk->produkStoks()->where('user_id', $userId)->first();
        $stokTersedia = $produkStok ? $produkStok->stok : 0;
        $currentQty = isset($cart[$produkId]) ? $cart[$produkId]['qty'] : 0;
        if ($currentQty >= $stokTersedia) {
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
            ];
        }
        Session::put('cart_mitra', $cart);
        $this->cart = $cart;
        $this->updateTotals();
        $this->loadProduks();
    }

    public function increment($produkId)
    {
        $cart = Session::get('cart_mitra', []);
        if (isset($cart[$produkId])) {
            $produk = Produk::find($produkId);
            $userId = Auth::id();
            $produkStok = $produk->produkStoks()->where('user_id', $userId)->first();
            $stokTersedia = $produkStok ? $produkStok->stok : 0;
            if ($cart[$produkId]['qty'] >= $stokTersedia) {
                session()->flash('error', 'Stok tidak mencukupi');
                return;
            }
            $cart[$produkId]['qty'] += 1;
            Session::put('cart_mitra', $cart);
            $this->cart = $cart;
            $this->updateTotals();
            $this->loadProduks();
        }
    }

    public function decrement($produkId)
    {
        $cart = Session::get('cart_mitra', []);
        if (isset($cart[$produkId]) && $cart[$produkId]['qty'] > 1) {
            $cart[$produkId]['qty'] -= 1;
            Session::put('cart_mitra', $cart);
            $this->cart = $cart;
            $this->updateTotals();
            $this->loadProduks();
        }
    }

    public function remove($produkId)
    {
        $cart = Session::get('cart_mitra', []);
        if (isset($cart[$produkId])) {
            unset($cart[$produkId]);
            Session::put('cart_mitra', $cart);
            $this->cart = $cart;
            $this->updateTotals();
            $this->loadProduks();
        }
    }

    private function updateTotals()
    {
        $this->totalQty = collect($this->cart)->sum('qty');
        $this->totalPrice = collect($this->cart)->sum(function ($item) {
            return $item['qty'] * $item['harga'];
        });
    }

    public function toggleCartSidebar()
    {
        $this->showCartSidebar = !$this->showCartSidebar;
    }

    public function checkout()
    {
        if (empty($this->cart)) {
            session()->flash('error', 'Keranjang kosong!');
            return;
        }

        // Validasi data form
        $this->validate([
            'nama' => 'required|string|max:255',
            'telepon' => 'required|string|max:20',
            'alamat' => 'required|string|max:500',
            'tanggal' => 'required|date',
        ]);

        $userId = Auth::id();
        $successCount = 0;
        $errorMessages = [];

        // Proses pengurangan stok untuk setiap produk di keranjang
        foreach ($this->cart as $produkId => $item) {
            $produk = Produk::find($produkId);
            if (!$produk) {
                $errorMessages[] = "Produk {$item['nama']} tidak ditemukan";
                continue;
            }

            $produkStok = $produk->produkStoks()->where('user_id', $userId)->first();

            if (!$produkStok) {
                $errorMessages[] = "Stok produk {$item['nama']} tidak tersedia";
                continue;
            }

            if ($produkStok->stok < $item['qty']) {
                $errorMessages[] = "Stok produk {$item['nama']} tidak mencukupi (tersedia: {$produkStok->stok}, dibutuhkan: {$item['qty']})";
                continue;
            }

            // Kurangi stok
            $produkStok->stok -= $item['qty'];
            $produkStok->save();
            $successCount++;
        }

        // Berikan notifikasi berdasarkan hasil
        if ($successCount > 0) {
            if (count($errorMessages) > 0) {
                // Ada yang berhasil dan ada yang gagal
                session()->flash('success', "Berhasil memproses {$successCount} produk. " . implode(', ', $errorMessages));
            } else {
                // Semua berhasil
                session()->flash('success', "Penjualan berhasil diproses! {$successCount} produk telah dikurangi dari stok.");
            }
        } else {
            // Semua gagal
            session()->flash('error', 'Gagal memproses penjualan: ' . implode(', ', $errorMessages));
            return;
        }

        // Reset keranjang hanya jika ada yang berhasil
        if ($successCount > 0) {
            Session::forget('cart_mitra');
            $this->cart = [];
            $this->updateTotals();
            $this->loadProduks(); // Reload produk untuk update stok
        }
    }

    public function render()
    {
        return view('livewire.user.penjualan-produk-mitra');
    }
}
