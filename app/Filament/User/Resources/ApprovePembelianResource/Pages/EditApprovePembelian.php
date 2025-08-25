<?php

namespace App\Filament\User\Resources\ApprovePembelianResource\Pages;

use App\Events\SpillOverBonusBulanan;
use App\Filament\User\Resources\ApprovePembelianResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditApprovePembelian extends EditRecord
{
    protected static string $resource = ApprovePembelianResource::class;
    protected static string $view = 'filament.user.pages.pembelian-produk-detail';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\Action::make('Set Proses')
            //     ->label('Proses')
            //     ->color('info')
            //     ->visible(fn() => $this->record->status_pembelian === 'menunggu'
            //         || $this->record->status_pembelian === 'ditolak')
            //     ->action(function () {
            //         $this->record->status_pembelian = 'proses';
            //         $this->record->save();
            //         Notification::make()
            //             ->title('Berhasil')
            //             ->body('Status diubah menjadi proses')
            //             ->success()
            //             ->send();
            //         // return redirect()->to($this->getResource()::getUrl('index'));
            //     }),

            Actions\Action::make('Set Selesai')
                ->label('Selesai')
                ->color('success')
                ->visible(fn() => $this->record->status_pembelian === 'menunggu')
                ->action(function () {
                    $pembelianDetails = \App\Models\PembelianDetail::where('pembelian_id', $this->record->id)->get();

                    $pembelianDetails->each(function ($item) {
                        $generatedRandomPin = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

                        $item->pin = $generatedRandomPin;

                        $item->save();
                    });
                    event(new \App\Events\PembelianDiterima($this->record));



                    $this->record->status_pembelian = 'selesai';
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Status diubah menjadi selesai, PIN telah dibuat untuk setiap detail pembelian.')
                        ->success()
                        ->send();
                    // return redirect()->to($this->getResource()::getUrl('index'));
                }),
            Actions\Action::make('Set Ditolak')
                ->label('Ditolak')
                ->color('danger')
                ->visible(fn() => $this->record->status_pembelian !== 'ditolak' && $this->record->status_pembelian !== 'selesai')
                ->action(function () {
                    $this->record->status_pembelian = 'ditolak';
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Status diubah menjadi ditolak.')
                        ->success()
                        ->send();
                    return redirect()->to($this->getResource()::getUrl('index'));
                }),

            //             Actions\Action::make('Set Proses')

            // ->label('Diterima')
            // ->color('info')
            // ->visible(fn() => $this->record->status_pembelian === 'menunggu'
            //     || $this->record->status_pembelian === 'ditolak')
            // ->action(function () {
            //     if (!in_array($this->record->status_pembelian, ['proses', 'selesai'])) {
            //         event(new \App\Events\PembelianDiterima($this->record));
            //     }
            //     $this->record->status_pembelian = 'proses';
            //     $this->record->save();
            //     Notification::make()
            //         ->title('Berhasil')
            //         ->body('Status diubah menjadi proses, stok ditambahkan ke pembeli dan dikurangi dari stockist.')
            //         ->success()
            //         ->send();
            //     // return redirect()->to($this->getResource()::getUrl('index'));
            // }),

            //            Actions\Action::make('Set Selesai')
            // ->label('Selesai')
            // ->color('success')
            // ->visible(fn() => $this->record->status_pembelian === 'proses')
            // ->action(function () {
            //     $this->record->status_pembelian = 'selesai';
            //     $this->record->save();
            //     Notification::make()
            //         ->title('Berhasil')
            //         ->body('Status diubah menjadi selesai.')
            //         ->success()
            //         ->send();
            //     return redirect()->to($this->getResource()::getUrl('index'));
            // }),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getFormActions(): array
    {
        return [

        ];
    }

    protected function getViewData(): array
    {
        $pembelianBonuses = \App\Models\PembelianBonus::where('pembelian_id', $this->record->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return [
            'pembelian' => $this->record,
            'stockis' => $this->record->seller ?? null,
            'isApprovePage' => true,
            'userBaru' => $this->record->user, // User yang dibuat dari aktivasi member
            'pembelianBonuses' => $pembelianBonuses, // Add pembelian bonuses data
        ];
    }

}
