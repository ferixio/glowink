<?php

namespace App\Listeners;

use App\Events\BonusGenerasi;
use App\Events\BonusReward;
use App\Events\BonusSponsor;
use App\Events\PembelianDiterima;
use App\Events\SpillOverBonusBulanan;
use App\Models\Pembelian;

class ProsesPembelianDiterima
{
    public function handle(PembelianDiterima $event)
    {
        $pembelian = $event->pembelian;

        $user = \App\Models\User::find($pembelian->user_id);

        // ini fungsi untuk cek user jika masih belum qr dan membuatnya menjadi plan karir
        if ($user && $user->status_qr == 0) {
            $targetCategories = ['stock pribadi', 'repeat order', 'repeat order bulanan'];

            if (in_array($pembelian->kategori_pembelian, $targetCategories)) {
                foreach ($pembelian->details as $detail) {
                    if ($detail->paket == 2) {
                        // Update user's status_qr to 1
                        $user->status_qr = 1;
                        $user->save();

                        if ($user->id_sponsor) {
                            $sponsor = \App\Models\User::find($user->id_sponsor);
                            if ($sponsor) {
                                // Create income data for sponsor
                                \App\Models\Penghasilan::create([
                                    'user_id' => $sponsor->id,
                                    'kategori_bonus' => 'Bonus Sponsor',
                                    'status_qr' => $user->status_qr,
                                    'tgl_dapat_bonus' => now(),
                                    'keterangan' => 'bonus sponsor (Member update Quick Reward)',
                                    'nominal_bonus' => 20000,
                                ]);
                                // Create activity for sponsor
                                \App\Models\Aktivitas::create([
                                    'user_id' => $sponsor->id,
                                    'judul' => 'Bonus Sponsor Quick Reward',
                                    'keterangan' => "Menerima bonus sponsor QR dari #{$user->id_mitra}",
                                    'tipe' => null,
                                    'status' => 'Berhasil',
                                    'nominal' => 20000,
                                ]);

                                // Add to sponsor's income balance
                                // $sponsor->saldo_penghasilan += 20000;
                                // $sponsor->save();

                                // PembelianBonus::create([
                                //     'pembelian_id' => $pembelian->id,
                                //     'user_id' => $sponsor->id,
                                //     'keterangan' => 'bonus sponsor (Member update Quick Reward)',
                                // ]);
                            }
                        }

                        break; // Exit loop once we find a matching detail
                    }

                }
            }
        }

        // HAPUS LOGIKA CASHBACK DI SINI
        if (!in_array($pembelian->status_pembelian, ['selesai'])) {
            foreach ($pembelian->details as $detail) {
                // Tambah stok ke user pembeli
                $produkStok = \App\Models\ProdukStok::firstOrNew([
                    'user_id' => $pembelian->user_id,
                    'produk_id' => $detail->produk_id,
                ]);
                $produkStok->stok = ($produkStok->stok ?? 0) + $detail->jml_beli;
                $produkStok->save();

                // Kurangi stok dari stockist (jika beli dari stockist)
                if ($pembelian->beli_dari) {
                    $seller = \App\Models\User::find($pembelian->beli_dari);
                    if ($seller && !$seller->isAdmin) {
                        $stockistStok = \App\Models\ProdukStok::where('user_id', $pembelian->beli_dari)
                            ->where('produk_id', $detail->produk_id)
                            ->first();
                        if ($stockistStok && $stockistStok->stok >= $detail->jml_beli) {
                            $stockistStok->update([
                                'stok' => $stockistStok->stok - $detail->jml_beli,
                            ]);
                        }
                    }
                }
            }
        }
        // if ($pembelian->beli_dari) {
        //     $seller = \App\Models\User::find($pembelian->beli_dari);
        //     if ($seller && !$seller->isAdmin) {
        //         $seller->saldo_penghasilan += $pembelian->total_beli;
        //         $seller->save();
        //         \App\Models\Penghasilan::create([
        //             'user_id' => $seller->id,
        //             'kategori_bonus' => 'Pemasukan',
        //             'status_qr' => $seller->status_qr,
        //             'tgl_dapat_bonus' => now(),
        //             'keterangan' => 'penambahan saldo stockist',
        //             'nominal_bonus' => $pembelian->total_beli,
        //         ]);
        //         \App\Models\Aktivitas::create([
        //             'user_id' => $seller->id,
        //             'judul' => 'Penjualan Produk',
        //             'keterangan' => "Menerima pemasukan dari penjualan produk {$pembelian->kategori_pembelian} oleh #{$user->id_mitra}",
        //             'tipe' => 'plus',
        //             'status' => 'Berhasil',
        //             'nominal' => $pembelian->total_beli,
        //         ]);
        //     }
        // }
        if ($pembelian->kategori_pembelian == 'aktivasi member') {
            event(new BonusSponsor($pembelian));
            event(new BonusGenerasi($pembelian, true));

        }
        if ($pembelian->kategori_pembelian == 'repeat order' || $pembelian->kategori_pembelian == 'stock pribadi') {
            event(new BonusReward($pembelian));

        }
        if ($pembelian->kategori_pembelian == 'stock pribadi') {
            event(new \App\Events\AktivasiPin($pembelian));
        }

        // if($pembelian->kategori_pembelian == 'repeat order' && $pembelian->status_pembelian == 'selesai') {
        //    event(new BonusGenerasi($pembelian));

        // }

        if ($pembelian->kategori_pembelian == 'repeat order bulanan') {
            $user = \App\Models\User::find($pembelian->user_id);
            $user->jml_ro_bulanan = $user->jml_ro_bulanan + 1;
            $user->save();

            event(new SpillOverBonusBulanan($user->id, $pembelian->id, $pembelian->kategori_pembelian, $pembelian));

        }
    }
}
