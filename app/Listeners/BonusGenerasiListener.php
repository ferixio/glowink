<?php

namespace App\Listeners;

use App\Events\BonusGenerasi;
use App\Models\Aktivitas;
use App\Models\JaringanMitra;
use App\Models\PembelianBonus;
use App\Models\Penghasilan;
use App\Models\User;

class BonusGenerasiListener
{
    public function handle(BonusGenerasi $event)
    {
        $pembelian = $event->pembelian;
        $user = $pembelian->user; // assuming relation exists

        // Ambil semua upline dari jaringan mitra (maksimal 10 level)
        $uplines = JaringanMitra::where('user_id', $user->id)
            ->orderBy('level')
            ->limit(10)
            ->get();

        foreach ($uplines as $upline) {
            $sponsor = User::find($upline->sponsor_id);
            if (!$sponsor) {
                continue;
            }

            $statusQr = $sponsor->status_qr;
            $nominalBonus = $statusQr ? 1500 : 300;

            Penghasilan::create([
                'user_id' => $sponsor->id,
                'kategori_bonus' => 'Bonus Generasi',
                'status_qr' => $statusQr,
                'tgl_dapat_bonus' => now(),
                'keterangan' => "bonus generasi level {$upline->level}",
                'nominal_bonus' => $nominalBonus,
            ]);

            // Tambahkan nominalBonus ke saldo_penghasilan sponsor
            $sponsor->saldo_penghasilan += $nominalBonus;
            $sponsor->save();

            // Buat data aktivitas setelah berhasil menambah saldo
            Aktivitas::create([
                'user_id' => $sponsor->id,
                'judul' => 'Bonus Generasi Diterima',
                'keterangan' => "Menerima bonus generasi level {$upline->level} dari member {$user->nama}",
                'status' => 'Berhasil',
                'nominal' => $nominalBonus,
            ]);

            // Buat data PembelianBonus untuk setiap upline
            $idMitra = $user->id_mitra ?? 'Unknown';
            $point = 3;

            $nominalPembelianBonus = $statusQr ? 1500 : 300;

            if ($statusQr) {
                // Jika status_qr true, buat keterangan mendapatkan bonus
                PembelianBonus::create([
                    'pembelian_id' => $pembelian->id,
                    'user_id' => $sponsor->id,
                    'keterangan' => "ID {$idMitra} mendapatkan {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
                ]);
            } else {
                // Jika status_qr false, buat keterangan kehilangan peluang
                PembelianBonus::create([
                    'pembelian_id' => $pembelian->id,
                    'user_id' => $sponsor->id,
                    'keterangan' => "ID {$idMitra} kehilangan peluang {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
                ]);
            }
        }
    }
}

// public function handle(BonusGenerasi $event)
// {
//     $pembelian = $event->pembelian;
//     $user = $pembelian->user; // assuming relation exists
//     $groupSponsor = $user->group_sponsor ?? [];
//     if (!is_array($groupSponsor)) {
//         $groupSponsor = json_decode($groupSponsor, true) ?? [];
//     }
//     $groupSponsor = array_slice($groupSponsor, 0, 10); // Batasi maksimal 10 id
//     foreach ($groupSponsor as $sponsorId) {
//         $sponsor = \App\Models\User::find($sponsorId);
//         if (!$sponsor) {
//             continue;
//         }

//         $statusQr = $sponsor->status_qr;
//         $nominalBonus = $statusQr ? 1500 : 300;
//         \App\Models\Penghasilan::create([
//             'user_id' => $sponsor->id,
//             'kategori_bonus' => 'Bonus Generasi',
//             'status_qr' => $statusQr,
//             'tgl_dapat_bonus' => now(),
//             'keterangan' => 'bonus generasi',
//             'nominal_bonus' => $nominalBonus,
//         ]);
//         // Tambahkan nominalBonus ke saldo_penghasilan sponsor
//         $sponsor->saldo_penghasilan += $nominalBonus;
//         $sponsor->save();
//         // Buat juga data penghasilan untuk penambahan saldo
//         // \App\Models\Penghasilan::create([
//         //     'user_id' => $sponsor->id,
//         //     'kategori_bonus' => 'Bonus Generasi',
//         //     'status_qr' => $statusQr,
//         //     'tgl_dapat_bonus' => now(),
//         //     'keterangan' => 'penambahan saldo bonus generasi',
//         //     'nominal_bonus' => $nominalBonus,
//         // ]);
//     }
// }
