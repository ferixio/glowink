<?php

namespace App\Listeners;

use App\Events\BonusGenerasi;
use App\Events\BonusReward;
use App\Events\BonusSponsor;
use App\Events\PembelianDiterima;

class ProsesPembelianDiterima
{
    public function handle(PembelianDiterima $event)
    {
        $pembelian = $event->pembelian;
        // HAPUS LOGIKA CASHBACK DI SINI
        if (!in_array($pembelian->status_pembelian, ['proses', 'selesai'])) {
            foreach ($pembelian->details as $detail) {
                // Tambah stok ke user pembeli
                $produkStok = \App\Models\ProdukStok::firstOrNew([
                    'user_id' => $pembelian->user_id,
                    'produk_id' => $detail->produk_id,
                ]);
                $produkStok->stok = ($produkStok->stok ?? 0) + $detail->jml_beli;
                $produkStok->save();

                // Kurangi stok dari stockist (jika beli dari stockist)
                if ($pembelian->beli_dari && $pembelian->beli_dari != 1) {
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
        if ($pembelian->beli_dari && $pembelian->beli_dari != 1) {
            $stockist = \App\Models\User::find($pembelian->beli_dari);
            if ($stockist) {
                $stockist->saldo_penghasilan += $pembelian->total_beli;
                $stockist->save();
                \App\Models\Penghasilan::create([
                    'user_id' => $stockist->id,
                    'kategori_bonus' => 'Pemasukan',
                    'status_qr' => $stockist->status_qr,
                    'tgl_dapat_bonus' => now(),
                    'keterangan' => 'penambahan saldo stockist',
                    'nominal_bonus' => $pembelian->total_beli,
                ]);
            }
        }
        if ($pembelian->kategori_pembelian == 'aktivasi member') {
            event(new BonusSponsor($pembelian));
            event(new BonusGenerasi($pembelian));

        }
        if ($pembelian->kategori_pembelian == 'repeat order' || $pembelian->kategori_pembelian == 'stock pribadi') {
            event(new BonusReward($pembelian));
        }
    }
}
