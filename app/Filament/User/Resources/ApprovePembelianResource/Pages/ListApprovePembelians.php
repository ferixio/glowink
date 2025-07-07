<?php

namespace App\Filament\User\Resources\ApprovePembelianResource\Pages;

use App\Filament\User\Resources\ApprovePembelianResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListApprovePembelians extends ListRecords
{
    protected static string $resource = ApprovePembelianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
