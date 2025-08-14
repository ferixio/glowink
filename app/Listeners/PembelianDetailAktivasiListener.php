<?php

namespace App\Listeners;

use App\Events\PembelianDetailAktivasi;
use App\Models\Penghasilan;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class PembelianDetailAktivasiListener implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PembelianDetailAktivasi $event): void
    {
        $pembelianDetail = $event->pembelianDetail;
        $user = $event->user;

        // Cek apakah pembelianDetail sudah is_accepted, jika sudah maka jangan lanjutkan proses
        if ($pembelianDetail->is_accepted) {
            // Notifikasi bahwa pembelian detail sudah accepted
            \Filament\Notifications\Notification::make()
                ->title('PIN sudah diterima sebelumnya')
                ->body('PIN dengan ID ' . $pembelianDetail->id . ' sudah diterima dan tidak dapat diproses lagi.')
                ->warning()
                ->send();

            return; // Skip jika sudah accepted
        }

        // Tentukan user penerima bonus dengan fallback berurutan
        $bonusUser = $user
        ?: ($pembelianDetail->user
            ?: ($pembelianDetail->pembelian?->user
                ?: ($pembelianDetail->pembelian?->sponsor
                    ?: $pembelianDetail->pembelian?->seller)));

        if (!$bonusUser) {
            return; // Skip jika tidak ada user yang menerima bonus
        }

        // Ambil kategori_pembelian dari relasi pembelian
        $kategoriPembelian = $pembelianDetail->pembelian?->kategori_pembelian ?? '-';

        // Hitung total bonus yang akan diterima
        $totalBonus = 0;
        $detailBonus = '';

        // Bonus Sponsor
        if ($pembelianDetail->nominal_bonus_sponsor > 0) {
            $totalBonus += $pembelianDetail->nominal_bonus_sponsor;
            $detailBonus .= 'Bonus Sponsor: ' . number_format($pembelianDetail->nominal_bonus_sponsor, 2) . ', ';
        }

        // Bonus Generasi
        if ($pembelianDetail->nominal_bonus_generasi > 0) {
            $totalBonus += $pembelianDetail->nominal_bonus_generasi;
            $detailBonus .= 'Bonus Generasi: ' . number_format($pembelianDetail->nominal_bonus_generasi, 2) . ', ';
        }

        // Cashback
        if ($pembelianDetail->cashback > 0) {
            $totalBonus += $pembelianDetail->cashback;
            $detailBonus .= 'Cashback: ' . number_format($pembelianDetail->cashback, 2) . ', ';
        }

        // Hapus koma terakhir
        $detailBonus = rtrim($detailBonus, ', ');

        // Gabungkan kategori_pembelian dan detail bonus
        $keterangan = $kategoriPembelian;
        if ($detailBonus) {
            $keterangan .= ' | ' . $detailBonus;
        }

        if ($totalBonus > 0) {
            // Buat record Penghasilan
            Penghasilan::create([
                'user_id' => $bonusUser->id,
                'kategori_bonus' => 'Pemasukan',
                'status_qr' => 'Aktivasi PIN',
                'tgl_dapat_bonus' => now()->toDateString(),
                'keterangan' => $keterangan,
                'nominal_bonus' => $totalBonus,
            ]);
            // Buat aktivitas untuk user
            \App\Models\Aktivitas::create([
                'user_id' => $bonusUser->id,
                'judul' => 'Aktivasi PIN',
                'keterangan' => "Menerima bonus aktivasi PIN ",
                'tipe' => 'plus',
                'status' => 'success',
                'nominal' => $totalBonus,
            ]);

            // Update saldo_penghasilan user
            $bonusUser->increment('saldo_penghasilan', $totalBonus);
        }

        \Filament\Notifications\Notification::make()
            ->title('Berhasil menerima PIN ' . $pembelianDetail->id)
            ->success()
            ->send();

    }
}
