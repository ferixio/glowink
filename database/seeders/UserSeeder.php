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
        // Load provinces
        $provinces = [];
        if (($handle = fopen(public_path('data/provinces.csv'), 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $provinces[$data[0]] = $data[1];
            }
            fclose($handle);
        }

        // Load regencies
        $regencies = [];
        if (($handle = fopen(public_path('data/regencies.csv'), 'r')) !== false) {
            while (($data = fgetcsv($handle)) !== false) {
                $regencies[] = [
                    'id' => $data[0],
                    'province_id' => $data[1],
                    'name' => $data[2],
                ];
            }
            fclose($handle);
        }

        // Helper to get random province and regency
        function getFirstProvinceAndRegency($provinces, $regencies)
        {
            // Ambil provinsi pertama
            reset($provinces);
            $provinceId = key($provinces);
            $provinceName = current($provinces);
            // Ambil kabupaten pertama yang sesuai provinsi
            $filteredRegencies = array_filter($regencies, function ($regency) use ($provinceId) {
                return $regency['province_id'] == $provinceId;
            });
            $regency = $filteredRegencies ? reset($filteredRegencies) : null;
            $regencyName = $regency ? $regency['name'] : null;
            return [$provinceName, $regencyName];
        }

        // Create Admin
        list($provinsi, $kabupaten) = getFirstProvinceAndRegency($provinces, $regencies);
        User::updateOrCreate(
            ['id_mitra' => 'ADM001'],
            [
                'username' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'isAdmin' => true,
                'isStockis' => false,

                'nama' => 'Administrator',
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'alamat' => 'Jl. Admin No. 1',
                'no_telp' => '081234567890',
                'no_rek' => '1234567890',
                'nama_rekening' => 'Admin Glowink',
                'bank' => 'BCA',
                'tgl_daftar' => now(),
                'group_sponsor' => [],
                'saldo_penghasilan' => 0,
                'poin_reward' => 750,
                'plan_karir_sekarang' => 'Gold',
                'next_plan_karir' => 'Gold',
                'next_poin_karir' => 0,
            ]
        );

        // Create Stockis
        list($provinsi, $kabupaten) = getFirstProvinceAndRegency($provinces, $regencies);
        User::create([
            'id_mitra' => 'STK001',
            'username' => 'stockis',
            'email' => 'stockis@gmail.com',
            'password' => Hash::make('password'),
            'isAdmin' => false,
            'isStockis' => true,
            'nama' => 'Stockis Glowink',
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'alamat' => 'Jl. Stockis No. 1',
            'no_telp' => '081234567891',
            'no_rek' => '1234567891',
            'nama_rekening' => 'Stockis Glowink',
            'bank' => 'Mandiri',
            'tgl_daftar' => now(),
            'group_sponsor' => [],
            'saldo_penghasilan' => 0,
            'poin_reward' => 750,
            'plan_karir_sekarang' => 'Gold',
            'next_plan_karir' => 'Gold',
            'next_poin_karir' => 0,
        ]);

        // Create Mitra Basic
        list($provinsi, $kabupaten) = getFirstProvinceAndRegency($provinces, $regencies);
        User::create([
            'id_mitra' => 'MTR001',
            'username' => 'mitra',
            'email' => 'mitra@gmail.com',
            'password' => Hash::make('password'),
            'isAdmin' => false,
            'isStockis' => false,
            'status_qr' => false,
            'nama' => 'Mitra Glowink',
            'provinsi' => $provinsi,
            'kabupaten' => $kabupaten,
            'alamat' => 'Jl. Mitra No. 1',
            'no_telp' => '081234567892',
            'no_rek' => '1234567892',
            'nama_rekening' => 'Mitra Glowink',
            'bank' => 'BNI',
            'tgl_daftar' => now(),
            'group_sponsor' => [],
            'saldo_penghasilan' => 0,
            'poin_reward' => 20,
            'plan_karir_sekarang' => 'bronze',
            'next_plan_karir' => 'bronze',
            'next_poin_karir' => 0,
        ]);
    }
}
