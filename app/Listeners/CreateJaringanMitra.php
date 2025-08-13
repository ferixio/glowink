<?php

namespace App\Listeners;

use App\Events\UserCreated;
use App\Models\JaringanMitra;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreateJaringanMitra implements ShouldQueue
{
    use InteractsWithQueue;

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
    public function handle(UserCreated $event): void
    {
        $userBaru = $event->user;
        $sponsorId = $event->sponsorId;

        // Jika tidak ada sponsor, tidak perlu membuat jaringan mitra
        if (!$sponsorId) {
            return;
        }

        try {
            DB::transaction(function () use ($userBaru, $sponsorId) {
                // 1. Insert sponsor langsung (level 1)
                JaringanMitra::create([
                    'user_id' => $userBaru->id,
                    'sponsor_id' => $sponsorId,
                    'level' => 1,
                ]);

                // 2. Ambil semua upline dari sponsor langsung
                $uplines = JaringanMitra::where('user_id', $sponsorId)->get();

                // 3. Buat record untuk setiap upline dengan level bertambah +1
                foreach ($uplines as $upline) {
                    JaringanMitra::create([
                        'user_id' => $userBaru->id,
                        'sponsor_id' => $upline->sponsor_id,
                        'level' => $upline->level + 1,
                    ]);
                }
            });

            Log::info("Jaringan mitra berhasil dibuat untuk user ID: {$userBaru->id} dengan sponsor ID: {$sponsorId}");
        } catch (\Exception $e) {
            Log::error("Gagal membuat jaringan mitra untuk user ID: {$userBaru->id}", [
                'error' => $e->getMessage(),
                'sponsor_id' => $sponsorId,
            ]);

            // Re-throw exception agar bisa ditangkap oleh caller
            throw $e;
        }
    }
}
