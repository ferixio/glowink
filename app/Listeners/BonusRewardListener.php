<?php

namespace App\Listeners;

use App\Events\BonusReward;
use App\Models\Aktivitas;
use App\Models\User;

class BonusRewardListener
{
    public function handle(BonusReward $event)
    {
        $pembelian = $event->pembelian;
        $user = $pembelian->user;

        $dataJumlahPoinYangDidapat = 0;
        // Cek user utama
        if ($user->status_qr) {
            foreach ($pembelian->details as $detail) {
                if ($detail->paket == 2) {
                    $oldPoin = $user->poin_reward;
                    $user->poin_reward += 1;
                    $dataJumlahPoinYangDidapat += 1; // hanya tambah 1 poin jika ada minimal 1 paket == 2

                    $user->save();
                    event(new \App\Events\ChangeLevelUser($user, $user->poin_reward));

                    break; // hanya tambah 1 poin jika ada minimal 1 paket == 2
                }
            }

            if ($dataJumlahPoinYangDidapat > 0) {
                Aktivitas::create([
                    'user_id' => $user->id,
                    'judul' => 'Poin',
                    'keterangan' => "",
                    'tipe' => 'plus',
                    'status' => '',
                    'nominal' => $dataJumlahPoinYangDidapat,
                ]);
            }
        }

        // Panggil event BonusGenerasi dengan isMemberAktivasi = false
        // untuk menangani bonus reward upline (kode yang sebelumnya ada di baris 46-78)
        event(new \App\Events\BonusGenerasi($pembelian, false));
    }
}
