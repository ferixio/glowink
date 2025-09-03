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

        // Distribusi level untuk variasi data mitra
        $levelDistributions = [
            ['level' => null, 'count' => 10, 'min' => 0, 'max' => 0],
            ['level' => 'bronze', 'count' => 10, 'min' => 20, 'max' => 80],
            ['level' => 'silver', 'count' => 10, 'min' => 100, 'max' => 300],
            ['level' => 'gold', 'count' => 8, 'min' => 750, 'max' => 1200],
            ['level' => 'platinum', 'count' => 6, 'min' => 3000, 'max' => 6000],
            ['level' => 'titanium', 'count' => 5, 'min' => 15000, 'max' => 22000],
            ['level' => 'ambassador', 'count' => 4, 'min' => 60000, 'max' => 90000],
            ['level' => 'chairman', 'count' => 3, 'min' => 150000, 'max' => 200000],
        ];

        $createdUsers = [];
        $index = 0;
        $today = now()->format('Ymd');

        foreach ($levelDistributions as $dist) {
            for ($c = 0; $c < $dist['count']; $c++) {
                $urutan = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
                $idMitra = 'G' . $today . $urutan;
                $mitraNumber = $index + 1;
                $username = 'mitra' . $mitraNumber;
                $email = $username . '@gmail.com';
                [$provinsi, $kabupaten] = $this->getFirstProvinceAndRegency($provinces, $regencies);
                $groupSponsor = array_column(array_slice($createdUsers, max(0, $index - 9), 9), 'id');

                $idSponsor = $index > 0 ? $createdUsers[$index - 1]['id'] : null;

                // Tentukan poin berdasarkan rentang agar beragam namun valid utk level tsb
                if ($dist['min'] === $dist['max']) {
                    $poinReward = $dist['min'];
                } else {
                    $range = $dist['max'] - $dist['min'];
                    $step = (int) floor($range / max(1, $dist['count'] - 1));
                    $poinReward = $dist['min'] + ($step * $c);
                }

                $statusQR = $dist['level'] !== null || ($index % 2 === 0); // Banyak yang true, sebagian non-level juga true
                $planKarir = $dist['level'];

                $user = User::create([
                    'id_mitra' => $idMitra,
                    'username' => $username,
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'isAdmin' => false,
                    'isStockis' => false,
                    'status_qr' => $statusQR,
                    'id_sponsor' => $idSponsor,
                    'nama' => 'Mitra Glowink ' . $mitraNumber,
                    'provinsi' => $provinsi,
                    'kabupaten' => $kabupaten,
                    'alamat' => 'Jl. Mitra No. ' . $mitraNumber,
                    'no_telp' => '0812345678' . str_pad($mitraNumber, 2, '0', STR_PAD_LEFT),
                    'no_rek' => '12345678' . str_pad($mitraNumber, 2, '0', STR_PAD_LEFT),
                    'nama_rekening' => 'Mitra Glowink ' . $mitraNumber,
                    'bank' => 'BNI',
                    'tgl_daftar' => now(),
                    'group_sponsor' => $groupSponsor,
                    'saldo_penghasilan' => 0,
                    'poin_reward' => $poinReward,
                    'plan_karir_sekarang' => $planKarir,
                    'next_plan_karir' => null,
                    'next_poin_karir' => 0,
                ]);

                $createdUsers[] = [
                    'id' => $user->id,
                    'id_mitra' => $idMitra,
                ];

                $index++;
            }
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
