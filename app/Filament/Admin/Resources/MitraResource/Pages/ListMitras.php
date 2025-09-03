<?php

namespace App\Filament\Admin\Resources\MitraResource\Pages;

use App\Filament\Admin\Resources\MitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMitras extends ListRecords
{
    protected static string $resource = MitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
