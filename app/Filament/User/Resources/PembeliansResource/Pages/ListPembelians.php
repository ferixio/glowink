<?php

namespace App\Filament\User\Resources\PembeliansResource\Pages;

use App\Filament\User\Resources\PembeliansResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembelians extends ListRecords
{
    protected static string $resource = PembeliansResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
