<?php

namespace App\Listeners;

use App\Events\SpillOverBonusBulanan;
use App\Models\Aktivitas;
use App\Models\JaringanMitra;
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
                ->with('user:id,nama,email,no_telp')
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

            // Ambil semua upline dari jaringan mitra (maksimal 9 level)
            $uplines = JaringanMitra::where('user_id', $selectedUserId)
                ->orderBy('level')
                ->limit(9)
                ->get();

            foreach ($uplines as $upline) {
                $sponsor = User::find($upline->sponsor_id);
                if (!$sponsor || !$sponsor->status_qr) {
                    continue;
                }

                foreach ($pembelian->details as $detail) {
                    if ($detail->paket == 2) {
                        $sponsor->saldo_penghasilan += 1500;
                        $sponsor->poin_reward += 1;
                        $sponsor->save();

                        event(new \App\Events\ChangeLevelUser($sponsor, $sponsor->poin_reward));

                        Aktivitas::create([
                            'user_id' => $sponsor->id,
                            'judul' => 'Poin',
                            'keterangan' => "",
                            'tipe' => 'plus',
                            'status' => '',
                            'nominal' => 1,
                        ]);
                        Aktivitas::create([
                            'user_id' => $sponsor->id,
                            'judul' => 'Bonus Generasi',
                            'keterangan' => "",
                            'tipe' => 'plus',
                            'status' => '',
                            'nominal' => 1500,
                        ]);

                        break;
                    }
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
