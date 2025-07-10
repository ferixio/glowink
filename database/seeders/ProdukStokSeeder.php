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
        // Get all products
        $produks = Produk::all();

        // Get non-admin users (stockists and basic members)
        $users = User::where('isAdmin', false)->get();

        if ($users->isEmpty()) {
            $this->command->info('No non-admin users found. Skipping ProdukStokSeeder.');
            return;
        }

        // Create stock entries for each product and non-admin user combination
        foreach ($produks as $produk) {
            foreach ($users as $user) {
                // Determine stock quantity based on user type
                $stockQuantity = $this->getStockQuantity($user, $produk);

                ProdukStok::create([
                    'produk_id' => $produk->id,
                    'user_id' => $user->id,
                    'stok' => $stockQuantity,
                ]);
            }
        }
    }

    private function getStockQuantity($user, $produk)
    {
        // Stockists get more stock than basic members
        if ($user->isStockis) {
            // Stockists get 10-50 stock for aktivasi packages, 20-100 for quick reward
            if ($produk->paket == 1) { // Aktivasi
                return rand(10, 50);
            } else { // Quick Reward
                return rand(20, 100);
            }
        } else {
            // Basic members get less stock: 0-10 for aktivasi, 5-25 for quick reward
            if ($produk->paket == 1) { // Aktivasi
                return rand(0, 10);
            } else { // Quick Reward
                return rand(5, 25);
            }
        }
    }
}
