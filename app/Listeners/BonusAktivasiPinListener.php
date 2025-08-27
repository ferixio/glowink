<?php

namespace App\Listeners;

use App\Events\BonusAktivasiPin;
use App\Models\Aktivitas;
use App\Models\JaringanMitra;
use App\Models\PembelianBonus;
use App\Models\User;

class BonusAktivasiPinListener
{
    public function handle(BonusAktivasiPin $event)
    {
        $aktivasiPin = $event->aktivasiPin;
        $pembelianDetail = $aktivasiPin->pembelianDetail;
        $pembelian = $pembelianDetail->pembelian;
        $user = $pembelian->user;
        $sponsor = User::find($user->id_sponsor);
        $statusQr = $user->status_qr;

        // Cek apakah user memiliki QR aktif
        if ($user->status_qr && $sponsor) {
            $hasPaket2 = false;
            $totalBonus = 0;

            // Proses pembelian detail untuk aktivasi pin
            if ($pembelianDetail->paket == 2) {
                $hasPaket2 = true;
                $bonusAmount = 20000;
                $keterangan = 'Bonus Cashback QR aktif';
            } else {
                $bonusAmount = 10000;
                $keterangan = 'Bonus Cashback';
            }

            $totalBonus += $bonusAmount;

            // Buat record penghasilan untuk user
            \App\Models\Penghasilan::create([
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
                'keterangan' => "Menerima {$keterangan}",
                'tipe' => 'plus',
                'status' => 'Berhasil',
                'nominal' => $bonusAmount,
            ]);
            // Buat record PembelianBonus untuk user
            PembelianBonus::create([
                'pembelian_id' => $pembelian->id,
                'aktivasi_pin_id' => $aktivasiPin->id,
                'user_id' => $user->id,
                'keterangan' => "ID {$user->id_mitra} mendapatkan BONUS CASHBACK {$bonusAmount}",
                'tipe' => 'bonus',
                'created_at' => now(),
                'updated_at' => now(),

            ]);

            // Update saldo penghasilan user
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

            if ($pembelianDetail->paket == 1) {

                $bonusAmount = 10000;
                $keterangan = 'Bonus Cashback Aktivasi QR';

                $totalBonus += $bonusAmount;

                // Buat record penghasilan untuk user
                \App\Models\Penghasilan::create([
                    'user_id' => $user->id,
                    'kategori_bonus' => 'Bonus Cashback QR',
                    'status_qr' => $statusQr,
                    'tgl_dapat_bonus' => now(),
                    'keterangan' => $keterangan,
                    'nominal_bonus' => $bonusAmount,
                ]);

                // Buat record aktivitas untuk user
                Aktivitas::create([
                    'user_id' => $user->id,
                    'judul' => 'Bonus Cashback',
                    'keterangan' => "Menerima {$keterangan}",
                    'tipe' => 'plus',
                    'status' => 'Berhasil',
                    'nominal' => $bonusAmount,
                ]);
                // Buat record PembelianBonus untuk user
                PembelianBonus::create([
                    'pembelian_id' => $pembelian->id,
                    'aktivasi_pin_id' => $aktivasiPin->id,
                    'user_id' => $user->id,
                    'keterangan' => "ID {$user->id_mitra} mendapatkan BONUS CASHBACK QR {$bonusAmount}",
                    'tipe' => 'bonus',
                    'created_at' => now(),
                    'updated_at' => now(),

                ]);
            }

            // Update saldo penghasilan user jika ada bonus
            if ($totalBonus > 0) {
                $user->saldo_penghasilan += $totalBonus;
                $user->save();
            }
        }

        // Ambil upline dari jaringan mitra dan tambahkan user sebagai level 0
        $uplines = JaringanMitra::where('user_id', $user->id)
            ->orderBy('level')
            ->limit(10)
            ->get();

        // Tambahkan user sebagai level 0 (level terendah)
        $userUpline = (object) [
            'sponsor_id' => $user->id,
            'level' => 0,
        ];
        $uplines->prepend($userUpline);

        // Temporary variables untuk mengumpulkan data
        $activitiesToCreate = [];
        $pembelianBonusesToCreate = [];
        $sponsorsToUpdate = [];

        foreach ($uplines as $upline) {
            $sponsor = User::find($upline->sponsor_id);
            if (!$sponsor) {
                continue;
            }

            $totalPoints = 0;
            $totalBonus = 0;

            $totalBonusAktivasi = 0;

            if ($pembelianDetail->paket == 1) {
                $totalBonusAktivasi = 300;
            } elseif ($pembelianDetail->paket == 2) {
                $totalBonusAktivasi = 1500;
            }
            // Hitung total poin dan bonus dari satu aktivasi pin
            $totalPoints = 1;

            if ($sponsor->status_qr) {
                $totalBonus = 1500;
            } else {
                $totalBonus = 300;
            }

            // Update sponsor dan siapkan data untuk aktivitas
            if ($totalPoints > 0) {
                $statusQr = $sponsor->status_qr;

                // Update sponsor data
                if ($statusQr) {
                    $sponsor->poin_reward += $totalPoints;
                }

                $sponsor->saldo_penghasilan += $totalBonus;

                // Buat Penghasilan record
                \App\Models\Penghasilan::create([
                    'user_id' => $sponsor->id,
                    'kategori_bonus' => 'Bonus Generasi',
                    'status_qr' => $statusQr,
                    'tgl_dapat_bonus' => now(),
                    'keterangan' => "bonus Generasi dari aktivasi pin mitra #{$user->id_mitra}",
                    'nominal_bonus' => $totalBonus,
                ]);

                // Simpan sponsor untuk update batch
                $sponsorsToUpdate[] = $sponsor;

                // Buat 2 aktivitas terpisah: Poin dan Bonus Aktivasi Pin
                if ($statusQr) {
                    // Aktivitas untuk Poin
                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Poin',
                        'keterangan' => "Mendapatkan {$totalPoints} poin dari aktivasi pin mitra #{$user->id_mitra}",
                        'tipe' => 'plus',
                        'status' => 'Berhasil',
                        'nominal' => $totalPoints,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Aktivitas untuk Bonus Aktivasi Pin
                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Bonus Aktivasi Pin',
                        'keterangan' => "Mendapatkan bonus aktivasi pin {$totalBonus} dari mitra #{$user->id_mitra}",
                        'tipe' => 'plus',
                        'status' => 'Berhasil',
                        'nominal' => $totalBonus,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {

                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Kehilangan Peluang Poin',
                        'keterangan' => "Kehilangan peluang {$totalPoints} poin dan bonus aktivasi pin {$totalBonusAktivasi} dari aktivasi pin member #{$user->id_mitra}",
                        'tipe' => 'minus',
                        'status' => '',
                        'nominal' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Aktivitas untuk Kehilangan Peluang Bonus Aktivasi Pin

                }

                // Kumpulkan PembelianBonus untuk dibuat nanti
                $idMitra = $sponsor->id_mitra ?? 'Unknown';
                $point = 1;
                $nominalPembelianBonus = $statusQr ? 1500 : 300;

                if ($statusQr) {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'aktivasi_pin_id' => $aktivasiPin->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => "ID {$idMitra} mendapatkan {$point} point dan BONUS AKTIVASI PIN {$nominalPembelianBonus}",
                        'tipe' => 'bonus',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'aktivasi_pin_id' => $aktivasiPin->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => "ID {$idMitra} mendapatkan BONUS AKTIVASI PIN {$nominalPembelianBonus}",
                        'tipe' => 'bonus',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'aktivasi_pin_id' => $aktivasiPin->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => "ID {$idMitra} kehilangan peluang {$point} point dan BONUS AKTIVASI PIN 1500 dari mitra #{$user->id_mitra}",
                        'tipe' => 'loss',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Update semua sponsor sekaligus
        foreach ($sponsorsToUpdate as $sponsor) {
            $sponsor->save();
            if ($sponsor->poin_reward > 0) {
                event(new \App\Events\ChangeLevelUser($sponsor, $sponsor->poin_reward));
            }
        }

        // Buat semua aktivitas sekaligus
        if (!empty($activitiesToCreate)) {
            Aktivitas::insert($activitiesToCreate);
        }

        // Buat semua PembelianBonus sekaligus
        if (!empty($pembelianBonusesToCreate)) {
            PembelianBonus::insert($pembelianBonusesToCreate);
        }
    }
}
