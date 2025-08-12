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
        $this->command->info('🌐 Seeding Jaringan Mitra...');

        // Hapus data jaringan mitra yang ada
        JaringanMitra::truncate();
        $this->command->info('🗑️  Data jaringan mitra lama dihapus');

        // Ambil semua user mitra yang sudah ada
        $mitras = User::where('isStockis', false)
            ->whereNotNull('id_sponsor')
            ->orderBy('id')
            ->get();

        if ($mitras->isEmpty()) {
            $this->command->warn('⚠️  Tidak ada mitra dengan sponsor yang ditemukan. Jalankan MitraSeeder terlebih dahulu.');
            return;
        }

        $this->command->info("📊 Ditemukan {$mitras->count()} mitra dengan sponsor");

        $createdRelationships = 0;

        foreach ($mitras as $mitra) {
            $this->createJaringanMitraRelationships($mitra, $createdRelationships);
        }

        $this->command->info("✅ Berhasil membuat {$createdRelationships} relasi jaringan mitra");

        // Tampilkan statistik
        $this->displayStatistics();
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

                // 3. Buat record untuk setiap upline dengan level bertambah +1
                foreach ($uplines as $upline) {
                    JaringanMitra::create([
                        'user_id' => $mitra->id,
                        'sponsor_id' => $upline->sponsor_id,
                        'level' => $upline->level + 1,
                    ]);
                    $createdRelationships++;
                }
            });

            $this->command->line("   ✅ Mitra {$mitra->nama} (ID: {$mitra->id}) - Relasi dibuat");

        } catch (\Exception $e) {
            $this->command->error("   ❌ Error membuat relasi untuk mitra {$mitra->nama}: " . $e->getMessage());
        }
    }

    /**
     * Menampilkan statistik jaringan mitra
     */
    private function displayStatistics(): void
    {
        $this->command->info("\n📊 Statistik Jaringan Mitra:");

        // Total relasi
        $totalRelationships = JaringanMitra::count();
        $this->command->line("   Total relasi: {$totalRelationships}");

        // Level maksimal
        $maxLevel = JaringanMitra::max('level');
        $this->command->line("   Level maksimal: {$maxLevel}");

        // Distribusi per level
        $levelDistribution = JaringanMitra::selectRaw('level, COUNT(*) as count')
            ->groupBy('level')
            ->orderBy('level')
            ->get();

        $this->command->line("   Distribusi per level:");
        foreach ($levelDistribution as $level) {
            $this->command->line("      Level {$level->level}: {$level->count} relasi");
        }

        // Mitra dengan downline terbanyak
        $topSponsors = JaringanMitra::selectRaw('sponsor_id, COUNT(*) as downline_count')
            ->groupBy('sponsor_id')
            ->orderByDesc('downline_count')
            ->limit(5)
            ->get();

        $this->command->line("   Top 5 Sponsor (berdasarkan jumlah downline):");
        foreach ($topSponsors as $sponsor) {
            $sponsorUser = User::find($sponsor->sponsor_id);
            $sponsorName = $sponsorUser ? $sponsorUser->nama : "User ID {$sponsor->sponsor_id}";
            $this->command->line("      {$sponsorName}: {$sponsor->downline_count} downline");
        }
    }
}
