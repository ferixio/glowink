<?php

namespace Database\Seeders;

use App\Models\Produk;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProdukSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $produkList = [
            [
                'paket' => 1,
                'nama' => 'A. BARILKHA COFFEE (BOX)',
                'harga_stokis' => 32000,
                'harga_member' => 35000,
                'deskripsi' => 'Paket starter untuk member baru',
            ],
            [
                'paket' => 2,
                'nama' => 'QR. BARILKHA COFFEE (Pouch) 300g',
                'harga_stokis' => 140000,
                'harga_member' => 150000,
                'deskripsi' => 'Produk quick reward varian pouch',
            ],
            [
                'paket' => 2,
                'nama' => 'QR. VIT 1',
                'harga_stokis' => 140000,
                'harga_member' => 150000,
                'deskripsi' => 'Produk quick reward VIT 1',
            ],
            [
                'paket' => 2,
                'nama' => 'QR. Teh Kawa BARILKHA',
                'harga_stokis' => 140000,
                'harga_member' => 150000,
                'deskripsi' => 'Teh Kawa varian quick reward',
            ],
            [
                'paket' => 2,
                'nama' => 'QR. BARILKHA EtawaKu',
                'harga_stokis' => 140000,
                'harga_member' => 150000,
                'deskripsi' => 'Susu Etawa BARILKHA quick reward',
            ],
            [
                'paket' => 2,
                'nama' => 'QR. BARILKHA Green COFFEE',
                'harga_stokis' => 140000,
                'harga_member' => 150000,
                'deskripsi' => 'Green coffee BARILKHA quick reward',
            ],
        ];

        foreach ($produkList as $index => $produk) {
            $originalImage = ($index + 1) . '.webp'; // 1.webp sampai 6.webp
            $newFileName = Str::random(10) . '_' . $originalImage;

            // Salin gambar ke storage publik
            Storage::disk('public')->put(
                'produk/' . $newFileName,
                file_get_contents(database_path('seeders/images/' . $originalImage))
            );

            // Simpan produk ke database
            Produk::create([
                'paket' => $produk['paket'],
                'nama' => $produk['nama'],
                'harga_stokis' => $produk['harga_stokis'],
                'harga_member' => $produk['harga_member'],
                'gambar' => 'produk/' . $newFileName,
                'deskripsi' => $produk['deskripsi'],
                'status_aktif' => 'aktif',
            ]);
        }
    }
}
