<?php

namespace App\Filament\User\Resources\PembelianDetailResource\Pages;

use App\Filament\User\Resources\PembelianDetailResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembelianDetails extends ListRecords
{
    protected static string $resource = PembelianDetailResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
