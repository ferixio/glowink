<?php

namespace App\Filament\Admin\Resources\LaporanPembelianMitraResource\Pages;

use App\Filament\Admin\Resources\LaporanPembelianMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanPembelianMitra extends EditRecord
{
    protected static string $resource = LaporanPembelianMitraResource::class;
    protected static string $view = 'filament.user.pages.pembelian-produk-detail';

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Actions\DeleteAction::make(),
    //     ];
    // }

    

   

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
            'userBaru' => $this->record->user, // User yang dibuat dari aktivasi member
        ];
    }
}
