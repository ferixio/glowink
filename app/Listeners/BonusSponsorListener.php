<?php

namespace App\Listeners;

use App\Events\BonusSponsor;

class BonusSponsorListener
{
    public function handle(BonusSponsor $event)
    {
        $pembelian = $event->pembelian;
        $user = $pembelian->user;
        $sponsor = \App\Models\User::find($user->id_sponsor);
        if ($sponsor) {
            $statusQr = $user->status_qr;
            $nominalBonus = 10000;
            \App\Models\Penghasilan::create([
                'user_id' => $sponsor->id,
                'kategori_bonus' => 'Bonus Sponsor',
                'status_qr' => $statusQr,
                'tgl_dapat_bonus' => now(),
                'keterangan' => 'bonus sponsor',
                'nominal_bonus' => $nominalBonus,
            ]);
            $sponsor->saldo_penghasilan += $nominalBonus;
            $sponsor->save();
        }
    }
}
