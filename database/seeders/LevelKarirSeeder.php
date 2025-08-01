<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LevelKarirSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $levels = [
            [
                'nama_level' => 'Bronze',
                'poin_reward' => 20,
                'minimal_RO_QR' => 1,
                'angka_deviden' => 3000,
                'jumlah_mitra_level_ini' => null,
            ],
            [
                'nama_level' => 'Silver',
                'poin_reward' => 100,
                'minimal_RO_QR' => 2,
                'angka_deviden' => 2000,
                'jumlah_mitra_level_ini' => null,
            ],
            [
                'nama_level' => 'Gold',
                'poin_reward' => 750,
                'minimal_RO_QR' => 3,
                'angka_deviden' => 1500,
                'jumlah_mitra_level_ini' => null,
            ],
            [
                'nama_level' => 'Platinum',
                'poin_reward' => 3000,
                'minimal_RO_QR' => 4,
                'angka_deviden' => 750,
                'jumlah_mitra_level_ini' => null,
            ],
            [
                'nama_level' => 'Titanium',
                'poin_reward' => 15000,
                'minimal_RO_QR' => 5,
                'angka_deviden' => 750,
                'jumlah_mitra_level_ini' => null,
            ],
            [
                'nama_level' => 'Ambassador',
                'poin_reward' => 60000,
                'minimal_RO_QR' => 6,
                'angka_deviden' => 500,
                'jumlah_mitra_level_ini' => null,
            ],
            [
                'nama_level' => 'Chairman',
                'poin_reward' => 150000,
                'minimal_RO_QR' => 7,
                'angka_deviden' => 0,
                'jumlah_mitra_level_ini' => null,
            ],
        ];

        foreach ($levels as $level) {
            \App\Models\LevelKarir::create($level);
        }
    }
}
