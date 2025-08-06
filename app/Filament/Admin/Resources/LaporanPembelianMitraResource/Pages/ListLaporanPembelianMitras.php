<?php

namespace App\Filament\Admin\Resources\LaporanPembelianMitraResource\Pages;

use App\Filament\Admin\Resources\LaporanPembelianMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLaporanPembelianMitras extends ListRecords
{
    protected static string $resource = LaporanPembelianMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
