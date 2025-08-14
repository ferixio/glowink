<?php

namespace App\Listeners;

use App\Events\BonusGenerasi;
use App\Models\Aktivitas;
use App\Models\JaringanMitra;
use App\Models\PembelianBonus;
use App\Models\Penghasilan;
use App\Models\User;

class BonusGenerasiListener
{
    public function handle(BonusGenerasi $event)
    {
        $pembelian = $event->pembelian;
        $isMemberAktivasi = $event->isMemberAktivasi;
        $user = $pembelian->user; // assuming relation exists

        if ($isMemberAktivasi) {
            $this->handleMemberAktivasi($pembelian, $user);
        } else {
            $this->handleRewardBonusGenerasi($pembelian, $user);
        }
    }

    private function handleMemberAktivasi($pembelian, $user)
    {
        // Ambil semua upline dari jaringan mitra (maksimal 10 level)
        $uplines = JaringanMitra::where('user_id', $user->id)
            ->orderBy('level')
            ->limit(10)
            ->get();

        foreach ($uplines as $upline) {
            $sponsor = User::find($upline->sponsor_id);
            if (!$sponsor) {
                continue;
            }

            $statusQr = $sponsor->status_qr;
            $nominalBonus = $statusQr ? 1500 : 300;

            Penghasilan::create([
                'user_id' => $sponsor->id,
                'kategori_bonus' => 'Bonus Generasi',
                'status_qr' => $statusQr,
                'tgl_dapat_bonus' => now(),
                'keterangan' => "bonus generasi level {$upline->level}",
                'nominal_bonus' => $nominalBonus,
            ]);

            // Tambahkan nominalBonus ke saldo_penghasilan sponsor
            $sponsor->saldo_penghasilan += $nominalBonus;
            $sponsor->save();

            // Buat data aktivitas setelah berhasil menambah saldo
            Aktivitas::create([
                'user_id' => $sponsor->id,
                'judul' => 'Bonus Generasi Diterima',
                'keterangan' => "Menerima bonus generasi level {$upline->level} dari member {$user->nama}",
                'tipe' => 'plus',
                'status' => 'Berhasil',
                'nominal' => $nominalBonus,
            ]);

            // Buat data PembelianBonus untuk setiap upline
            $idMitra = $user->id_mitra ?? 'Unknown';
            $point = 3;

            $nominalPembelianBonus = $statusQr ? 1500 : 300;

            if ($statusQr) {
                // Jika status_qr true, buat keterangan mendapatkan bonus
                PembelianBonus::create([
                    'pembelian_id' => $pembelian->id,
                    'user_id' => $sponsor->id,
                    'keterangan' => "ID {$idMitra} mendapatkan {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
                ]);
            } else {
                // Jika status_qr false, buat keterangan kehilangan peluang
                PembelianBonus::create([
                    'pembelian_id' => $pembelian->id,
                    'user_id' => $sponsor->id,
                    'keterangan' => "ID {$idMitra} kehilangan peluang {$point} point dan BONUS GENERASI {$nominalPembelianBonus}",
                ]);
            }
        }
    }

    private function handleRewardBonusGenerasi($pembelian, $user)
    {
        // Ambil semua upline dari jaringan mitra (maksimal 10 level)
        $uplines = JaringanMitra::where('user_id', $user->id)
            ->orderBy('level')
            ->limit(10)
            ->get();

        foreach ($uplines as $upline) {
            $sponsor = User::find($upline->sponsor_id);
            if (!$sponsor || !$sponsor->status_qr) {
                continue;
            }

            foreach ($pembelian->details as $detail) {
                if ($detail->paket == 2) {
                    $sponsor->saldo_penghasilan += 1500;
                    $sponsor->poin_reward += 1;
                    $sponsor->save();

                    event(new \App\Events\ChangeLevelUser($sponsor, $sponsor->poin_reward));

                    Aktivitas::create([
                        'user_id' => $sponsor->id,
                        'judul' => 'Poin',
                        'keterangan' => "dari mitra {$user->nama}",
                        'tipe' => 'plus',
                        'status' => '',
                        'nominal' => 1,
                    ]);
                    Aktivitas::create([
                        'user_id' => $sponsor->id,
                        'judul' => 'Bonus Generasi',
                        'keterangan' => "",
                        'tipe' => 'plus',
                        'status' => '',
                        'nominal' => 1500,
                    ]);

                    break; // hanya tambah 1 poin jika ada minimal 1 paket == 2
                }
            }
        }
    }
}
