<?php

namespace App\Filament\Admin\Resources\LaporanDevidenHarianResource\Pages;

use App\Filament\Admin\Resources\LaporanDevidenHarianResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLaporanDevidenHarian extends EditRecord
{
    protected static string $resource = LaporanDevidenHarianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
