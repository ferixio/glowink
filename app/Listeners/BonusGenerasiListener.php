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

            // Hitung total poin dan bonus dari semua pembelian detail
            foreach ($pembelian->details as $detail) {
                // Perulangan berdasarkan quantity pembelian
                for ($i = 0; $i < $detail->jml_beli; $i++) {
                    // Setiap quantity menambahkan 1 poin dan bonus
                    $totalPoints += 1;

                    if ($sponsor->status_qr) {
                        $totalBonus += 1500;
                    } else {
                        $totalBonus += 300;
                    }
                }
            }

            // Update sponsor dan siapkan data untuk aktivitas
            if ($totalPoints > 0) {
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

                // Buat 2 aktivitas terpisah: Poin dan Bonus Generasi
                if ($statusQr) {
                    // Aktivitas untuk Poin
                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Poin',
                        'keterangan' => "Mendapatkan {$totalPoints} poin dari mitra #{$user->id_mitra}",
                        'tipe' => 'plus',
                        'status' => 'Berhasil',
                        'nominal' => $totalPoints,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Aktivitas untuk Bonus Generasi
                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Bonus Generasi',
                        'keterangan' => "Mendapatkan bonus generasi {$totalBonus} dari mitra #{$user->id_mitra}",
                        'tipe' => 'plus',
                        'status' => 'Berhasil',
                        'nominal' => $totalBonus,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    // Aktivitas untuk Kehilangan Peluang Poin
                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Kehilangan Peluang Poin',
                        'keterangan' => "Kehilangan peluang {$totalPoints} poin dari member #{$user->id_mitra}",
                        'tipe' => 'minus',
                        'status' => '',
                        'nominal' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Aktivitas untuk Kehilangan Peluang Bonus Generasi
                    // $activitiesToCreate[] = [
                    //     'user_id' => $sponsor->id,
                    //     'judul' => 'Kehilangan Peluang Bonus Generasi',
                    //     'keterangan' => "Kehilangan peluang bonus generasi {$totalBonus} dari member #{$user->id_mitra}",
                    //     'tipe' => 'minus',
                    //     'status' => '',
                    //     'nominal' => null,
                    //     'created_at' => now(),
                    //     'updated_at' => now(),
                    // ];
                }

                // Kumpulkan PembelianBonus untuk dibuat nanti
                $idMitra = $sponsor->id_mitra ?? 'Unknown';
                $point = $statusQr ? $totalPoints : 0;
                $nominalPembelianBonus = $statusQr ? 1500 : 300;

                if ($statusQr) {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => "ID {$idMitra} mendapatkan {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
                        'tipe' => 'bonus',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => "ID {$idMitra} mendapatkan BONUS GENERASI {$nominalPembelianBonus}",
                        'tipe' => 'bonus',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => "ID {$idMitra} kehilangan peluang {$point} point  ",
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

    // private function handleMemberAktivasi($pembelian, $user)
    // {
    //     // Ambil semua upline dari jaringan mitra (maksimal 10 level)
    //     $uplines = JaringanMitra::where('user_id', $user->id)
    //         ->orderBy('level')
    //         ->limit(10)
    //         ->get();

    //     // Temporary variables untuk mengumpulkan data
    //     $activitiesToCreate = [];
    //     $pembelianBonusesToCreate = [];
    //     $sponsorsToUpdate = [];

    //     foreach ($uplines as $upline) {
    //         $sponsor = User::find($upline->sponsor_id);
    //         if (!$sponsor) {
    //             continue;
    //         }

    //         $statusQr = $sponsor->status_qr;
    //         $nominalBonus = $statusQr ? 1500 : 300;
    //         $point = 1;

    //         if ($statusQr) {
    //             Aktivitas::create([
    //                 'user_id' => $sponsor->id,
    //                 'judul' => 'Poin',
    //                 'keterangan' => "",
    //                 'tipe' => 'plus',
    //                 'status' => '',
    //                 'nominal' => 1,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         } else {
    //             Aktivitas::create([
    //                 'user_id' => $sponsor->id,
    //                 'judul' => 'Kehilangan Peluang',
    //                 'keterangan' => "Kehilangan peluang poin dari member #{$user->id_mitra}",
    //                 'tipe' => 'minus',
    //                 'status' => '',
    //                 'nominal' => null,
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ]);
    //         }

    //         // Update sponsor data
    //         $sponsor->saldo_penghasilan += $nominalBonus;
    //         if ($statusQr) {
    //             $sponsor->poin_reward += $point;
    //         }

    //         // Buat Penghasilan record satu persatu
    //         \App\Models\Penghasilan::create([
    //             'user_id' => $sponsor->id,
    //             'kategori_bonus' => 'Bonus Generasi',
    //             'status_qr' => $statusQr,
    //             'tgl_dapat_bonus' => now(),
    //             'keterangan' => "bonus generasi level {$upline->level}",
    //             'nominal_bonus' => $nominalBonus,
    //         ]);

    //         // Simpan sponsor untuk update batch
    //         $sponsorsToUpdate[] = $sponsor;

    //         // Kumpulkan aktivitas untuk dibuat nanti
    //         $activitiesToCreate[] = [
    //             'user_id' => $sponsor->id,
    //             'judul' => 'Bonus Generasi Diterima',
    //             'keterangan' => "Menerima bonus generasi level {$upline->level} dari member #{$user->id_mitra}",
    //             'tipe' => 'plus',
    //             'status' => 'Berhasil',
    //             'nominal' => $nominalBonus,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ];

    //         // Kumpulkan PembelianBonus untuk dibuat nanti
    //         $idMitra = $sponsor->id_mitra ?? 'Unknown';
    //         $nominalPembelianBonus = $statusQr ? 1500 : 300;

    //         if ($statusQr) {
    //             $pembelianBonusesToCreate[] = [
    //                 'pembelian_id' => $pembelian->id,
    //                 'user_id' => $sponsor->id,
    //                 'keterangan' => "ID {$idMitra} mendapatkan {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         } else {
    //             $pembelianBonusesToCreate[] = [
    //                 'pembelian_id' => $pembelian->id,
    //                 'user_id' => $sponsor->id,
    //                 'keterangan' => "ID {$idMitra} kehilangan peluang {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
    //                 'created_at' => now(),
    //                 'updated_at' => now(),
    //             ];
    //         }
    //     }

    //     // Update semua sponsor sekaligus
    //     foreach ($sponsorsToUpdate as $sponsor) {
    //         $sponsor->save();
    //         if ($sponsor->poin_reward > 0) {
    //             event(new \App\Events\ChangeLevelUser($sponsor, $sponsor->poin_reward));
    //         }
    //     }

    //     // Buat semua aktivitas sekaligus
    //     if (!empty($activitiesToCreate)) {
    //         Aktivitas::insert($activitiesToCreate);
    //     }

    //     // Buat semua PembelianBonus sekaligus
    //     if (!empty($pembelianBonusesToCreate)) {
    //         PembelianBonus::insert($pembelianBonusesToCreate);
    //     }
    // }

}
