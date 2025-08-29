<?php

namespace App\Listeners;

use App\Events\BonusReward;
use App\Models\Aktivitas;
use App\Models\PembelianBonus;
use App\Models\Penghasilan;
use App\Models\User;

class BonusRewardListener
{
    public function handle(BonusReward $event)
    {
        $pembelian = $event->pembelian;
        $user = $pembelian->user;
        $sponsor = User::find($user->id_sponsor);
        $statusQr = $user->status_qr;
        $idMitra = $user->id_mitra;

        // Formatter Rupiah
        $format = function ($number) {
            return 'Rp. ' . number_format((float) $number, 0, ',', '.');
        };

        // Cek apakah user memiliki QR aktif
        if ($user->status_qr && $sponsor) {
            $hasPaket2 = false;
            $totalBonus = 0;

            // Loop sekali untuk memproses semua detail pembelian
            foreach ($pembelian->details as $detail) {
                if ($detail->paket == 2) {
                    $hasPaket2 = true;
                    $bonusAmount = 20000;
                    $keterangan = 'Bonus Cashback QR aktif';
                } else {
                    $bonusAmount = 10000;
                    $keterangan = 'Bonus Cashback';
                }

                $totalBonus += $bonusAmount;

                // Buat record penghasilan untuk sponsor
                Penghasilan::create([
                    'user_id' => $user->id,
                    'kategori_bonus' => 'Bonus Cashback',
                    'status_qr' => $statusQr,
                    'tgl_dapat_bonus' => now(),
                    'keterangan' => $keterangan,
                    'nominal_bonus' => $bonusAmount,
                ]);

                // Buat record aktivitas untuk user
                Aktivitas::create([
                    'user_id' => $user->id,
                    'judul' => 'Bonus Cashback',
                    'keterangan' => "Menerima {$keterangan} {$format($bonusAmount)}",
                    'tipe' => 'plus',
                    'status' => 'Berhasil',
                    'nominal' => $bonusAmount,
                ]);

                PembelianBonus::create([
                    'pembelian_id' => $pembelian->id,
                    'user_id' => $user->id,
                    'keterangan' => "ID {$idMitra} mendapatkan bonus cashback {$format($bonusAmount)}",
                    'tipe' => 'bonus',
                    'created_at' => now(),
                    'updated_at' => now(),

                ]);
            }

            // Update saldo penghasilan sponsor
            if ($totalBonus > 0) {
                $user->saldo_penghasilan += $totalBonus;
                $user->save();
            }

            // Trigger event untuk perubahan level user jika ada paket 2
            if ($hasPaket2) {
                event(new \App\Events\ChangeLevelUser($user, $user->poin_reward));
            }
        } else {
            // Jika user tidak memiliki QR aktif, berikan cashback khusus untuk paket 1
            // karena status_qr akan menjadi true secara otomatis ketika membeli paket 2
            $totalBonus = 0;

            foreach ($pembelian->details as $detail) {
                if ($detail->paket == 1) {
                    $bonusAmount = 10000;
                    $keterangan = 'Bonus Cashback Aktivasi QR';

                    $totalBonus += $bonusAmount;

                    // Buat record penghasilan untuk user
                    Penghasilan::create([
                        'user_id' => $user->id,
                        'kategori_bonus' => 'Bonus Cashback',
                        'status_qr' => $statusQr,
                        'tgl_dapat_bonus' => now(),
                        'keterangan' => $keterangan,
                        'nominal_bonus' => $bonusAmount,
                    ]);

                    // Buat record aktivitas untuk user
                    Aktivitas::create([
                        'user_id' => $user->id,
                        'judul' => 'Bonus Cashback',
                        'keterangan' => "Menerima {$keterangan} {$format($bonusAmount)}",
                        'tipe' => 'plus',
                        'status' => 'Berhasil',
                        'nominal' => $bonusAmount,
                    ]);
                }
            }

            // Update saldo penghasilan user jika ada bonus
            if ($totalBonus > 0) {
                $user->saldo_penghasilan += $totalBonus;
                $user->save();
            }
        }

        // Panggil event BonusGenerasi untuk menangani bonus reward upline
        event(new \App\Events\BonusGenerasi($pembelian, false));
    }
}
