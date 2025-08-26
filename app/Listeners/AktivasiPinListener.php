<?php

namespace App\Listeners;

use App\Events\AktivasiPin;
use App\Models\AktivasiPin as AktivasiPinModel;

class AktivasiPinListener
{
    public function handle(AktivasiPin $event)
    {
        $pembelian = $event->pembelian;

        foreach ($pembelian->details as $detail) {
            $jumlah = (int) ($detail->jml_beli ?? 0);

            for ($i = 0; $i < $jumlah; $i++) {
                $generatedRandomPin = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

                AktivasiPinModel::create([
                    'user_id' => $pembelian->user_id,
                    'pin' => $generatedRandomPin,
                    'pembelian_detail_id' => $detail->id,
                    'produk_id' => $detail->produk_id,
                    'is_accept' => false,
                ]);
            }
        }
    }
}
