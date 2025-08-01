<?php

namespace App\Filament\Admin\Resources\LevelKarirResource\Pages;

use App\Filament\Admin\Resources\LevelKarirResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListLevelKarirs extends ListRecords
{
    protected static string $resource = LevelKarirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }
}
