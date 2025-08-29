<?php

namespace App\Listeners;

use App\Events\BonusGenerasi;
use App\Models\Aktivitas;
use App\Models\JaringanMitra;
use App\Models\PembelianBonus;
use App\Models\User;

class BonusGenerasiListener
{
    public function handle(BonusGenerasi $event)
    {
        $pembelian = $event->pembelian;
        $isMemberAktivasi = $event->isMemberAktivasi;
        $user = $pembelian->user; // assuming relation exists
        // if ($isMemberAktivasi) {
        //     $this->handleMemberAktivasi($pembelian, $user);
        // } else {

        $this->handleRewardBonusGenerasi($pembelian, $user, $isMemberAktivasi);
        // }

    }

    private function handleRewardBonusGenerasi($pembelian, $user, $isMemberAktivasi)
    {

        if ($isMemberAktivasi) {
            // Ambil upline dari jaringan mitra dan tambahkan user sebagai level 0
            $uplines = JaringanMitra::where('user_id', $user->id)
                ->orderBy('level')
                ->limit(10)
                ->get();

        } else {
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

        }

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

            // Hitung total poin dan bonus dari semua pembelian detail
            foreach ($pembelian->details as $detail) {
                // Perulangan berdasarkan quantity pembelian
                for ($i = 0; $i < $detail->jml_beli; $i++) {

                    if ($detail->paket == 2) {
                        $hasPaket2 = true;
                        $totalPoints += 1;

                        $totalBonusJikaQR += 1500;
                        if ($sponsor->status_qr) {
                            $totalBonus += 1500;
                        }
                    } else if ($detail->paket == 1) {
                        $totalBonusJikaQR += 300;
                        // Untuk paket 1, bonus diberikan terlepas dari status QR
                        $totalBonus += 300;
                    }
                }
            }

            // Update sponsor dan siapkan data untuk aktivitas

            $statusQr = $sponsor->status_qr;

            // Update sponsor data
            if ($statusQr) {
                $sponsor->poin_reward += $totalPoints;
            }

            $sponsor->saldo_penghasilan += $totalBonus;

            // Buat Penghasilan record satu persatu
            \App\Models\Penghasilan::create([
                'user_id' => $sponsor->id,
                'kategori_bonus' => 'Bonus Generasi',
                'status_qr' => $statusQr,
                'tgl_dapat_bonus' => now(),
                'keterangan' => "bonus generasi dari mitra #{$user->id_mitra}",
                'nominal_bonus' => $totalBonus,
            ]);

            // Simpan sponsor untuk update batch
            $sponsorsToUpdate[] = $sponsor;

            // Tentukan keterangan berdasarkan status QR dan paket
            if ($statusQr) {
                // Jika status QR true, tidak ada kehilangan peluang
                if ($totalPoints > 0) {
                    $keterangan = "Mendapatkan {$totalPoints} poin dan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}";
                    $keteranganPembelianBonus = "ID {$sponsor->id_mitra} mendapatkan {$totalPoints} poin dan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}";
                } else {
                    $keterangan = "Mendapatkan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}";
                    $keteranganPembelianBonus = "ID {$sponsor->id_mitra} mendapatkan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}";
                }
            } else {
                // Jika status QR false, ada kehilangan peluang
                if ($totalPoints > 0) {
                    $keterangan = "Mendapatkan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}";
                    $keteranganKehilangan = "Kehilangan peluang {$totalPoints} poin dan bonus generasi {$totalBonusJikaQR} dari mitra #{$user->id_mitra}";
                    $keteranganPembelianBonus = "ID {$sponsor->id_mitra} mendapatkan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}";
                    $keteranganPembelianBonusKehilangan = "ID {$sponsor->id_mitra} kehilangan peluang {$totalPoints} poin dan bonus generasi {$totalBonusJikaQR} dari mitra #{$user->id_mitra}";
                } else {
                    $keterangan = "Mendapatkan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}";
                    $keteranganKehilangan = "Kehilangan peluang bonus generasi {$totalBonusJikaQR} dari mitra #{$user->id_mitra}";
                    $keteranganPembelianBonus = "ID {$sponsor->id_mitra} mendapatkan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}";
                    $keteranganPembelianBonusKehilangan = "ID {$sponsor->id_mitra} kehilangan peluang bonus generasi {$totalBonusJikaQR} dari mitra #{$user->id_mitra}";
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

                // Hanya tampilkan kehilangan peluang jika ada paket 2 (poin yang hilang)
                if ($hasPaket2) {
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
                    'user_id' => $sponsor->id,
                    'keterangan' => $keteranganPembelianBonus,
                    'tipe' => 'bonus',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $pembelianBonusesToCreate[] = [
                    'pembelian_id' => $pembelian->id,
                    'user_id' => $sponsor->id,
                    'keterangan' => $keteranganPembelianBonus,
                    'tipe' => 'bonus',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Hanya buat pembelian bonus kehilangan jika ada paket 2 (poin yang hilang)
                if ($hasPaket2) {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
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
