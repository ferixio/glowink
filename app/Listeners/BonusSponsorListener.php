<?php

namespace App\Listeners;

use App\Events\BonusSponsor;
use App\Models\PembelianBonus;

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
            \App\Models\Aktivitas::create([
                'user_id' => $sponsor->id,
                'judul' => 'Bonus Sponsor Diterima',
                'keterangan' => "Menerima bonus sponsor dari mitra #{$user->id_mitra}",
                'tipe' => 'plus',
                'status' => 'Berhasil',
                'nominal' => $nominalBonus,
            ]);
            PembelianBonus::create([
                'pembelian_id' => $pembelian->id,
                'user_id' => $sponsor->id,
                'keterangan' => "ID {$sponsor->id_mitra} Menerima bonus sponsor " . ('Rp. ' . number_format((float) $nominalBonus, 0, ',', '.')) . " dari mitra #{$user->id_mitra} ",
                'tipe' => 'bonus',
                'created_at' => now(),
                'updated_at' => now(),

            ]);
            $sponsor->saldo_penghasilan += $nominalBonus;
            $sponsor->save();
            // Tambahan: jika statusQr true, cek detail pembelian untuk paket == 2
            if ($statusQr) {
                foreach ($pembelian->details as $detail) {
                    if ($detail->paket == 2) {
                        \App\Models\Penghasilan::create([
                            'user_id' => $sponsor->id,
                            'kategori_bonus' => 'Bonus Sponsor',
                            'status_qr' => $statusQr,
                            'tgl_dapat_bonus' => now(),
                            'keterangan' => 'bonus sponsor (paket Quick Reward)',
                            'nominal_bonus' => 20000,
                        ]);
                        \App\Models\Aktivitas::create([
                            'user_id' => $sponsor->id,
                            'judul' => 'Bonus Sponsor Quick Reward',
                            'keterangan' => "Menerima bonus sponsor QR dari {$user->id_mitra}",
                            'tipe' => 'plus',
                            'status' => 'Berhasil',
                            'nominal' => 20000,
                        ]);
                        PembelianBonus::create([
                            'pembelian_id' => $pembelian->id,
                            'user_id' => $sponsor->id,
                            'keterangan' => "ID {$sponsor->id_mitra} Menerima bonus sponsor QR dari Rp. 20.000 dari mitra #{$user->id_mitra} ",
                            'tipe' => 'bonus',
                            'created_at' => now(),
                            'updated_at' => now(),

                        ]);

                        $sponsor->saldo_penghasilan += 20000;
                        $sponsor->save();
                        break;
                    }
                }
            }
        }
    }
}
