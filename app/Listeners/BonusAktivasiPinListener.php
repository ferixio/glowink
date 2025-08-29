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
            $totalBonusJikaQR = 0;
            $hasPaket2 = false;

            // Hitung total poin dan bonus dari aktivasi pin
            if ($pembelianDetail->paket == 2) {
                $hasPaket2 = true;
                $totalPoints = 1;
                $totalBonusJikaQR = 1500;

                if ($sponsor->status_qr) {
                    $totalBonus = 1500;
                }
                // Jika status_qr false, totalBonus tetap 0
            } else if ($pembelianDetail->paket == 1) {
                $totalBonusJikaQR = 1500; // Kehilangan peluang bonus 1500 jika status QR false
                // Untuk paket 1, bonus diberikan berdasarkan status QR
                if ($sponsor->status_qr) {
                    $totalBonus = 1500;
                } else {
                    $totalBonus = 300;
                }
            }

            // Update sponsor dan siapkan data untuk aktivitas
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

            // Tentukan keterangan berdasarkan status QR dan paket
            if ($statusQr) {
                // Jika status QR true, tidak ada kehilangan peluang
                if ($totalPoints > 0) {
                    $keterangan = "Mendapatkan {$totalPoints} poin dan bonus generasi {$totalBonus} dari aktivasi pin mitra #{$user->id_mitra}";
                    $keteranganPembelianBonus = "ID {$sponsor->id_mitra} mendapatkan {$totalPoints} poin dan bonus generasi {$totalBonus} dari aktivasi pin mitra #{$user->id_mitra}";
                } else {
                    $keterangan = "Mendapatkan bonus generasi {$totalBonus} dari aktivasi pin mitra #{$user->id_mitra}";
                    $keteranganPembelianBonus = "ID {$sponsor->id_mitra} mendapatkan bonus generasi {$totalBonus} dari aktivasi pin mitra #{$user->id_mitra}";
                }
            } else {
                // Jika status QR false, ada kehilangan peluang
                if ($totalPoints > 0) {
                    $keterangan = "Mendapatkan bonus generasi {$totalBonus} dari aktivasi pin mitra #{$user->id_mitra}";
                    $keteranganKehilangan = "Kehilangan peluang {$totalPoints} poin dan bonus generasi {$totalBonusJikaQR} dari aktivasi pin mitra #{$user->id_mitra}";
                    $keteranganPembelianBonus = "ID {$sponsor->id_mitra} mendapatkan bonus generasi {$totalBonus} dari aktivasi pin mitra #{$user->id_mitra}";
                    $keteranganPembelianBonusKehilangan = "ID {$sponsor->id_mitra} kehilangan peluang {$totalPoints} poin dan bonus generasi {$totalBonusJikaQR} dari aktivasi pin mitra #{$user->id_mitra}";
                } else {
                    $keterangan = "Mendapatkan bonus generasi {$totalBonus} dari aktivasi pin mitra #{$user->id_mitra}";
                    $keteranganKehilangan = "Kehilangan peluang bonus generasi {$totalBonusJikaQR} dari aktivasi pin mitra #{$user->id_mitra}";
                    $keteranganPembelianBonus = "ID {$sponsor->id_mitra} mendapatkan bonus generasi {$totalBonus} dari aktivasi pin mitra #{$user->id_mitra}";
                    $keteranganPembelianBonusKehilangan = "ID {$sponsor->id_mitra} kehilangan peluang bonus generasi {$totalBonusJikaQR} dari aktivasi pin mitra #{$user->id_mitra}";
                }
            }

            // Buat aktivitas berdasarkan status QR
            if ($statusQr) {
                // Jika status QR true, hanya buat aktivitas bonus generasi
                $activitiesToCreate[] = [
                    'user_id' => $sponsor->id,
                    'judul' => 'Bonus Generasi',
                    'keterangan' => $keterangan,
                    'tipe' => 'plus',
                    'status' => 'Berhasil',
                    'nominal' => $totalBonus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                // Jika status QR false, buat aktivitas bonus generasi dan kehilangan peluang
                if ($totalBonus > 0) {
                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Bonus Generasi',
                        'keterangan' => $keterangan,
                        'tipe' => 'plus',
                        'status' => 'Berhasil',
                        'nominal' => $totalBonus,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Tampilkan kehilangan peluang untuk paket 2 (poin yang hilang) atau paket 1 (bonus yang hilang)
                if ($hasPaket2 || $pembelianDetail->paket == 1) {
                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Kehilangan Peluang',
                        'keterangan' => $keteranganKehilangan,
                        'tipe' => 'minus',
                        'status' => '',
                        'nominal' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            if ($statusQr) {
                $pembelianBonusesToCreate[] = [
                    'pembelian_id' => $pembelian->id,
                    'aktivasi_pin_id' => $aktivasiPin->id,
                    'user_id' => $sponsor->id,
                    'keterangan' => $keteranganPembelianBonus,
                    'tipe' => 'bonus',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                // Untuk paket 2 dengan status_qr false, tidak buat keterangan mendapatkan bonus
                if ($pembelianDetail->paket == 1) {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'aktivasi_pin_id' => $aktivasiPin->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => $keteranganPembelianBonus,
                        'tipe' => 'bonus',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                // Buat pembelian bonus kehilangan untuk paket 2 (poin yang hilang) atau paket 1 (bonus yang hilang)
                if ($hasPaket2 || $pembelianDetail->paket == 1) {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'aktivasi_pin_id' => $aktivasiPin->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => $keteranganPembelianBonusKehilangan,
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
