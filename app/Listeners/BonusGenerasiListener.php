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
        if ($isMemberAktivasi) {
            $this->handleMemberAktivasi($pembelian, $user);
        } else {

            $this->handleRewardBonusGenerasi($pembelian, $user);
        }

    }

    private function handleMemberAktivasi($pembelian, $user)
    {
        // Ambil semua upline dari jaringan mitra (maksimal 10 level)
        $uplines = JaringanMitra::where('user_id', $user->id)
            ->orderBy('level')
            ->limit(10)
            ->get();

        // Temporary variables untuk mengumpulkan data
        $activitiesToCreate = [];
        $pembelianBonusesToCreate = [];
        $penghasilansToCreate = [];
        $sponsorsToUpdate = [];

        foreach ($uplines as $upline) {
            $sponsor = User::find($upline->sponsor_id);
            if (!$sponsor) {
                continue;
            }

            $statusQr = $sponsor->status_qr;
            $nominalBonus = $statusQr ? 1500 : 300;
            $point = 3; 
            
            Aktivitas::create([
                'user_id' => $sponsor->id,
                'judul' => 'Poin',
                'keterangan' => "",
                'tipe' => 'plus',
                'status' => '',
                'nominal' => 3,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update sponsor data
            $sponsor->saldo_penghasilan += $nominalBonus;
            $sponsor->poin_reward += $point;

            // Simpan sponsor untuk update batch
            $sponsorsToUpdate[] = $sponsor;

            // Kumpulkan Penghasilan untuk dibuat nanti
            $penghasilansToCreate[] = [
                'user_id' => $sponsor->id,
                'kategori_bonus' => 'Bonus Generasi',
                'status_qr' => $statusQr,
                'tgl_dapat_bonus' => now(),
                'keterangan' => "bonus generasi level {$upline->level}",
                'nominal_bonus' => $nominalBonus,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Kumpulkan aktivitas untuk dibuat nanti
            $activitiesToCreate[] = [
                'user_id' => $sponsor->id,
                'judul' => 'Bonus Generasi Diterima',
                'keterangan' => "Menerima bonus generasi level {$upline->level} dari member #{$user->id_mitra}",
                'tipe' => 'plus',
                'status' => 'Berhasil',
                'nominal' => $nominalBonus,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // Kumpulkan PembelianBonus untuk dibuat nanti
            $idMitra = $user->id_mitra ?? 'Unknown';
            $nominalPembelianBonus = $statusQr ? 1500 : 300;

            if ($statusQr) {
                $pembelianBonusesToCreate[] = [
                    'pembelian_id' => $pembelian->id,
                    'user_id' => $sponsor->id,
                    'keterangan' => "ID {$idMitra} mendapatkan {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            } else {
                $pembelianBonusesToCreate[] = [
                    'pembelian_id' => $pembelian->id,
                    'user_id' => $sponsor->id,
                    'keterangan' => "ID {$idMitra} kehilangan peluang {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Update semua sponsor sekaligus
        foreach ($sponsorsToUpdate as $sponsor) {
            $sponsor->save();
            if ($sponsor->poin_reward > 0) {
                event(new \App\Events\ChangeLevelUser($sponsor, $sponsor->poin_reward));
            }
        }

        // Buat semua Penghasilan sekaligus
        if (!empty($penghasilansToCreate)) {
            \App\Models\Penghasilan::insert($penghasilansToCreate);
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

    private function handleRewardBonusGenerasi($pembelian, $user)
    {
        // Ambil semua upline dari jaringan mitra (maksimal 10 level)
        $uplines = JaringanMitra::where('user_id', $user->id)
            ->orderBy('level')
            ->limit(10)
            ->get();

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
            $hasPaket2 = false;

            foreach ($pembelian->details as $detail) {
                if ($detail->paket == 2) {
                    $isMemberQR = true;
                    $hasPaket2 = true;
                    $statusQr = $sponsor->status_qr;

                    if ($statusQr) {
                        $totalPoints += 1;
                        $totalBonus += 1500;
                    } else {
                        $totalBonus += 300;
                    }
                }
            }

            // Jika ada paket 2, update sponsor dan siapkan data untuk aktivitas
            if ($hasPaket2) {
                $statusQr = $sponsor->status_qr;

                // Update sponsor data
                if ($statusQr) {
                    $sponsor->poin_reward += $totalPoints;
                }

                $nominalBonus = $statusQr ? 1500 : 300;
                $sponsor->saldo_penghasilan += $totalBonus;

                // Simpan sponsor untuk update batch
                $sponsorsToUpdate[] = $sponsor;

                if ($statusQr && $totalPoints > 0) {
                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Poin',
                        'keterangan' => "dari mitra #{$user->id_mitra}",
                        'tipe' => 'plus',
                        'status' => '',
                        'nominal' => $totalPoints,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $activitiesToCreate[] = [
                    'user_id' => $sponsor->id,
                    'judul' => 'Bonus Generasi',
                    'keterangan' => "dari mitra #{$user->id_mitra}",
                    'tipe' => 'plus',
                    'status' => '',
                    'nominal' => $totalBonus,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Kumpulkan PembelianBonus untuk dibuat nanti
                $idMitra = $user->id_mitra ?? 'Unknown';
                $point = $statusQr ? $totalPoints : 0;
                $nominalPembelianBonus = $statusQr ? 1500 : 300;

                if ($statusQr) {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => "ID {$idMitra} mendapatkan {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                } else {
                    $pembelianBonusesToCreate[] = [
                        'pembelian_id' => $pembelian->id,
                        'user_id' => $sponsor->id,
                        'keterangan' => "ID {$idMitra} kehilangan peluang {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
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
