<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Setting::create([
            'nama' => 'PT Glowink Indonesia',
            'keterangan' => 'Aplikasi Manajemen Glowink',
            'bank_name' => 'BCA',
            'bank_atas_nama' => 'PT Glowink Indonesia',
            'no_rek' => '1234567890',
            'email' => 'info@glowink.com',
            'alamat' => 'Jl. Contoh Alamat No. 123, Jakarta',
            'telepon' => '021-12345678',
            'logo' => null,
            'val' => null,
        ]);
    }
}
