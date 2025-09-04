<?php

namespace App\Listeners;

use App\Events\SpillOverBonusBulanan;
use App\Models\Aktivitas;
use App\Models\JaringanMitra;
use App\Models\PembelianBonus;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class SpillOverBonusBulananListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SpillOverBonusBulanan $event): void
    {
        try {
            $userId = $event->user_id ?? null;

            $pembelian = $event->pembelian ?? null;

            if (!$userId || !$pembelian) {
                return;
            }

            // Tambahkan Bonus Cashback (RO Bulanan hanya paket 2)
            try {
                $user = $pembelian->user ?? \App\Models\User::find($userId);
                if ($user) {
                    $statusQr = $user->status_qr;
                    $idMitra = $user->id_mitra;

                    // Formatter Rupiah
                    $format = function ($number) {
                        return 'Rp. ' . number_format((float) $number, 0, ',', '.');
                    };

                    $totalBonusCashback = 0;

                    foreach ($pembelian->details as $detail) {
                        $quantity = (int) ($detail->jml_beli ?? 1);
                        // Hanya paket 2 untuk RO Bulanan
                        $bonusPerUnit = 20000;
                        $keterangan = 'Bonus Cashback QR aktif';

                        $bonusAmount = $bonusPerUnit * $quantity;
                        $totalBonusCashback += $bonusAmount;

                        // Penghasilan untuk user
                        \App\Models\Penghasilan::create([
                            'user_id' => $user->id,
                            'kategori_bonus' => 'Bonus Cashback dari RO Bulanan',
                            'status_qr' => $statusQr,
                            'tgl_dapat_bonus' => now(),
                            'keterangan' => $keterangan,
                            'nominal_bonus' => $bonusAmount,
                        ]);

                        // Aktivitas untuk user
                        Aktivitas::create([
                            'user_id' => $user->id,
                            'judul' => 'Bonus Cashback',
                            'keterangan' => "Menerima {$keterangan} {$format($bonusAmount)} dari RO Bulanan",
                            'tipe' => 'plus',
                            'status' => 'Berhasil',
                            'nominal' => $bonusAmount,
                        ]);

                        // Log PembelianBonus
                        PembelianBonus::create([
                            'pembelian_id' => $pembelian->id,
                            'user_id' => $user->id,
                            'keterangan' => "ID {$idMitra} mendapatkan bonus cashback {$format($bonusAmount)} dari RO Bulanan",
                            'tipe' => 'bonus',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }

                    // Update saldo penghasilan user
                    if ($totalBonusCashback > 0) {
                        $user->saldo_penghasilan += $totalBonusCashback;
                        $user->save();
                    }
                }
            } catch (\Exception $e) {
                // Abaikan error cashback agar tidak mengganggu proses spillover
            }

            // Cari jaringan mitra dari user tersebut
            $jaringanMitra = JaringanMitra::where('user_id', $userId)->first();

            if (!$jaringanMitra) {
                return;
            }

            // Cari level 9 atau level paling bawah dari jaringan user ini
            $targetLevel = 9;
            $maxLevel = JaringanMitra::where('sponsor_id', $userId)->max('level');

            if ($maxLevel && $maxLevel < 9) {
                $targetLevel = $maxLevel;
            }

            // Cari semua user di level target dari jaringan user ini
            $usersAtTargetLevel = JaringanMitra::where('level', $targetLevel)
                ->where('sponsor_id', $userId)
                ->with('user:id')
                ->get();

            if ($usersAtTargetLevel->isEmpty()) {
                return;
            }

            // Pilih satu user secara random
            $selectedUser = $usersAtTargetLevel->random();
            $userData = $selectedUser->user;

            // Cari upline 9 level ke atas dari user yang terpilih
            $this->processUplineBonus($userData->id, $pembelian, $userId);

        } catch (\Exception $e) {
            // Silent fail - tidak ada log
        }
    }

    /**
     * Proses bonus untuk upline 9 level ke atas
     */
    private function processUplineBonus($selectedUserId, $pembelian, $userId): void
    {
        try {
            DB::beginTransaction();

            $user = User::find($selectedUserId);
            $userWhoROBulanan = User::find($userId)->id_mitra;

            // Ambil semua upline dari jaringan mitra (maksimal 9 level)
            $uplines = JaringanMitra::where('user_id', $selectedUserId)
                ->orderBy('level')
                ->limit(9)
                ->get();

            // Tambahkan selectedUserId ke dalam uplines agar mendapatkan bonus yang sama
            $selectedUserUpline = (object) [
                'sponsor_id' => $selectedUserId,
                'level' => 0, // Level 0 untuk user yang terpilih sendiri
            ];
            $uplines->prepend($selectedUserUpline);

            // proses bonus generasi mengikuti skema pada BonusGenerasiListener

            $activitiesToCreate = [];
            $pembelianBonusesToCreate = [];
            $sponsorsToUpdate = [];

            foreach ($uplines as $upline) {
                $sponsor = User::find($upline->sponsor_id);
                if (!$sponsor) {
                    continue;
                }

                // Formatter Rupiah
                $format = function ($number) {
                    return 'Rp. ' . number_format((float) $number, 0, ',', '.');
                };

                $potentialPoints = 0;
                $potentialBonus = 0;

                // Hitung total poin dan bonus POTENSIAL dari semua pembelian detail
                foreach ($pembelian->details as $detail) {
                    // Perulangan berdasarkan quantity pembelian
                    for ($i = 0; $i < $detail->jml_beli; $i++) {
                        if ($detail->paket == 2) {
                            $potentialPoints += 1;
                            $potentialBonus += 1500;
                        } else if ($detail->paket == 1) {
                            $potentialBonus += 300;
                        }
                    }
                }

                $statusQr = $sponsor->status_qr;

                if ($statusQr) {
                    $awardedPoints = $potentialPoints;
                    $awardedBonus = $potentialBonus;

                    // Update sponsor data
                    if ($awardedPoints > 0) {
                        $sponsor->poin_reward += $awardedPoints;
                    }
                    if ($awardedBonus > 0) {
                        $sponsor->saldo_penghasilan += $awardedBonus;
                    }

                    // Buat Penghasilan record satu persatu
                    \App\Models\Penghasilan::create([
                        'user_id' => $sponsor->id,
                        'kategori_bonus' => 'Bonus Generasi',
                        'status_qr' => $statusQr,
                        'tgl_dapat_bonus' => now(),
                        'keterangan' => "bonus generasi dari mitra #{$userWhoROBulanan} dari RO Bulanan",
                        'nominal_bonus' => $awardedBonus,
                    ]);

                    // Simpan sponsor untuk update batch
                    $sponsorsToUpdate[] = $sponsor;

                    // Aktivitas Poin (jika ada)
                    if ($awardedPoints > 0) {
                        $activitiesToCreate[] = [
                            'user_id' => $sponsor->id,
                            'judul' => 'Poin',
                            'keterangan' => "Mendapatkan {$awardedPoints} poin dari mitra #{$userWhoROBulanan} dari RO Bulanan",
                            'tipe' => 'plus',
                            'status' => 'Berhasil',
                            'nominal' => $awardedPoints,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Aktivitas Bonus Generasi (jika ada)
                    if ($awardedBonus > 0) {
                        $activitiesToCreate[] = [
                            'user_id' => $sponsor->id,
                            'judul' => 'Bonus Generasi',
                            'keterangan' => "Mendapatkan bonus generasi {$format($awardedBonus)} dari mitra #{$userWhoROBulanan} dari RO Bulanan",
                            'tipe' => 'plus',
                            'status' => 'Berhasil',
                            'nominal' => $awardedBonus,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Kumpulkan PembelianBonus untuk dibuat nanti
                    $idMitra = $sponsor->id_mitra ?? 'Unknown';
                    if ($awardedBonus > 0) {
                        $pembelianBonusesToCreate[] = [
                            'pembelian_id' => $pembelian->id,
                            'user_id' => $sponsor->id,
                            'keterangan' => "ID {$idMitra} mendapatkan {$awardedPoints} point dan BONUS GENERASI {$format($awardedBonus)} dari RO Bulanan",
                            'tipe' => 'bonus',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                } else {
                    // status_qr false: TIDAK mendapatkan poin dan bonus; tampilkan kehilangan peluang total
                    $lostPoints = $potentialPoints;
                    $lostBonus = $potentialBonus;

                    // Aktivitas Kehilangan peluang (jika ada yang hilang)
                    if ($lostPoints > 0 || $lostBonus > 0) {
                        $activitiesToCreate[] = [
                            'user_id' => $sponsor->id,
                            'judul' => 'Kehilangan Peluang',
                            'keterangan' => "Kehilangan peluang {$lostPoints} poin dan bonus generasi {$format($lostBonus)} dari RO Bulanan",
                            'tipe' => 'minus',
                            'status' => '',
                            'nominal' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        // PembelianBonus kehilangan
                        $idMitra = $sponsor->id_mitra ?? 'Unknown';
                        $pembelianBonusesToCreate[] = [
                            'pembelian_id' => $pembelian->id,
                            'user_id' => $sponsor->id,
                            'keterangan' => "ID {$idMitra} kehilangan peluang {$lostPoints} point dan BONUS GENERASI {$format($lostBonus)} dari RO Bulanan",
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

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
