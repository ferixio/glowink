<?php

namespace App\Filament\Admin\Resources\LevelKarirResource\Pages;

use App\Filament\Admin\Resources\LevelKarirResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditLevelKarir extends EditRecord
{
    protected static string $resource = LevelKarirResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }
}
