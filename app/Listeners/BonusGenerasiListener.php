<?php

namespace App\Listeners;

use App\Events\BonusGenerasi;

class BonusGenerasiListener
{
    public function handle(BonusGenerasi $event)
    {
        $pembelian = $event->pembelian;
        $user = $pembelian->user; // assuming relation exists
        $groupSponsor = $user->group_sponsor ?? [];
        if (!is_array($groupSponsor)) {
            $groupSponsor = json_decode($groupSponsor, true) ?? [];
        }
        $groupSponsor = array_slice($groupSponsor, 0, 10); // Batasi maksimal 10 id
        foreach ($groupSponsor as $sponsorId) {
            $sponsor = \App\Models\User::find($sponsorId);
            if (!$sponsor) {
                continue;
            }

            $statusQr = $sponsor->status_qr;
            $nominalBonus = $statusQr ? 1500 : 300;
            \App\Models\Penghasilan::create([
                'user_id' => $sponsor->id,
                'kategori_bonus' => 'Bonus Generasi',
                'status_qr' => $statusQr,
                'tgl_dapat_bonus' => now(),
                'keterangan' => 'bonus generasi',
                'nominal_bonus' => $nominalBonus,
            ]);
            // Tambahkan nominalBonus ke saldo_penghasilan sponsor
            $sponsor->saldo_penghasilan += $nominalBonus;
            $sponsor->save();
            // Buat juga data penghasilan untuk penambahan saldo
            \App\Models\Penghasilan::create([
                'user_id' => $sponsor->id,
                'kategori_bonus' => 'Bonus Generasi',
                'status_qr' => $statusQr,
                'tgl_dapat_bonus' => now(),
                'keterangan' => 'penambahan saldo bonus generasi',
                'nominal_bonus' => $nominalBonus,
            ]);
        }
    }
}
