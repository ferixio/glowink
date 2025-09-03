<?php

namespace App\Listeners;

use App\Events\ChangeLevelUser;

class ChangeLevelUserListener
{
    /**
     * Handle the event.
     *
     * @param  \App\Events\ChangeLevelUser  $event
     * @return void
     */
    public function handle(ChangeLevelUser $event)
    {
        $user = $event->user;
        $poin = $event->newLevel;

        // Daftar level dan ketentuannya (urutan dari terendah ke tertinggi)
        $levels = [
            'bronze' => ['poin' => 20, 'bonus' => 100000],
            'silver' => ['poin' => 100, 'bonus' => 400000],
            'gold' => ['poin' => 750, 'bonus' => 2500000],
            'platinum' => ['poin' => 3000, 'bonus' => 10000000],
            'titanium' => ['poin' => 15000, 'bonus' => 50000000],
            'ambassador' => ['poin' => 60000, 'bonus' => 200000000],
            'chairman' => ['poin' => 150000, 'bonus' => 500000000],
        ];

        // Tentukan level sebelumnya (jika null, dianggap belum punya level)
        $currentLevel = $user->plan_karir_sekarang;
        $lastLevelIndex = -1;
        if ($currentLevel !== null) {
            $levelNames = array_keys($levels);
            $lastLevelIndex = array_search($currentLevel, $levelNames);
        }

        $levelNames = array_keys($levels);
        $newLevelIndex = $lastLevelIndex;
        // Cek level tertinggi yang dicapai sekarang
        foreach ($levels as $i => $data) {
            if ($poin >= $data['poin']) {
                $newLevelIndex = array_search($i, $levelNames);
            }
        }

        // Jika naik level satu atau lebih
        if ($newLevelIndex > $lastLevelIndex) {
            // Berikan bonus untuk setiap level yang terlewati
            for ($i = $lastLevelIndex + 1; $i <= $newLevelIndex; $i++) {
                $level = $levelNames[$i];
                $bonus = $levels[$level]['bonus'];
                $user->saldo_penghasilan += $bonus;
            }
            // Update level sekarang
            $user->plan_karir_sekarang = $levelNames[$newLevelIndex];
            $user->save();
        }
    }
}
