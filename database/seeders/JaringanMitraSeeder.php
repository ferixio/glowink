<?php

namespace Database\Seeders;

use App\Models\JaringanMitra;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JaringanMitraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Hapus data jaringan mitra yang ada
        JaringanMitra::truncate();

        // Ambil semua user mitra yang sudah ada
        $mitras = User::where('isStockis', false)
            ->whereNotNull('id_sponsor')
            ->orderBy('id')
            ->get();

        if ($mitras->isEmpty()) {
            return;
        }

        $createdRelationships = 0;

        foreach ($mitras as $mitra) {
            $this->createJaringanMitraRelationships($mitra, $createdRelationships);
        }

        // Buat downline tambahan untuk mitra1 agar memiliki multiple members di beberapa level
        $this->createAdditionalDownlineForMitra1();
    }

    /**
     * Membuat relasi jaringan mitra untuk satu mitra
     */
    private function createJaringanMitraRelationships(User $mitra, int &$createdRelationships): void
    {
        $sponsorId = $mitra->id_sponsor;

        if (!$sponsorId) {
            return;
        }

        try {
            DB::transaction(function () use ($mitra, $sponsorId, &$createdRelationships) {
                // 1. Buat relasi level 1 (sponsor langsung)
                JaringanMitra::create([
                    'user_id' => $mitra->id,
                    'sponsor_id' => $sponsorId,
                    'level' => 1,
                ]);
                $createdRelationships++;

                // 2. Ambil semua upline dari sponsor langsung
                $uplines = JaringanMitra::where('user_id', $sponsorId)->get();

                // 3. Buat record untuk setiap upline dengan level bertambah +1 (maksimal 9)
                foreach ($uplines as $upline) {
                    $newLevel = $upline->level + 1;
                    if ($newLevel <= 9) { // Maksimal 9 level
                        JaringanMitra::create([
                            'user_id' => $mitra->id,
                            'sponsor_id' => $upline->sponsor_id,
                            'level' => $newLevel,
                        ]);
                        $createdRelationships++;
                    }
                }
            });

        } catch (\Exception $e) {
            // Silent error handling
        }
    }

    /**
     * Membuat downline tambahan untuk mitra1 agar memiliki multiple members di beberapa level
     */
    private function createAdditionalDownlineForMitra1(): void
    {
        $mitra1 = User::where('id', 1)->first();
        if (!$mitra1) {
            return;
        }

        // Buat beberapa downline tambahan untuk mitra1 di level 2
        $this->createDownlineAtLevel($mitra1->id, 2, 5);

        // Buat beberapa downline tambahan untuk mitra1 di level 3
        $this->createDownlineAtLevel($mitra1->id, 3, 4);

        // Buat beberapa downline tambahan untuk mitra1 di level 4
        $this->createDownlineAtLevel($mitra1->id, 4, 3);

        // Buat beberapa downline tambahan untuk mitra1 di level 5
        $this->createDownlineAtLevel($mitra1->id, 5, 3);

        // Buat beberapa downline tambahan untuk mitra1 di level 6
        $this->createDownlineAtLevel($mitra1->id, 6, 2);

        // Buat beberapa downline tambahan untuk mitra1 di level 7
        $this->createDownlineAtLevel($mitra1->id, 7, 2);

        // Buat beberapa downline tambahan untuk mitra1 di level 8
        $this->createDownlineAtLevel($mitra1->id, 8, 2);

        // Buat beberapa downline tambahan untuk mitra1 di level 9
        $this->createDownlineAtLevel($mitra1->id, 9, 1);
    }

    /**
     * Membuat downline di level tertentu untuk sponsor tertentu
     */
    private function createDownlineAtLevel(int $sponsorId, int $level, int $count): void
    {
        // Ambil user yang sudah ada untuk dijadikan downline
        $availableUsers = User::where('isStockis', false)
            ->where('id', '!=', $sponsorId)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('jaringan_mitras')
                    ->whereRaw('jaringan_mitras.user_id = users.id');
            })
            ->limit($count)
            ->get();

        foreach ($availableUsers as $user) {
            // Buat relasi langsung dengan sponsor
            JaringanMitra::create([
                'user_id' => $user->id,
                'sponsor_id' => $sponsorId,
                'level' => $level,
            ]);

            // Buat relasi untuk level-level di atasnya (jika ada)
            if ($level < 9) {
                $this->createUplineRelationships($user->id, $sponsorId, $level);
            }
        }
    }

    /**
     * Membuat relasi upline untuk user yang baru ditambahkan
     */
    private function createUplineRelationships(int $userId, int $sponsorId, int $currentLevel): void
    {
        // Ambil semua upline dari sponsor
        $uplines = JaringanMitra::where('user_id', $sponsorId)->get();

        foreach ($uplines as $upline) {
            $newLevel = $upline->level + $currentLevel;
            if ($newLevel <= 9) { // Maksimal 9 level
                // Cek apakah relasi sudah ada
                $existingRelation = JaringanMitra::where('user_id', $userId)
                    ->where('sponsor_id', $upline->sponsor_id)
                    ->where('level', $newLevel)
                    ->first();

                if (!$existingRelation) {
                    JaringanMitra::create([
                        'user_id' => $userId,
                        'sponsor_id' => $upline->sponsor_id,
                        'level' => $newLevel,
                    ]);
                }
            }
        }
    }
}
