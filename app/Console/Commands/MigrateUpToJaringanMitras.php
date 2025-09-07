<?php

namespace App\Console\Commands;

use App\Models\JaringanMitra;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MigrateUpToJaringanMitras extends Command
{
    protected $signature = 'migrate:up-to-jaringan';
    protected $description = 'Migrasi tabel up lama ke tabel jaringan_mitras baru dengan perhitungan level';

    public function handle()
    {
        $this->info('Memulai migrasi data dari tabel up ke jaringan_mitras...');

        DB::transaction(function () {
            // Step 1: Ambil semua data dari tabel up
            $oldUps = DB::connection('mysql_old')
                ->table('up')
                ->whereNotNull('id_mem')
                ->whereNotNull('id_sponsor')
                ->where('id_mem', '!=', '')
                ->where('id_sponsor', '!=', '')
                ->get();

            $this->info('Total data up yang akan dimigrasi: ' . $oldUps->count());

            // Step 2: Normalisasi username (hilangkan suffix -1, -2, dll)
            $normalizedUps = [];
            foreach ($oldUps as $up) {
                $normalizedMem = $this->normalizeUsername($up->id_mem);
                $normalizedSponsor = $this->normalizeUsername($up->id_sponsor);

                if ($normalizedMem && $normalizedSponsor) {
                    $normalizedUps[] = (object) [
                        'id_mem' => $normalizedMem,
                        'id_sponsor' => $normalizedSponsor,
                        'original_mem' => $up->id_mem,
                        'original_sponsor' => $up->id_sponsor,
                    ];
                }
            }

            $this->info('Total data setelah normalisasi: ' . count($normalizedUps));

            // Debug: Tampilkan beberapa sample data
            if (count($normalizedUps) > 0) {
                $this->info('Sample data up (setelah normalisasi):');
                $sample = array_slice($normalizedUps, 0, 3);
                foreach ($sample as $up) {
                    $this->info("id_mem: {$up->id_mem} (original: {$up->original_mem}), id_sponsor: {$up->id_sponsor} (original: {$up->original_sponsor})");
                }
            } else {
                $this->warn('Tidak ada data ditemukan di tabel up!');
                return 0;
            }

            // Step 3: Buat mapping username ke user_id
            $usernameToIdMapping = $this->createUsernameToIdMapping();

            // Step 4: Buat semua relasi jaringan (direct sponsor + semua level di atasnya)
            $jaringanData = [];
            $skipped = 0;
            $processed = 0;
            $duplicates = 0;

            // Set untuk tracking kombinasi yang sudah diproses dalam batch ini
            $processedCombinations = [];

            // Buat mapping dari normalizedUps untuk pencarian yang lebih cepat
            $upMapping = [];
            foreach ($normalizedUps as $up) {
                $upMapping[$up->id_mem] = $up->id_sponsor;
            }

            // Untuk setiap relasi direct di tabel up, buat relasi lengkap
            foreach ($normalizedUps as $up) {
                $userId = $usernameToIdMapping[$up->id_mem] ?? null;
                $sponsorId = $usernameToIdMapping[$up->id_sponsor] ?? null;

                if (!$userId || !$sponsorId) {
                    $this->warn("Data dilewati karena user tidak ditemukan: id_mem={$up->id_mem}, id_sponsor={$up->id_sponsor}");
                    $skipped++;
                    continue;
                }

                // Tambahkan relasi direct (level 1)
                $this->addRelationIfNotExists($userId, $sponsorId, 1, $jaringanData, $processedCombinations, $processed, $duplicates);

                // Cari semua sponsor di atas sponsor ini (level 2, 3, dst)
                $this->addAllUpperSponsors($up->id_sponsor, $userId, $upMapping, $usernameToIdMapping, $jaringanData, $processedCombinations, $processed, $duplicates);
            }

            // Step 5: Insert data dalam batch
            if (!empty($jaringanData)) {
                $this->info('Memasukkan data ke tabel jaringan_mitras...');

                // Insert dalam batch untuk performa yang lebih baik
                $chunks = array_chunk($jaringanData, 1000);
                foreach ($chunks as $chunk) {
                    JaringanMitra::insert($chunk);
                }

                $this->info("Migrasi selesai!");
                $this->info("Total data berhasil dimigrasi: {$processed}");
                $this->info("Data dilewati (user tidak ditemukan): {$skipped}");
                $this->info("Data duplikasi: {$duplicates}");
            } else {
                $this->warn("Tidak ada data yang dimigrasi.");
            }
        });

        return 0;
    }

    /**
     * Buat mapping username ke user_id dari tabel users
     */
    private function createUsernameToIdMapping()
    {
        $this->info('Membuat mapping username ke user_id...');

        $users = User::select('id', 'id_mitra')->get();
        $mapping = [];

        foreach ($users as $user) {
            if ($user->id_mitra) {
                $mapping[$user->id_mitra] = $user->id;
            }
        }

        $this->info('Mapping selesai. Total user: ' . count($mapping));
        return $mapping;
    }

    /**
     * Normalisasi username dengan menghilangkan suffix -1, -2, dll
     */
    private function normalizeUsername($username)
    {
        if (!$username) {
            return null;
        }

        // Hilangkan suffix -1, -2, -3, dst
        return preg_replace('/-\d+$/', '', $username);
    }

    /**
     * Tambahkan relasi jika belum ada
     */
    private function addRelationIfNotExists($userId, $sponsorId, $level, &$jaringanData, &$processedCombinations, &$processed, &$duplicates)
    {
        $combinationKey = "{$userId}-{$sponsorId}-{$level}";

        if (isset($processedCombinations[$combinationKey])) {
            $duplicates++;
            return;
        }

        // Cek apakah relasi sudah ada di database
        $existingRelation = JaringanMitra::where('user_id', $userId)
            ->where('sponsor_id', $sponsorId)
            ->where('level', $level)
            ->first();

        if (!$existingRelation) {
            $jaringanData[] = [
                'user_id' => $userId,
                'sponsor_id' => $sponsorId,
                'level' => $level,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            $processedCombinations[$combinationKey] = true;
            $processed++;
        } else {
            $duplicates++;
        }
    }

    /**
     * Tambahkan semua sponsor di atas sponsor tertentu
     */
    private function addAllUpperSponsors($sponsorUsername, $userId, $upMapping, $usernameToIdMapping, &$jaringanData, &$processedCombinations, &$processed, &$duplicates)
    {
        $currentSponsor = $sponsorUsername;
        $level = 1;
        $visited = [];

        // Cari semua sponsor di atas sponsor ini
        while (isset($upMapping[$currentSponsor])) {
            $upperSponsorUsername = $upMapping[$currentSponsor];
            $upperSponsorId = $usernameToIdMapping[$upperSponsorUsername] ?? null;

            if (!$upperSponsorId) {
                break;
            }

            // Cek untuk mencegah loop
            if (in_array($upperSponsorUsername, $visited)) {
                $this->warn("Loop terdeteksi untuk sponsor: {$sponsorUsername}, upper sponsor: {$upperSponsorUsername}");
                break;
            }

            $visited[] = $upperSponsorUsername;
            $level++;

            // Tambahkan relasi
            $this->addRelationIfNotExists($userId, $upperSponsorId, $level, $jaringanData, $processedCombinations, $processed, $duplicates);

            $currentSponsor = $upperSponsorUsername;

            // Batasi level maksimal untuk mencegah infinite loop
            if ($level > 50) {
                $this->warn("Level maksimal tercapai untuk sponsor: {$sponsorUsername}");
                break;
            }
        }
    }
}
