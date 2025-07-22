<?php

namespace App\Filament\Admin\Resources\ApprovePembelianStockisResource\Pages;

use App\Filament\Admin\Resources\ApprovePembelianStockisResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditApprovePembelianStockis extends EditRecord
{
    protected static string $resource = ApprovePembelianStockisResource::class;

    protected static string $view = 'filament.user.pages.pembelian-produk-detail';

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('Set Proses')
                ->label('Diterima')
                ->color('info')
                ->visible(fn() => $this->record->status_pembelian === 'menunggu'
                    || $this->record->status_pembelian === 'ditolak')
                ->action(function () {
                    if (!in_array($this->record->status_pembelian, ['proses', 'selesai'])) {
                        event(new \App\Events\PembelianDiterima($this->record));
                    }
                    $this->record->status_pembelian = 'proses';
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Status diubah menjadi proses, stok ditambahkan ke pembeli dan dikurangi dari stockist.')
                        ->success()
                        ->send();
                    return redirect()->to($this->getResource()::getUrl('index'));
                }),
            Actions\Action::make('Set Ditolak')
                ->label('Ditolak')
                ->color('danger')
                ->visible(fn() => $this->record->status_pembelian !== 'ditolak')
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
            Actions\Action::make('Set Selesai')
                ->label('Selesai')
                ->color('success')
                ->visible(fn() => $this->record->status_pembelian === 'proses')
                ->action(function () {
                    $this->record->status_pembelian = 'selesai';
                    $this->record->save();
                    Notification::make()
                        ->title('Berhasil')
                        ->body('Status diubah menjadi selesai.')
                        ->success()
                        ->send();
                    return redirect()->to($this->getResource()::getUrl('index'));
                }),
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
        return [
            'pembelian' => $this->record,
            'stockis' => $this->record->seller ?? null,
            'isApprovePage' => true,
        ];
    }
}
