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

        $createdUsers = [];
        for ($i = 0; $i < 15; $i++) {
            $urutan = str_pad($i + 1, 2, '0', STR_PAD_LEFT);
            $today = '20250723';
            $idMitra = 'G' . $today . $urutan;
<<<<<<< HEAD
            $username = 'mitra' . ($i + 1);
=======
            $mitraNumber = $i + 1; // Index 0-14 menjadi 1-15
            $username = 'mitra' . $mitraNumber;
>>>>>>> 6040f3b9d4ed20cdd9c340f718bfabffdf84dea7
            $email = $username . '@gmail.com';
            [$provinsi, $kabupaten] = $this->getFirstProvinceAndRegency($provinces, $regencies);
            $groupSponsor = array_column(array_slice($createdUsers, max(0, $i - 9), 9), 'id');

            $idSponsor = $i > 0 ? $createdUsers[$i - 1]['id'] : null;

            $poinReward = in_array($i, [2, 6]) ? 25 : 0;
            $statusQR = $poinReward != 0;

            $user = User::create([
                'id_mitra' => $idMitra,
                'username' => $username,
                'email' => $email,
                'password' => Hash::make('password'),
                'isAdmin' => false,
                'isStockis' => false,
                'status_qr' => $statusQR,
                'id_sponsor' => $idSponsor,
<<<<<<< HEAD
                'nama' => 'Mitra Glowink ' . ($i + 1),
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'alamat' => 'Jl. Mitra No. ' . ($i + 1),
                'no_telp' => '0812345678' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'no_rek' => '12345678' . str_pad($i + 1, 2, '0', STR_PAD_LEFT),
                'nama_rekening' => 'Mitra Glowink ' . ($i + 1),
=======
                'nama' => 'Mitra Glowink ' . $mitraNumber,
                'provinsi' => $provinsi,
                'kabupaten' => $kabupaten,
                'alamat' => 'Jl. Mitra No. ' . $mitraNumber,
                'no_telp' => '0812345678' . str_pad($mitraNumber, 2, '0', STR_PAD_LEFT),
                'no_rek' => '12345678' . str_pad($mitraNumber, 2, '0', STR_PAD_LEFT),
                'nama_rekening' => 'Mitra Glowink ' . $mitraNumber,
>>>>>>> 6040f3b9d4ed20cdd9c340f718bfabffdf84dea7
                'bank' => 'BNI',
                'tgl_daftar' => now(),
                'group_sponsor' => $groupSponsor,
                'saldo_penghasilan' => 0,
                'poin_reward' => $poinReward,
                'plan_karir_sekarang' => $poinReward == 0 ? null : 'bronze',
                'next_plan_karir' => null,
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
