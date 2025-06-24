<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin
        User::create([
            'id_mitra' => 'ADM001',
            'username' => 'admin',
            'email' => 'admin@glowink.com',
            'password' => Hash::make('admin123'),
            'isAdmin' => true,
            'isStockis' => false,
            'isMitraBasic' => false,
            'isMitraKarir' => false,
            'nama' => 'Administrator',
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Selatan',
            'alamat' => 'Jl. Admin No. 1',
            'no_telp' => '081234567890',
            'no_rek' => '1234567890',
            'nama_rekening' => 'Admin Glowink',
            'bank' => 'BCA',
            'tgl_daftar' => now(),
            'group_sponsor' => 'ADMIN',
            'saldo_penghasilan' => 0,
            'poin_reward' => 0,
            'plan_karir_sekarang' => 'Admin',
            'next_plan_karir' => 'Admin',
            'next_poin_karir' => 0,
        ]);

        // Create Stockis
        User::create([
            'id_mitra' => 'STK001',
            'username' => 'stockis',
            'email' => 'stockis@glowink.com',
            'password' => Hash::make('stockis123'),
            'isAdmin' => false,
            'isStockis' => true,
            'isMitraBasic' => false,
            'isMitraKarir' => false,
            'nama' => 'Stockis Glowink',
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Pusat',
            'alamat' => 'Jl. Stockis No. 1',
            'no_telp' => '081234567891',
            'no_rek' => '1234567891',
            'nama_rekening' => 'Stockis Glowink',
            'bank' => 'Mandiri',
            'tgl_daftar' => now(),
            'group_sponsor' => 'STOCKIS',
            'saldo_penghasilan' => 0,
            'poin_reward' => 0,
            'plan_karir_sekarang' => 'Stockis',
            'next_plan_karir' => 'Stockis',
            'next_poin_karir' => 0,
        ]);

        // Create Mitra Basic
        User::create([
            'id_mitra' => 'MTR001',
            'username' => 'mitra',
            'email' => 'mitra@glowink.com',
            'password' => Hash::make('mitra123'),
            'isAdmin' => false,
            'isStockis' => false,
            'isMitraBasic' => true,
            'isMitraKarir' => false,
            'nama' => 'Mitra Glowink',
            'provinsi' => 'DKI Jakarta',
            'kabupaten' => 'Jakarta Barat',
            'alamat' => 'Jl. Mitra No. 1',
            'no_telp' => '081234567892',
            'no_rek' => '1234567892',
            'nama_rekening' => 'Mitra Glowink',
            'bank' => 'BNI',
            'tgl_daftar' => now(),
            'group_sponsor' => 'MITRA',
            'saldo_penghasilan' => 0,
            'poin_reward' => 0,
            'plan_karir_sekarang' => 'Mitra Basic',
            'next_plan_karir' => 'Mitra Karir',
            'next_poin_karir' => 0,
        ]);
    }
}
