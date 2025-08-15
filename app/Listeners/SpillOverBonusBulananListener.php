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

            // Cari jaringan mitra dari user tersebut
            $jaringanMitra = JaringanMitra::where('user_id', $userId)->first();

            if (!$jaringanMitra) {
                return;
            }

            // Cari level 9 atau level paling bawah jika kurang dari 9
            $targetLevel = 9;
            $maxLevel = JaringanMitra::max('level');

            if ($maxLevel < 9) {
                $targetLevel = $maxLevel;
            }

            // Cari semua user di level target
            $usersAtTargetLevel = JaringanMitra::where('level', $targetLevel)
                ->with('user:id')
                ->get();

            if ($usersAtTargetLevel->isEmpty()) {
                return;
            }

            // Pilih satu user secara random
            $selectedUser = $usersAtTargetLevel->random();
            $userData = $selectedUser->user;

            // Cari upline 9 level ke atas dari user yang terpilih
            $this->processUplineBonus($userData->id, $pembelian);

        } catch (\Exception $e) {
            // Silent fail - tidak ada log
        }
    }

    /**
     * Proses bonus untuk upline 9 level ke atas
     */
    private function processUplineBonus($selectedUserId, $pembelian): void
    {
        try {
            DB::beginTransaction();

            $user = User::find($selectedUserId);

            // Ambil semua upline dari jaringan mitra (maksimal 9 level)
            $uplines = JaringanMitra::where('user_id', $selectedUserId)
                ->orderBy('level')
                ->limit(9)
                ->get();

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

                    // Kumpulkan aktivitas untuk dibuat nanti
                    if ($statusQr && $totalPoints > 0) {
                        $activitiesToCreate[] = [
                            'user_id' => $sponsor->id,
                            'judul' => 'Poin (spillover)',
                            'keterangan' => "Spillover dari mitra #{$user->id_mitra}",
                            'tipe' => 'plus',
                            'status' => '',
                            'nominal' => $totalPoints,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    $activitiesToCreate[] = [
                        'user_id' => $sponsor->id,
                        'judul' => 'Bonus Generasi (spillover)',
                        'keterangan' => "Spillover dari mitra #{$user->id_mitra}",
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

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
