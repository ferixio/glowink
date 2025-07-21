<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MitraSeeder extends Seeder
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

        // Simpan semua mitra yang sudah dibuat
        $createdUsers = [];

        for ($i = 0; $i < 15; $i++) {
            $urutan = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $today = now()->format('Ymd');
            $idMitra = 'G' . $today . $urutan;

            $username = 'mitra' . ($i + 2);
            $email = $username . '@gmail.com';

            [$provinsi, $kabupaten] = $this->getFirstProvinceAndRegency($provinces, $regencies);

            // group_sponsor = 9 mitra sebelumnya (jika ada)
            $groupSponsor = array_column(array_slice($createdUsers, max(0, $i - 9), 9), 'id');

            // id_sponsor = id dari mitra sebelumnya
            $idSponsor = $i > 0 ? $createdUsers[$i - 1]['id'] : null;

            // status_qr: dua pertama false, sisanya true
            $statusQR = $i < 2 ? false : true;

            $user = User::create([
                'id_mitra' => $idMitra,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password'),
                'isAdmin' => false,
                'isStockis' => false,
                'status_qr' => $statusQR,
                'id_sponsor' => $idSponsor,
                'nama' => 'Mitra Glowink ' . ($i + 2),
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'alamat' => 'Jl. Mitra No. ' . ($i + 2),
                'no_telp' => '0812345678' . str_pad($i + 2, 2, '0', STR_PAD_LEFT),
                'no_rek' => '12345678' . str_pad($i + 2, 2, '0', STR_PAD_LEFT),
                'nama_rekening' => 'Mitra Glowink ' . ($i + 2),
                'bank' => 'BNI',
                'tgl_daftar' => now(),
                'group_sponsor' => $groupSponsor,
                'saldo_penghasilan' => 0,
                'poin_reward' => 0,
                'plan_karir_sekarang' => 'bronze',
                'next_plan_karir' => 'bronze',
                'next_poin_karir' => 0,
            ]);

            $createdUsers[] = [
                'id' => $user->id,
                'id_mitra' => $idMitra,
            ];
        }
    }

    private function getFirstProvinceAndRegency($provinces, $regencies)
    {
        reset($provinces);
        $provinceId = key($provinces);
        $provinceName = current($provinces);
        $filteredRegencies = array_filter($regencies, fn($regency) => $regency['province_id'] == $provinceId);
        $regency = $filteredRegencies ? reset($filteredRegencies) : null;
        return [$provinceName, $regency ? $regency['name'] : null];
    }
}
