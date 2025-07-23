<?php

namespace App\Listeners;

use App\Events\BonusReward;

class BonusRewardListener
{
    public function handle(BonusReward $event)
    {
        $pembelian = $event->pembelian;
        $user = $pembelian->user;

        // Cek user utama
        if ($user->status_qr) {
            foreach ($pembelian->details as $detail) {
                if ($detail->paket == 2) {
                    $user->poin_reward += 1;
                    $user->save();
                    event(new \App\Events\ChangeLevelUser($user, $user->poin_reward));
                    break; // hanya tambah 1 poin jika ada minimal 1 paket == 2
                }
            }
        }

        // Cek semua user di group_sponsor
        $groupSponsor = $user->group_sponsor ?? [];
        if (!is_array($groupSponsor)) {
            $groupSponsor = json_decode($groupSponsor, true) ?? [];
        }
        foreach ($groupSponsor as $sponsorId) {
            $sponsor = \App\Models\User::find($sponsorId);
            if ($sponsor && $sponsor->status_qr) {
                foreach ($pembelian->details as $detail) {
                    if ($detail->paket == 2) {
                        $sponsor->poin_reward += 1;
                        $sponsor->save();
                        event(new \App\Events\ChangeLevelUser($sponsor, $sponsor->poin_reward));
                        break; // hanya tambah 1 poin jika ada minimal 1 paket == 2
                    }
                }
            }
        }
    }
}
