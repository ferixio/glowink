<?php

namespace Database\Seeders;

use App\Models\Pembelian;
use App\Models\User;
use Illuminate\Database\Seeder;

class PembelianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users for seeding
        $stockists = User::where('isStockis', true)->get();
        $basicMembers = User::where('isStockis', false)->get();
        $adminUser = User::where('id', 1)->first(); // Admin user with ID 1

     

        $statuses = ['menunggu', 'transfer', 'proses', 'ditolak', 'selesai'];

        // Create purchases for stockists (buying from admin user ID 1)
        foreach ($stockists as $stockist) {
            $this->createStockistPurchases($stockist, $adminUser, $statuses);
        }

        // Create purchases for basic members (buying from stockists)
        foreach ($basicMembers as $member) {
            $this->createMemberPurchases($member, $stockists, $statuses);
        }
    }

    private function createStockistPurchases($stockist, $adminUser, $statuses)
    {
        // Create 3-5 purchases per stockist
        $purchaseCount = rand(3, 5);

        for ($i = 0; $i < $purchaseCount; $i++) {
            $status = $statuses[array_rand($statuses)];
            $totalBeli = rand(500000, 2000000); // Random total between 500k - 2M

            Pembelian::create([
                'tgl_beli' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                'user_id' => $stockist->id,
                'beli_dari' => $adminUser->id, // Stockists buy from admin (ID 1)
                'tujuan_beli' => 'null',
                'nama_penerima' => $stockist->nama,
                'no_telp' => $stockist->no_telp ?? '081234567890',
                'alamat_tujuan' => $stockist->alamat ?? 'Alamat Stockist',
                'total_beli' => $totalBeli,
                'total_bonus' => 0, // Will be calculated later
                'status_pembelian' => $status,
                'jumlah_poin_qr' => 0, // Will be calculated later
            ]);
        }
    }

    private function createMemberPurchases($member, $stockists, $statuses)
    {
        // Create 2-4 purchases per member
        $purchaseCount = rand(2, 4);

        for ($i = 0; $i < $purchaseCount; $i++) {
            $randomStockist = $stockists->random();
            $status = $statuses[array_rand($statuses)];
            $totalBeli = rand(200000, 1000000); // Random total between 200k - 1M

            Pembelian::create([
                'tgl_beli' => now()->subDays(rand(1, 30))->format('Y-m-d'),
                'user_id' => $member->id,
                'beli_dari' => $randomStockist->id, // Members buy from stockists
                'tujuan_beli' => 'null',
                'nama_penerima' => $member->nama,
                'no_telp' => $member->no_telp ?? '081234567890',
                'alamat_tujuan' => $member->alamat ?? 'Alamat Member',
                'total_beli' => $totalBeli,
                'total_bonus' => 0, // Will be calculated later
                'status_pembelian' => $status,
                'jumlah_poin_qr' => 0, // Will be calculated later
            ]);
        }
    }
}
