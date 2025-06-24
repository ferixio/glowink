<?php

namespace App\Filament\Admin\Resources\UserMitraResource\Pages;

use App\Filament\Admin\Resources\UserMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUserMitras extends ListRecords
{
    protected static string $resource = UserMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
