<?php

namespace App\Filament\User\Resources\AktivasiPinResource\Pages;

use App\Filament\User\Resources\AktivasiPinResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAktivasiPin extends EditRecord
{
    protected static string $resource = AktivasiPinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
