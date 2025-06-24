<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produks = [
            [
                'paket' => 'Paket Basic',
                'nama' => 'Paket Starter',
                'harga_stokis' => 1000000,
                'harga_member' => 1200000,
                'gambar' => 'paket-basic.jpg',
                'deskripsi' => 'Paket starter untuk member baru',
                'status_aktif' => 'aktif',
            ],
            [
                'paket' => 'Paket Premium',
                'nama' => 'Paket Premium',
                'harga_stokis' => 2000000,
                'harga_member' => 2500000,
                'gambar' => 'paket-premium.jpg',
                'deskripsi' => 'Paket premium dengan fitur lengkap',
                'status_aktif' => 'aktif',
            ],
            [
                'paket' => 'Paket VIP',
                'nama' => 'Paket VIP',
                'harga_stokis' => 5000000,
                'harga_member' => 6000000,
                'gambar' => 'paket-vip.jpg',
                'deskripsi' => 'Paket VIP dengan semua keuntungan',
                'status_aktif' => 'aktif',
            ],
        ];

        foreach ($produks as $produk) {
            Produk::create($produk);
        }
    }
}
