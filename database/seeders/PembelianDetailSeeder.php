<?php

namespace Database\Seeders;

use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Produk;
use Illuminate\Database\Seeder;

class PembelianDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all purchases
        $pembelians = Pembelian::all();
        $produks = Produk::all();

        if ($produks->isEmpty()) {
            // Create some sample products if none exist
            $this->createSampleProducts();
            $produks = Produk::all();
        }

        foreach ($pembelians as $pembelian) {
            $this->createPurchaseDetails($pembelian, $produks);
        }
    }

    private function createSampleProducts()
    {
        $sampleProducts = [
            [
                'paket' => 1, // Aktivasi
                'nama' => 'Paket Aktivasi Basic',
                'harga_stokis' => 500000,
                'harga_member' => 600000,
                'deskripsi' => 'Paket aktivasi member baru',
                'status_aktif' => true,
            ],
            [
                'paket' => 1, // Aktivasi
                'nama' => 'Paket Aktivasi Premium',
                'harga_stokis' => 800000,
                'harga_member' => 1000000,
                'deskripsi' => 'Paket aktivasi member premium',
                'status_aktif' => true,
            ],
            [
                'paket' => 2, // Quick Reward
                'nama' => 'Produk Quick Reward A',
                'harga_stokis' => 150000,
                'harga_member' => 200000,
                'deskripsi' => 'Produk quick reward kategori A',
                'status_aktif' => true,
            ],
            [
                'paket' => 2, // Quick Reward
                'nama' => 'Produk Quick Reward B',
                'harga_stokis' => 250000,
                'harga_member' => 300000,
                'deskripsi' => 'Produk quick reward kategori B',
                'status_aktif' => true,
            ],
            [
                'paket' => 2, // Quick Reward
                'nama' => 'Produk Quick Reward C',
                'harga_stokis' => 350000,
                'harga_member' => 400000,
                'deskripsi' => 'Produk quick reward kategori C',
                'status_aktif' => true,
            ],
        ];

        foreach ($sampleProducts as $product) {
            Produk::create($product);
        }
    }

    private function createPurchaseDetails($pembelian, $produks)
    {
        // Determine how many products to add to this purchase (1-3 products)
        $productCount = rand(1, 3);
        $selectedProducts = $produks->random($productCount);

        $totalBeli = 0;

        foreach ($selectedProducts as $produk) {
            // Random quantity between 1-5
            $quantity = rand(1, 5);

            // Use member price for calculation
            $hargaBeli = $produk->harga_member;
            $subtotal = $quantity * $hargaBeli;
            $totalBeli += $subtotal;

            PembelianDetail::create([
                'pembelian_id' => $pembelian->id,
                'produk_id' => $produk->id,
                'nama_produk' => $produk->nama,
                'paket' => $produk->paket,
                'jml_beli' => $quantity,
                'harga_beli' => $hargaBeli,
                'nominal_bonus_sponsor' => 0, // Will be calculated later
                'nominal_bonus_generasi' => 0, // Will be calculated later
                'user_id_get_bonus_sponsor' => null, // Will be set later
                'group_user_id_get_bonus_generasi' => null, // Will be set later
            ]);
        }

        // Update the total_beli in pembelian table to match the actual total
        $pembelian->update([
            'total_beli' => $totalBeli,
        ]);
    }
}
