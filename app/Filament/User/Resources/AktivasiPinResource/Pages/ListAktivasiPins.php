<?php

namespace App\Filament\User\Resources\AktivasiPinResource\Pages;

use App\Filament\User\Resources\AktivasiPinResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAktivasiPins extends ListRecords
{
    protected static string $resource = AktivasiPinResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
