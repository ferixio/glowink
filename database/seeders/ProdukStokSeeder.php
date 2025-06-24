<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\ProdukStok;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProdukStokSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all products and users
        $produks = Produk::all();
        $users = User::all();

        // Create stock entries for each product and user combination
        foreach ($produks as $produk) {
            foreach ($users as $user) {
                ProdukStok::create([
                    'produk_id' => $produk->id,
                    'user_id' => $user->id,
                    'stok' => rand(0, 100), // Random stock between 0 and 100
                ]);
            }
        }
    }
}
