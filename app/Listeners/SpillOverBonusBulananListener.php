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

                $totalPoints = 0;
                $totalBonus = 0;

                // Hitung total poin dan bonus dari semua pembelian detail
                foreach ($pembelian->details as $detail) {
                    // Perulangan berdasarkan quantity pembelian
                    for ($i = 0; $i < $detail->jml_beli; $i++) {
                        // Setiap quantity menambahkan 1 poin dan bonus
                        // Hanya menambahkan poin jika paket bernilai 2
                        if ($detail->paket == 2) {
                            $totalPoints += 1;
                        }

                        if ($sponsor->status_qr && $detail->paket == 2) {
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
                        'keterangan' => "bonus generasi dari mitra #{$userWhoROBulanan} dari RO Bulanan",
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
                            'keterangan' => "Mendapatkan {$totalPoints} poin dari mitra #{$userWhoROBulanan} dari RO Bulanan",
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
                            'keterangan' => "Mendapatkan bonus generasi {$totalBonus} dari mitra #{$userWhoROBulanan} dari RO Bulanan",
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
                            'keterangan' => "Kehilangan peluang {$totalPoints} poin dari member #{$userWhoROBulanan} dari RO Bulanan",
                            'tipe' => 'minus',
                            'status' => '',
                            'nominal' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $activitiesToCreate[] = [
                            'user_id' => $sponsor->id,
                            'judul' => 'Bonus Generasi',
                            'keterangan' => "Mendapatkan bonus generasi {$totalBonus} dari mitra #{$userWhoROBulanan} dari RO Bulanan",
                            'tipe' => 'plus',
                            'status' => 'Berhasil',
                            'nominal' => $totalBonus,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }

                    // Kumpulkan PembelianBonus untuk dibuat nanti
                    $idMitra = $sponsor->id_mitra ?? 'Unknown';
                    $point = 1;
                    $nominalPembelianBonus = $statusQr ? 1500 : 300;

                    if ($statusQr) {
                        $pembelianBonusesToCreate[] = [
                            'pembelian_id' => $pembelian->id,
                            'user_id' => $sponsor->id,
                            'keterangan' => "ID {$idMitra} mendapatkan {$point} point dan BONUS GENERASI {$nominalPembelianBonus} dari RO Bulanan",
                            'tipe' => 'bonus',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    } else {
                        $pembelianBonusesToCreate[] = [
                            'pembelian_id' => $pembelian->id,
                            'user_id' => $sponsor->id,
                            'keterangan' => "ID {$idMitra} mendapatkan BONUS GENERASI {$nominalPembelianBonus} dari RO Bulanan",
                            'tipe' => 'bonus',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $pembelianBonusesToCreate[] = [
                            'pembelian_id' => $pembelian->id,
                            'user_id' => $sponsor->id,
                            'keterangan' => "ID {$idMitra} kehilangan peluang {$point} point dari RO Bulanan",
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
