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
                'paket' => 1,
                'nama' => 'Barilkha Coffe',
                'harga_stokis' => 10000,
                'harga_member' => 12000,
                'gambar' => null,
                'deskripsi' => 'Paket starter untuk member baru',
                'status_aktif' => 'aktif',
            ],
            [
                'paket' => 1,
                'nama' => 'Barilkha Coffe',
                'harga_stokis' => 20000,
                'harga_member' => 25000,
                'gambar' => null,
                'deskripsi' => 'Paket premium dengan fitur lengkap',
                'status_aktif' => 'aktif',
            ],
            [
                'paket' => 2,
                'nama' => 'Barilkha Queen',
                'harga_stokis' => 50000,
                'harga_member' => 60000,
                'gambar' => null,
                'deskripsi' => 'Paket VIP dengan semua keuntungan',
                'status_aktif' => 'aktif',
            ],
        ];

        foreach ($produks as $produk) {
            Produk::create($produk);
        }
    }
}
